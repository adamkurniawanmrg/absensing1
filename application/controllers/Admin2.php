<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin2 extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];

    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
	
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Mesin_model','Opd_model','JabatanGolongan_model','Jamkerja_model','Jabatan_model','Skp_model','Izin_model','Upacara_model']);
		is_logged_in();
    }

    public function index(){
        if ($this->session->userdata('role_id')!=1 && $this->session->userdata('role_id')!=2 && $this->session->userdata('role_id')!=7) {
            $getopd = $this->Opd_model->getOpdById($this->session->userdata('opd_id'));
            $opd = array();
            $opd[$getopd['id']] = $getopd;
        }else{
            $opd = $this->Opd_model->getAllOpd();
        }

		$data = [
			"page"				=> "pegawai/data_pegawai",
			"title"             => "Data Pegawai",
			"datapegawai"       => $this->db->get('tb_pegawai')->result_array(),
			"opd"               => $opd,
			"javascript"		=> [
				
				
				base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js"),
				base_url("assets/js/select2.js"),
			
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
			"javascriptCode"	=> "
    			(function($) {
    				'use strict';
    				$(function() {
    				  $('#order-listing').DataTable();
    				  
    				});
    				
    				
    			})(jQuery);
			",
			"cssCode"			=> "",
		];
		
		$this->load->view('template/default', $data);
    }
    
    function selectpegawaibyopd()
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
            echo "<option value='" . $l->id . "'>" . $l->nama . "</option>";
        }
    }
    

    public function dataizin()
    {
        if ($this->session->userdata('role_id')!=1) {
            $getopd = $this->Opd_model->getOpdById($this->session->userdata('opd_id'));
            $opd = array();
            $opd[$getopd['id']] = $getopd;
        }else{
            $opd = $this->Opd_model->getAllOpd();
        }

        $data = [
			"page"				=> "izinkerjaadmin/data_izinkerja",
			"title"             => "Data Izin Kerja",
			"opd"               => $opd,
			"pegawai"           => $this->db->get('tb_pegawai')->result_array(),
			
			"javascript"		=> [
				
				
			
			
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
			"javascriptCode"	=> "
    			(function($) {
    				'use strict';
    				$(function() {
    				  $('#order-listing').DataTable();
    				  
    				});
    				
    			})(jQuery);
			",
			"cssCode"			=> "",
		];
		
		$this->load->view('template/default', $data);   
    }
    
     function get_data_izin_kerja()
    {
        $this->db->order_by('id', 'desc');
        // $this->db->where("tb_izin_kerja.opd_id", $_POST['opdId']);
        
        if (isset($_POST['pegawaiId']) && $_POST['pegawaiId'] != "") {
            $this->db->like("tb_izin_kerja.pegawai_id", $_POST['pegawaiId']);
        }
        
        if (isset($_POST['opdId']) && $_POST['opdId'] != "") {
            $this->db->where("tb_izin_kerja.opd_id", $_POST['opdId']);
        }else if($this->session->userdata('role_id')==3){
            $this->db->where("tb_izin_kerja.opd_id", $this->session->userdata('opd_id'));
        }
        
        if (isset($_POST['tgl_awal']) && $_POST['tgl_awal'] != "") {
            $this->db->where("tb_izin_kerja.tanggal_awal>=", date("Y-m-d", strtotime($_POST['tgl_awal'])));
        }
        
         if (isset($_POST['tgl_akhir']) && $_POST['tgl_akhir'] != "") {
            $this->db->where("tb_izin_kerja.tanggal_akhir<=", date("Y-m-d", strtotime($_POST['tgl_akhir'])));
        }
        
        $list = $this->Izin_model->getAllIzin();
        $data = array();
        $no = 1;
        $confirm = "Anda yakin hapus ?";
        $opds = $this->Opd_model->getAllOpd();
        foreach ($list as $field) {

            $pgw_id = explode(',', $field['pegawai_id']);
            if (isset($_POST['opdId']) && $_POST['opdId'] != "opdId" && isset($_POST['pegawaiId']) && $_POST['pegawaiId'] != "") {
                if ($field['pegawai_id'] == $_POST['pegawaiId'] || in_array($_POST['pegawaiId'], $pgw_id)) {
                } else {
                    continue;
                }
            }

            $nama = explode(",", $field['pegawai_id']);
            $namapegawai = "";
            for ($index = 0; $index < count($nama); $index++) {

                $opd = $opds[$field['opd_id']];

                $this->db->where('tb_pegawai.id', $nama[$index]);
                $pegawai = $this->db->get('tb_pegawai')->row_array();

                $row = array();

                if (isset($_POST['pegawaiId']) && $_POST['pegawaiId'] != "" && $_POST['pegawaiId'] == $pegawai['id']) {
                    $row[3] = $pegawai['nama'];
                } else if (isset($_POST['pegawaiId']) && $_POST['pegawaiId'] == "") {
                    $row[3] = $pegawai['nama'];
                } else {
                    continue;
                }
                

                $row[0] = $no;
                $row[1] = $this->hari[date("w", strtotime($field['tanggal_awal']))] . ", " . date('d F Y', strtotime($field['tanggal_awal']));
                $row[2] = $this->hari[date("w", strtotime($field{
                    'tanggal_akhir'}))] . ", " . date('d F Y', strtotime($field['tanggal_akhir']));


                $row[4] = $opd['singkatan'];
                $row[5] = $field['jenis_izin'];
                // $row[6] = '<a href="' . base_url() . 'assets/img/berkas/izin_kerja/' . $field['file_izin'] . '" class="text-center" target="_BLANK">Lihat Berkas</a>';
                $row[6] = '
                    <a href="' . base_url('admin2/editizin/' . $field['id'] . '?token=' . $_GET['token']) . '" class="btn btn-success btn-sm" style="padding: 5px 15px"><i class="fa fa-edit" title="Edit"></i> Ubah</a>
                    
                    <a href="' . site_url('admin2/deleteizin/' . $field['id'] . '/' . $nama[$index] . '?token=' . $_GET['token']) . '" onclick="return confirm(\'Yakin hapus data?\')"  class="btn btn-danger btn-sm" style="padding: 5px 15px" title="Delete"><i class="fa fa-trash"></i> Hapus</a>';
                $no++;

                $data[] = $row;
            }
        }

        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
    
    public function addizin()
    {

        if ($this->session->userdata('role_id')!=1) {
            $getopd = $this->Opd_model->getOpdById($this->session->userdata('opd_id'));
            $opd = array();
            $opd[$getopd['id']] = $getopd;
        }else{
            $opd = $this->Opd_model->getAllOpd();
        }


    $data = [
		"page"				=> "izinkerjaadmin/add_izinkerja",
		"opd"               => $opd,
	    "pegawai"           => $this->db->get('tb_pegawai')->result_array(),
		
		"title"             => "Tambah Data Izin Kerja",
		"javascript"		=> [
		
		
		base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js"),
		base_url("assets/js/select2.js"),
		
		],
		"css"				=> [
			base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
		],
		"javascriptCode"	=> "
			(function($) {
				'use strict';
				$(function() {
				  $('#order-listing').DataTable();
				  
				});
				
			})(jQuery);
		",
		"cssCode"			=> "",
		];
		
	    $this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'required');
        
		 if ($this->form_validation->run() == false) {
		     
		    $this->load->view('template/default', $data);
		    
		 } else {
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
             redirect('admin2/dataizin?token=' . $_GET['token']);
            } else {
               
             $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                 ' . $addIzin[1] . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
             redirect('admin2/addizin?token=' . $_GET['token']);
            }
        
      }
    }
    
    
    public function editizin($id)
    {
        if ($this->session->userdata('role_id')!=1) {
            $getopd = $this->Opd_model->getOpdById($this->session->userdata('opd_id'));
            $opd = array();
            $opd[$getopd['id']] = $getopd;
        }else{
            $opd = $this->Opd_model->getAllOpd();
        }

         $data = [
		"page"				=> "izinkerjaadmin/edit_izinkerja",
		"opd"               => $opd,
		"pegawai"           => $this->db->get('tb_pegawai')->result_array(),
		"izin"              => $this->Izin_model->getIzinById($id),
		"title"             => "Ubah Data Izin Kerja",
		"javascript"		=> [
		
		
		base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js"),
		base_url("assets/js/select2.js"),
		
		],
		"css"				=> [
			base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
		],
		"javascriptCode"	=> "
			(function($) {
				'use strict';
				$(function() {
				  $('#order-listing').DataTable();
				  
				});
				
			})(jQuery);
		",
		"cssCode"			=> "",
		];
		 $data['last_pegawai'] = $data['izin']['pegawai_id'];
		
	    $this->form_validation->set_rules('opd_id', 'Nama OPD', 'required');
        $this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'required');
        $this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'required');
        
		 if ($this->form_validation->run() == false) {
		     
		    $this->load->view('template/default', $data);
		    
		 } else {
          $this->Izin_model->update();
         
           $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
             Data Izin Kerja berhasil diubah
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           ');
         redirect('admin2/dataizin?token=' . $_GET['token']);
       
        
      }
    }
    
    public function _get_date($tanggal_awal, $tanggal_akhir)
    {
        $this->db->get('tb_izin_kerja');
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
    
      public function cekIzin()
    {
        if (isset($_POST['tanggal_awal']) && isset($_POST['tanggal_akhir'])) {
            extract($_POST);
            // print_r($_POST);
            $hasil_true = 0;
            for ($i = 0; $i < count($pegawai_id); $i++) {
                $this->db->like('pegawai_id', $pegawai_id[$i]);
                $this->db->where('opd_id', $opd_id);

                $d = $this->db->get('tb_izin_kerja');
                $data = $d->result_array();

                $num_data = $d->num_rows();
                $rentang_tanggal = $this->_get_date($tanggal_awal, $tanggal_akhir);


                if ($num_data > 0) {
                    foreach ($data as $data) {

                        $valid_pegawai = 0;
                        $data_pegawai = explode(",", $data['pegawai_id']);
                        for ($j = 0; $j < count($data_pegawai); $j++) {
                            if ($data_pegawai[$j] == $pegawai_id[$i]) {
                                $valid_pegawai++;
                            }
                        }

                        if ($valid_pegawai == 0) {
                            continue;
                        }


                        $begin = new DateTime($data['tanggal_awal']);
                        $end = new DateTime($data['tanggal_akhir']);
                        $end->modify('+1 day');
                        $interval = DateInterval::createFromDateString('1 day');
                        $period = new DatePeriod($begin, $interval, $end);
                        foreach ($period as $dt) {
                            if (isset($rentang_tanggal[$dt->format("Y-m-d")])) {
                                $hasil_true++;
                                // echo $rentang_tanggal[$dt->format("Y-m-d")];
                                break;
                            }
                        }
                    }
                }
            }
            if ($hasil_true > 0) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }
        } else {
            echo json_encode(false);
        }
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
    
      public function deleteizin($id = false, $pegawai_id = false)
    {
        if (!isset($_GET['token']) || $_GET['token'] == "") {
            redirect('auth/logout/nomessage');
        }

        $this->db->where('id', $id);
        $izin = $this->db->get('tb_izin_kerja')->row();

        $izin_pegawai = explode(',', $izin->pegawai_id);
        if (count($izin_pegawai) > 1) {
            for ($i = 0; $i < count($izin_pegawai); $i++) {
                if ($pegawai_id != $izin_pegawai[$i]) {
                    $pgs[] = $izin_pegawai[$i];
                }
            }
            $data = [
                'pegawai_id' => implode(",", $pgs)
            ];
            $this->db->where('id', $id);
            $this->db->update("tb_izin_kerja", $data);
        } else {
            $this->Izin_model->deleteDataIzin($id);
        }

       $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
             Data Izin Kerja berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           ');
         redirect('admin2/dataizin?token=' . $_GET['token']);
    }
   
    

    
}
