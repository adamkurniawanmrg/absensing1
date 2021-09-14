<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Izinkerja extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Skpd_model','Izin_model','Upacara_model']);
		is_logged_in();
    }

    public function dataizinkerja($bulanTahun=false)
    {
        $data = [
			"page"				=> "izinkerja/data_izinkerja",
			"title"             => "Data Izin Kerja",
            "skpds"             => $this->Skpd_model->getSkpd(true),
            "pegawais"          => $this->Pegawai_model->getPegawai(),
            "tkss"              => $this->Pegawai_model->getPegawaiTks(),
            "izinKerjaVerified" => $this->queryIzinKerja("!=", "tb_izin_kerja.aproved_at"),
            "izinKerjaUnverified" => $this->queryIzinKerja(),
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

    private function queryIzinKerja($eq="", $order="tb_izin_kerja.id"){
        $start_date     = isset($_GET['bulan']) ? date("Y-m-01", strtotime("01-".$_GET['bulan']))   : date("Y-m-01");
        $end_date       = isset($_GET['bulan']) ? date("Y-m-t", strtotime("01-".$_GET['bulan']))    : date("Y-m-t");

        return $this->db->
                        select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*')->
                        where("tb_izin_kerja.jenis_pegawai", $this->session->userdata('jenis_pegawai'))->
                        where("tb_izin_kerja.pegawai_id", $this->session->userdata('user_id'))->
                        where("tb_izin_kerja.skpd_id", $this->session->userdata('skpd_id'))->
                        where("tb_izin_kerja.status".$eq, null)->
                        
                        where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')<=", $end_date)->
                        
                        order_by($order, 'desc')->
                        join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                        order_by('tb_izin_kerja_meta.tanggal_awal', 'desc')->
                        get('tb_izin_kerja')->
                        result_array();
        
    }

    public function getDataIzinKerja(){
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");

        $list = $this->db->
                        select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*')->
                        where("tb_izin_kerja.jenis_pegawai", $this->session->userdata('jenis_pegawai'))->
                        where("tb_izin_kerja.pegawai_id", $this->session->userdata('user_id'))->
                        // where("tb_izin_kerja.status!=", null)->
                        where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')<=", $end_date)->
                        order_by('tb_izin_kerja_meta.tanggal_awal', 'desc')->
                        join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                        order_by('tb_izin_kerja_meta.tanggal_awal', 'desc')->
                        get('tb_izin_kerja')->
                        result_array();

        $data       = array();
        $no         = 1;
        $confirm    = "Anda yakin hapus ?";
        $skpds      = $this->Skpd_model->getSkpd(true);
        $pegawais   = $this->Pegawai_model->getPegawai();
        $tkss       = $this->Pegawai_model->getPegawaiTks();
        foreach ($list as $field) {
            $skpd   = isset($skpds[$field['skpd_id']]) ? $skpds[$field['skpd_id']] : array(['nama_skpd'=>'undefined']);
            
            $indexPegawai   = array_search($field['pegawai_id'], array_column($pegawais, 'user_id'));
            $indexTks       = array_search($field['pegawai_id'], array_column($tkss, 'user_id'));
            $indexAprover   = $field['aproved_by'] ? array_search($field['aproved_by'], array_column($pegawais, 'user_id')) : null;
            
            $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
            $indexTks       = $indexTks!==false ? $indexTks : "none"; 
            $indexAprover   = $indexAprover!==false ? $indexAprover : "none"; 
            

            $pegawai        = $field['jenis_pegawai']=='pegawai' ? 
                              (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                              (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
            
            $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];
            
            $row = array();
            $row[0] = $this->hari[date("w", strtotime($field['tanggal_awal']))] . ", " . date('d F Y', strtotime($field['tanggal_awal']));
            $row[1] = $this->hari[date("w", strtotime($field{'tanggal_akhir'}))] . ", " . date('d F Y', strtotime($field['tanggal_akhir']));
            
            $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
            $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

            $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
            $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
            $totimeDisetujuiPada    = strtotime($field['aproved_at']);
            $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";
            
            $row[2] = (strlen($gelarDepan.$pegawai['nama'].$gelarBelakang)>6 ? substr($gelarDepan.$pegawai['nama'].$gelarBelakang,0,6)."..." : $nama);
            $row[3] = (strlen($skpd['nama_skpd'])>18 ? substr($skpd['nama_skpd'],0,18).".." : $skpd['nama_skpd']);
            
            $row[4] = $field['jenis_izin'];
            $row[5] = '<a href="' . ($field['jenis_izin'] == 'Dinas Luar' ? 'https://simpernas.labura.go.id/publik/cetakspt/'.$field['spt_id'] : base_url('resources/berkas/izin_kerja/' . $field['file_izin'])) . '" class="text-center" target="_BLANK">Berkas</a>';
            $row[6] = $field['status']==1 ? 
                            'Disetujui Oleh '.(isset($aprover['nama']) ? '<strong>'.($field['jenis_izin'] == 'Dinas Luar' ? 'SIMPERNAS' : $aproverGelarDepan.$aprover['nama'].$aproverGelarBelakang).'</strong><br><small class="text-info"><i>'.$disetujuiPada.'</i></small>' : 'undefined')  : 
                      ($field['status']==null ? 
                            '<span class="btn-warning" style="padding: 2px 7px; border-radius: 6px;">Menunggu</span>' : 
                            '<span class="btn-danger" style="padding: 2px 7px; border-radius: 6px;">Ditolak</span>'); 
            $row[7] = '
                <a href="' . site_url('izinkerja/deleteizin/' . $field['meta_id'].'?token=' . $_GET['token']) . '" onclick="return confirm(\'Yakin hapus data?\')"  class="btn btn-danger btn-sm" style="padding: 5px 15px" title="Hapus"><i class="fa fa-trash"></i></a>';
            $no++;

            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        echo json_encode($output);
        return;
    }
    
    public function addizin()
    {

        $data = [
                    "page"				=> "izinkerja/add_izinkerja",
                    "skpd"              => $this->Skpd_model->getSkpd(),
            		"title"             => "Buat Izin Kerja",
            		"javascript"		=> [
                                    		base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js"),
                                    		base_url("assets/js/select2.js"),
    		        ],
            		"css"				=> [
            			                    base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
            		],
    	];

	    $this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'required');
	    $this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'required');

		if ($this->form_validation->run() == false) {
		    $this->load->view('template/default', $data);
		}else{

		    $_POST['tanggal_akhir'] = $_POST['tanggal_akhir'] ? $_POST['tanggal_akhir'] : $_POST['tanggal_awal'];
		    if($this->cekIzin(true)){
                $this->session->set_flashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Data izin sudah ada, silahkan pilih tanggal lain!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                ');
                redirect('izinkerja/addizin?token=' . $_GET['token']);
                return;
		    }
            $addIzin = $this->Izin_model->addDataIzin();

            if ($addIzin[0] == true) {
                $this->session->set_flashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                     ' . $addIzin[1] . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                ');
                redirect('izinkerja/dataizinkerja?token=' . $_GET['token']);
                return;
            }else{
                 $this->session->set_flashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                     ' . $addIzin[1] . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                ');
                redirect('pegawai/addizin?token=' . $_GET['token']);
                return;
            }
        }
    }
    
    public function _get_date($tanggal_awal, $tanggal_akhir)
    {
        $begin = new DateTime($tanggal_awal);
        $end = new DateTime($tanggal_akhir);
        $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        foreach ($period as $dt) {
            $hari[$dt->format("Y-m-d")] = $dt->format("Y-m-d");
        }
        return $hari;
    }
    
    public function cekIzin($no_json=false){
        
        if (isset($_POST['tanggal_awal']) && isset($_POST['tanggal_akhir'])) {
            extract($_POST);
            $tanggal_awal   = date("Y-m-d", strtotime($tanggal_awal));
            $tanggal_akhir  = date("Y-m-d", strtotime($tanggal_akhir));
            $data   = $this->db->
                             select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*')->
                             where('tb_izin_kerja.pegawai_id', $this->session->userdata('user_id'))->
                             where('tb_izin_kerja.jenis_pegawai', $this->session->userdata('jenis_pegawai'))->
                             group_start()->
                                where('tb_izin_kerja.status', null)->
                                or_where('tb_izin_kerja.status', 1)->
                             group_end()->
                             group_start()->
                                or_group_start()->
                                    where('tb_izin_kerja_meta.tanggal_awal>=', $tanggal_awal)->
                                    where('tb_izin_kerja_meta.tanggal_akhir<=', $tanggal_awal)->
                                group_end()->
                                or_group_start()->
                                    where('tb_izin_kerja_meta.tanggal_awal>=', $tanggal_akhir)->
                                    where('tb_izin_kerja_meta.tanggal_akhir<=', $tanggal_akhir)->
                                group_end()->
                             group_end()->
                             order_by('tb_izin_kerja.id', 'desc')->
                             join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                             get('tb_izin_kerja')->result();

            if(count($data)>0) {
                $html = "<ol>";
                foreach($data as $dt){
                    $html .= "<li>".$dt->jenis_izin." ".$dt->tanggal_awal." s/d ".$dt->tanggal_awal."</li>";
                }
                $html .= "</ol>";
                if(!$no_json) echo json_encode([true, $html]);
                return true;
            }
        }
        if(!$no_json) echo json_encode([false]);
        return false;
    }

    
     function selectpegawaibyopdeditizin()
    {
        $this->db->order_by('nama', 'asc');
        $this->db->where("opd_id", $_POST['opd_id']);
        $list = $this->Pegawai_model->getPegawaiByOpd();

        $a = count($list);
        if ($a > 0) {
            echo "<option value=''>-- Pilih Pegawai --</option>";
        } else {
            echo "<option value=''>-- Tidak ada data --</option>";
        }
        foreach ($list as $l) {
            $last_pegawai = explode(',', $_POST['last_pegawai']);
            $selected = in_array($l->id, $last_pegawai) ? "selected" : null;
            echo "<option value='" . $l->id . "'" . $selected . ">" . $l->nama . "</option>";
        }
    }
    
    public function deleteizin($id = false){
        $izinkerja = $this->db->
                           where('meta_id', $id)->
                           where('pegawai_id', $this->session->userdata('user_id'))->
                           where('jenis_pegawai', $this->session->userdata('jenis_pegawai'))->
                           get('tb_izin_kerja')->row();

        if(!$izinkerja){
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                 Invalid!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
            redirect('izinkerja/dataizinkerja?token=' . $_GET['token']);
            return;
        }

        $this->db->where('meta_id', $id)->delete('tb_izin_kerja');
        $this->db->where('id', $id)->delete('tb_izin_kerja_meta');


        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
             Berhasil dihapus!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           ');
        
        redirect('izinkerja/dataizinkerja?token=' . $_GET['token']);
        return;
    }

}
