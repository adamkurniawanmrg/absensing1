<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jabatan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
		$data = [
		    "title"             => "Jabatan dan Penghasilan",
			"page"				=> "jabatan/jabatan",
			"jabatans"          => $this->db->where('deleted', null)->order_by('id', 'desc')->get('tb_jabatan_penghasilan')->result_array(),
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
        $jabatan    = $this->db->where('id', $id)->get('tb_jabatan_penghasilan')->row_array();
        if(!$jabatan) {
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Invalid!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
    	    redirect('jabatan?token='.$_GET['token']);
    	    return;
        }

        $this->db->where('id', $id)->update('tb_jabatan_penghasilan', ["deleted"=>"Ya"]);
        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Berhasil dihapus!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
        ');
	    redirect('jabatan?token='.$_GET['token']);
	    return;
        
    }
    
    public function setjabatan($id=false)
    {
        if($id){
            $jabatan    = $this->db->where('id', $id)->get('tb_jabatan_penghasilan')->row_array();
            if(!$jabatan) {redirect('jabatan?token='.$_GET['token']);return;}
        }
        
        $this->form_validation->set_rules('nama_jabatan', 'Nama Jabatan', 'required');
        $this->form_validation->set_rules('penghasilan', 'Penghasilan', 'required');
		if($this->form_validation->run()){
            extract($_POST);
            $data = [
                "nama_jabatan"   => $nama_jabatan,
                "skp"                   => $penghasilan*(60/100),
                "pkp"                   => $penghasilan*(40/100),
                "total"                 => $penghasilan,
            ];
            
            if(isset($jabatan) && $jabatan){
                $this->db->where('id', $id)->update('tb_jabatan_penghasilan', $data);
            }else{
                $this->db->insert('tb_jabatan_penghasilan', $data);
            }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('jabatan?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => $id ? "Ubah Jabatan Penghasilan" : "Tambah Jabatan Penghasilan",
			"page"				=> "jabatan/setjabatan",
			"jabatan"           => isset($jabatan) ? $jabatan : false,
		];
		
		$this->load->view('template/default', $data);
    }





}
