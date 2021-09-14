<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Golongan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
		$data = [
		    "title"             => "Jabatan Golongan",
			"page"				=> "golongan/golongan",
			"golongans"         => $this->db->where('deleted', null)->order_by('id', 'desc')->get('tb_jabatan_golongan')->result_array(),
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
    
    public function hapus($id)
    {
        $golongan    = $this->db->where('id', $id)->get('tb_jabatan_golongan')->row_array();
        if(!$golongan) {
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Invalid!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
    	    redirect('golongan?token='.$_GET['token']);
    	    return;
        }

        $this->db->where('id', $id)->update('tb_jabatan_golongan', ["deleted"=>"Ya"]);
        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Berhasil dihapus!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
        ');
	    redirect('golongan?token='.$_GET['token']);
	    return;
        
    }
    
    public function setgolongan($id=false)
    {
        if($id){
            $golongan    = $this->db->where('id', $id)->get('tb_jabatan_golongan')->row_array();
            if(!$golongan) {redirect('golongan?token='.$_GET['token']);return;}
        }
        
        $this->form_validation->set_rules('nama_golongan', 'Nama Golongan', 'required');
        $this->form_validation->set_rules('pph', 'PPH', 'required');
		if($this->form_validation->run()){
            extract($_POST);
            $data = [
                "nama_golongan" => $nama_golongan,
                "pph"           => $pph,
            ];
            
            if(isset($golongan) && $golongan){
                $this->db->where('id', $id)->update('tb_jabatan_golongan', $data);
            }else{
                $this->db->insert('tb_jabatan_golongan', $data);
            }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('golongan?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => $id ? "Ubah Jabatan Golongan" : "Tambah Golongan",
			"page"				=> "golongan/setgolongan",
			"golongan"          => isset($golongan) ? $golongan : false,
		];
		
		$this->load->view('template/default', $data);
    }





}
