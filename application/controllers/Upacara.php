<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upacara extends CI_Controller {
    
     public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];

    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
	
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Upacara_model']);
		is_logged_in();
    }
    
    public function upacara()
    {
           $data = [
			"page"				=> "upacara/data_upacara",
			"title"             => "Data Upacara & Libur",
			"upacara"           => $this->Upacara_model->getAllUpacara(),
			"javascript"		=> [
				
				
				base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js"),
				base_url("assets/js/select2.js"),
			
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
			"javascriptCode"	=> "
    				  $('#tableUpacara').DataTable({
    				      ordering: false,
                          fnInitComplete: function(oSettings, json) {
                              $('#tableUpacara_wrapper #tableUpacara').addClass('table-responsive');
                          },
    				  });
			",
			"cssCode"			=> "",
		];
		
		$this->load->view('template/default', $data);
    }
    
    public function addupacara()
    {
    	$data = [
		"page"				=> "upacara/add_upacara",
		
		"title"             => "Tambah Data Upacara & Libur",
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
				  $('#order-listing').DataTable({
				  });
				  
				});
				
			})(jQuery);
		",
		"cssCode"			=> "",
		];
		
	     $this->form_validation->set_rules('nama_hari', 'Nama Hari', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');
        
		 if ($this->form_validation->run() == false) {
		     
		    $this->load->view('template/default', $data);
		    
		 } else {
        $this->Upacara_model->addDataUpacara();
         $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
             Data Upacara & Libur berhasil ditambah
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           ');
         redirect('upacara/upacara?token=' . $_GET['token']);
      }
    }
    
     public function editupacara($id)
    {
        	$data = [
			"page"				=> "upacara/edit_upacara",
			"upacara" => $this->Upacara_model->getUpacaraById($id),
			"title"             => "Ubah Data Upacara & Libur",
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
		
	  $this->form_validation->set_rules('nama_hari', 'Nama Hari', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');
        
        
		 if ($this->form_validation->run() == false) {
		     
		    $this->load->view('template/default', $data);
		    
		 } else {
  $this->Upacara_model->editDataUpacara();
  $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Upacara & Libur berhasil diubah
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           ');
         redirect('upacara/upacara?token=' . $_GET['token']);
      }
    }
    
    public function deleteupacara($id)
    {
    
      $this->Upacara_model->deleteDataUpacara($id);
      $this->session->set_flashdata('pesan', '
       <div class="alert alert-success alert-dismissible fade show" role="alert">
        Data Upacara & Libur berhasil dihapus
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       ');
         redirect('upacara/upacara?token=' . $_GET['token']);
    }
}