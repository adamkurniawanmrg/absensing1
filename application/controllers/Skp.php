<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skp extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Skpd_model','Skp_model','Upacara_model']);
		is_logged_in();
    }

    function get_data_skp()
    {
        extract($_POST);
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");

        $akses = [1,2];
        if(in_array($this->session->userdata('role_id'), $akses) && $skpd_id){
            $this->db->where("tb_skp.skpd_id", $skpd_id);
        }
        if(!in_array($this->session->userdata('role_id'), $akses)){
            $this->db->where("tb_skp.skpd_id", $this->session->userdata('skpd_id'));
        }
        $this->db->where("DATE_FORMAT(tb_skp.bulan,'%Y-%m-%d')>=", $start_date);
        $this->db->where("DATE_FORMAT(tb_skp.bulan,'%Y-%m-%d')<=", $end_date);
        $this->db->order_by('tb_skp.id', 'desc');

        $list        = $this->Skp_model->getAllSkp();
        $skpds       = $this->Skpd_model->getSkpd(true);

        $data = array();
        $no = 1;
        $confirm = "Anda yakin hapus ?";
        foreach ($list as $field) {
            $skpd   = isset($skpds[$field['skpd_id']]) ? $skpds[$field['skpd_id']] : array(['nama_skpd'=>'undefined']);

            $row = array();
            $row[] = $no;
            $row[] = date("F Y", strtotime($field['bulan']));
            $row[] = $skpd['nama_skpd'];
            $row[] = $field['jumlah'];
            $row[] = '
                <a href="' . site_url() . 'skp/editskp/' . $field['id'] . '?token=' . $_GET['token'] .  '" class="btn btn-success btn-sm"><i class="fa fa-edit" title="Edit"></i></a>
                <a href="' . site_url() . 'skp/deleteskp/' .  $field['id'] . '?token=' . $_GET['token'] . '" onclick="return confirm(\'Yakin hapus data?\')"  class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></a>';
            $no++;


            $data[] = $row;
        }


        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }


    public function getskppegawaibyskpd()
    {
        extract($_POST);
        $pegawai = $this->Pegawai_model->getPegawai(null, $skpd_id);
        $pegawai_tks = $this->Pegawai_model->getPegawaiTks(null, $skpd_id);
        
        $no = 1;
        $datapegawai = 0;
        array_multisort(array_column($pegawai, 'nama'), SORT_ASC, $pegawai);
        array_multisort(array_column($pegawai_tks, 'nama'), SORT_ASC, $pegawai_tks);
        
        $bulan = date("Y-m", strtotime($bulan));
        
        $pgws = array();
        foreach ($pegawai as $pg) {
            $skp = $this->db
                        ->select('tb_skp_meta.*, tb_skp.bulan')
                        ->where('tb_skp_meta.skp_id', $skp_id)
                        ->where('tb_skp_meta.pegawai_id', $pg['user_id'])
                        ->where('tb_skp_meta.jenis_pegawai', 'pegawai')
                        ->where("DATE_FORMAT(tb_skp.bulan,'%Y-%m')", $bulan)
                        ->join('tb_skp', 'tb_skp.id=tb_skp_meta.skp_id', 'left')
                        ->get('tb_skp_meta')
                        ->row_array();

            $pgw[0] = $no;
            $pgw[1] = ($pg['gelar_depan'] && $pg['gelar_depan']!="" ? $pg['gelar_depan'].". " : null).$pg['nama'].($pg['gelar_belakang'] && $pg['gelar_belakang']!="" ? ", ".$pg['gelar_belakang'] : null);
            $pgw[2] = $pg['username'];
            $pgw[3] = "<input type='number' name='nilai[" . $pg['user_id'] . "]' value='".(isset($skp['nilai']) ? $skp['nilai']: null)."' /> ";
            array_push($pgws, $pgw);
            $no++;
        }
        foreach ($pegawai_tks as $tks) {
            $skp = $this->db
                        ->select('tb_skp_meta.*, tb_skp.bulan')
                        ->where('tb_skp_meta.skp_id', $skp_id)
                        ->where('tb_skp_meta.pegawai_id', $tks['user_id'])
                        ->where('tb_skp_meta.jenis_pegawai', 'tks')
                        ->where("DATE_FORMAT(tb_skp.bulan,'%Y-%m')", $bulan)
                        ->join('tb_skp', 'tb_skp.id=tb_skp_meta.skp_id', 'left')
                        ->get('tb_skp_meta')
                        ->row_array();

            $pgw[0] = $no;
            $pgw[1] = $tks['nama'];
            $pgw[2] = $tks['username'];
            $pgw[3] = "<input type='number' name='nilaitks[" . $tks['user_id'] . "]' value='".(isset($skp['nilai']) ? $skp['nilai']: null)."' /> ";
            array_push($pgws, $pgw);
            $no++;
        }
        $data["data"] = $pgws;
        $data = json_encode($data);
        echo $data;
    }
    public function index($bulanTahun=false)
    {
        $data = [
			"page"				=> "skp/data_skp",
			"title"             => "Data SKP",
            "skpds"             => $this->Skpd_model->getSkpd(true),
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		$this->load->view('template/default', $data);   
    }

    public function cekskpexists()
    {
        if (!empty($_POST)) {
            extract($_POST);
            $bulan = date("Y-m", strtotime("1-".$bulan));
            $getSKP = $this->db
                            ->where('skpd_id', $skpd_id)
                            ->where("DATE_FORMAT(tb_skp.bulan,'%Y-%m')", $bulan)
                            ->get('tb_skp')
                            ->num_rows();
            if ($getSKP > 0) {
                echo json_encode(true);
                return;
            }
        }
        echo json_encode(false);
        return;
    }

    public function addskp()
    {
        $data = [
			"page"				=> "skp/add_skp",
			"title"             => "Buat SKP",
            "skpds"             => $this->Skpd_model->getSkpd(),
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
        
        $akses = [1,2];
        $is_akses = in_array($this->session->userdata('role_id'), $akses);
        if($is_akses){
            $this->form_validation->set_rules('skpd_id', 'Nama OPD', 'required');
        }
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');

        if ($this->form_validation->run() == false) {
	    	$this->load->view('template/default', $data);   
        } else {
            $skpd_id = $this->input->post('skpd_id');
            $data = [
                "skpd_id"       => $is_akses ? $this->input->post('skpd_id', true) : $this->session->userdata('skpd_id'),
                "bulan"         => date("Y-m-d", strtotime("01-".$this->input->post('bulan', true))),
                "jumlah"        => 0,
                "created_by"    => $this->session->userdata('user_id'),
            ];
            $this->db->insert('tb_skp', $data);
            $id_skp = $this->db->insert_id();

            $jumlah = 0;
            foreach($_POST['nilai'] as $pegawai_id=>$nilai){
                if($nilai>0) $jumlah++;
                $this->db->insert('tb_skp_meta', [
                    "skp_id"        => $id_skp,
                    "pegawai_id"    => $pegawai_id,
                    "jenis_pegawai" => 'pegawai',
                    "nilai"         => $nilai,
                ]);
            }
            foreach($_POST['nilaitks'] as $pegawai_id=>$nilai){
                if($nilai>0) $jumlah++;
                $this->db->insert('tb_skp_meta', [
                    "skp_id"        => $id_skp,
                    "pegawai_id"    => $pegawai_id,
                    "jenis_pegawai" => 'tks',
                    "nilai"         => $nilai,
                ]);
            }

            $this->db
                 ->where('id', $id_skp)
                 ->update('tb_skp', ["jumlah" => $jumlah]);

            $this->session->set_flashdata('pesan', '
            <div class="alert alert-success" role="alert">
            SKP telah ditambahkan.</div>
            ');
            redirect('skp?token=' . $_GET['token']);
        }
    }

    public function editskp($id)
    {

        if (isset($_POST['nilai']) && isset($_POST['nilaitks'])) {
            $jumlah = 0;
            foreach($_POST['nilai'] as $pegawai_id=>$nilai){
                if($nilai>0) $jumlah++;
                $this->db
                ->where('skp_id', $id)
                ->where('pegawai_id', $pegawai_id)
                ->where('jenis_pegawai', 'pegawai')
                ->update('tb_skp_meta', [
                    "nilai"         => $nilai,
                ]);
            }
            foreach($_POST['nilaitks'] as $pegawai_id=>$nilai){
                if($nilai>0) $jumlah++;
                $this->db
                ->where('skp_id', $id)
                ->where('pegawai_id', $pegawai_id)
                ->where('jenis_pegawai', 'tks')
                ->update('tb_skp_meta', [
                    "nilai"         => $nilai,
                ]);
            }

            $this->db
                 ->where('id', $id)
                 ->update('tb_skp', ["jumlah" => $jumlah]);

            $this->session->set_flashdata('pesan', '
            <div class="alert alert-success" role="alert">
            SKP telah diubah.</div>
            ');
            redirect('skp?token=' . $_GET['token']);
            return;
        }
        $data = [
			"page"				=> "skp/edit_skp",
			"title"             => "Ubah SKP",
            "skpds"             => $this->Skpd_model->getSkpd(),
            "skp"               => $this->Skp_model->getSkpById($id),
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
	    $this->load->view('template/default', $data);   

    }
    public function deleteskp($id)
    {

        $this->Skp_model->deleteSkp($id);
        $this->session->set_flashdata('pesan', '
        <div class="alert alert-success" role="alert">
        SKP Berhasil dihapus</div>
        ');
        redirect('skp?token=' . $_GET['token']);
    }


    
}
