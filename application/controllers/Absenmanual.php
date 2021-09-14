<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absenmanual extends CI_Controller
{

    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model([
            'LogAbsen_model',
            'Pegawai_model',
            'Skpd_model',
            'AbsenManual_model'
        ]);
    }

    function getDataAbsenManual(){
        $akses = [1,2,3]; 
        $akses2 = [1,2]; 
        $hak_akses = in_array($this->session->userdata('role_id'), $akses);
        $hak_akses2= in_array($this->session->userdata('role_id'), $akses2);

        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");


        $list = $this->db->
                        where("tb_absensi.pegawai_id", $this->session->userdata('user_id'))->
                        where("tb_absensi.jenis_pegawai", $this->session->userdata('jenis_pegawai'))->
                        where("tb_absensi.keterangan!=", null)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
                        order_by('tb_absensi.jam', 'desc')->
                        get('tb_absensi')->result_array();
        
        $skpds      = $this->Skpd_model->getSkpd(true);
        $pegawais   = $this->Pegawai_model->getPegawai();
        $tkss       = $this->Pegawai_model->getPegawaiTks();
        $data = array();
        $no = 1;
        foreach ($list as $field) {
            $skpd   = isset($skpds[$field['skpd_id']]) ? $skpds[$field['skpd_id']] : array(['nama_skpd'=>'undefined']);
            
            $indexPegawai   = array_search($field['pegawai_id'], array_column($pegawais, 'user_id'));
            $indexTks       = array_search($field['pegawai_id'], array_column($tkss, 'user_id'));
            $indexAprover   = $field['approved_by'] ? array_search($field['approved_by'], array_column($pegawais, 'user_id')) : null;
            

            $pegawai        = $field['jenis_pegawai']=='pegawai' ? 
                              (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                              (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
            
            $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];
    
            $row = array();

            $row[0] = $this->hari[date("w", strtotime($field['jam']))] . ", " . date('d F Y - H:i', strtotime($field['jam']));

            $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
            $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

            $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
            $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
            $totimeDisetujuiPada    = strtotime($field['approved_at']);
            $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";
            
            $nama = $gelarDepan.$pegawai['nama'].$gelarBelakang;
            $jenis_absen = $field['jenis_absen']."<br><small>".$field['keterangan']."</small>";

            $status = $field['status']==1 ? 
                            'Disetujui Oleh '.(isset($aprover['nama']) ? '<strong>'.$aproverGelarDepan.$aprover['nama'].$aproverGelarBelakang.'</strong><br><small class="text-info"><i>'.$disetujuiPada.'</i></small>' : 'undefined')  : 
                      ($field['status']==null ? 
                            '<span class="btn-warning" style="padding: 2px 7px; border-radius: 6px;">Menunggu</span>' : 
                            '<span class="btn-danger" style="padding: 2px 7px; border-radius: 6px;">Ditolak</span>'); 

            $row[1] = "<div class='row'><div class='col-md-4'>".$nama."</div>
                      <div class='col-md-4'>".$jenis_absen."</div>
                      <div class='col-md-4'>".$status."</div></div>";

            $no++;
            $data[] = $row;

        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }

    public function index(){
        $start_date     = isset($_GET['bulan']) ? date("Y-m-01", strtotime("01-".$_GET['bulan']))   : date("Y-m-01");
        $end_date       = isset($_GET['bulan']) ? date("Y-m-t", strtotime("01-".$_GET['bulan']))    : date("Y-m-t");

        $absenManualMenunggu = $this->db->
                        where("tb_absensi.pegawai_id", $this->session->userdata('user_id'))->
                        where("tb_absensi.jenis_pegawai", $this->session->userdata('jenis_pegawai'))->
                        where("tb_absensi.keterangan!=", null)->
                        where("tb_absensi.status", null)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
                        order_by('tb_absensi.jam', 'desc')->
                        get('tb_absensi')->result_array();
        $absenManual = $this->db->
                        where("tb_absensi.pegawai_id", $this->session->userdata('user_id'))->
                        where("tb_absensi.jenis_pegawai", $this->session->userdata('jenis_pegawai'))->
                        where("tb_absensi.keterangan!=", null)->
                        where("tb_absensi.status!=", null)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
                        order_by('tb_absensi.jam', 'desc')->
                        get('tb_absensi')->result_array();

        
		$data = [
		    "title"             => "Absensi Manual",
			"page"				=> "absenmanual/absenmanual",
			"skpd"              => $this->Skpd_model->getSkpd(),
            "absenManualMenunggu"=> $absenManualMenunggu,
            "absenManual"       => $absenManual,
            "skpds"             => $this->Skpd_model->getSkpd(true),
            "pegawais"          => $this->Pegawai_model->getPegawai(),
            "tkss"              => $this->Pegawai_model->getPegawaiTks(),
			"javascript"		=> [
				base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		
		$this->load->view('template/default', $data);

    }



    public function cekAbsenManual()
    {

        $akses = [1,2,3]; 
        $hak_akses = in_array($this->session->userdata('role_id'), $akses);

        $pegawai_id      = $hak_akses ? $_POST['pegawai_id']    : $this->session->userdata('user_id');
        $jenis_pegawai   = $hak_akses ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');
        $skpd_id         = $hak_akses ? $_POST['skpd_id']       : $this->session->userdata('skpd_id');

        if (isset($_POST['tanggal'])) {
            $d          = $this->db->
                                 where('tanggal', date("Y-m-d", strtotime($_POST['tanggal'])))->
                                 where('pegawai_id', $pegawai_id)->
                                 where('jenis_pegawai', $jenis_pegawai)->
                                 where('skpd_id', $skpd_id)->
                                 group_start()->
                                     where('jenis_absen', $_POST['jenis_absen'])->
                                     or_where('jenis_absen', 'AMP dan AMS')->
                                 group_end()->
                                 get('tb_absen_manual');


            $data       = $d->row_array();
            $num_data   = $d->num_rows();

            if ($num_data > 0) {
                echo json_encode(true);
                return;
            }
        }
        echo json_encode(false);
        return;
    }


    public function addmanual()
    {

        $akses = [1,2,3]; 
        $hak_akses = in_array($this->session->userdata('role_id'), $akses);

        if($hak_akses){
            $this->form_validation->set_rules('skpd_id', 'Unit Kerja', 'required');
            $this->form_validation->set_rules('pegawai_id', 'Nama Pegawai', 'required');
            $this->form_validation->set_rules('jenis_pegawai', 'Jenis Pegawai', 'required');
        }
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run()) {
            $tgl_start = new DateTime(date("Y-m-d"));
            $tgl_start->modify('-7 day');
            $tgl_start = $tgl_start->format("Y-m-d");

            if(($this->session->role_id == 3 || $this->session->role_id == 4) && (strtotime($_POST['tanggal'])<strtotime($tgl_start))){
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Maaf, tanggal yang anda masukkan tidak Valid!</div>');
                redirect('absenmanual/addmanual?token=' . $_GET['token']);
                return;
            }

            $addManual = $this->AbsenManual_model->addDataManual();
            if ($addManual[0] == true) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">' . $addManual[1] . '</div>');
                redirect('absenmanual?token=' . $_GET['token']);
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">' . $addManual[1] . '</div>');
                redirect('absenmanual/addmanual?token=' . $_GET['token']);
            }
            return;
        }



        
		$data = [
		    "title"             => "Add Absensi Manual",
			"page"				=> "absenmanual/addmanual",
			"skpd"              => $this->Skpd_model->getSkpd(),
			"javascript"		=> [
				base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		
		$this->load->view('template/default', $data);

    }



    public function deletemanual($id)
    {

        if (!isset($_GET['token']) || $_GET['token'] == "") {
            redirect('auth/logout/nomessage');
        }
        $this->AbsenManual_model->deleteDataManual($id);
        $this->session->set_flashdata('pesan', '
        <div class="alert alert-success" role="alert">
        Absen Manual Deleted</div>
        ');
        redirect('absensi/manual?token=' . $_GET['token']);
    }



}
