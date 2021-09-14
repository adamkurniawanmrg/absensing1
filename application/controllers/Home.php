<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
		$this->load->helper('kategori');
    }



    public function index(){

        $jumlahAntrianIzinKerja = $this->db->
                    select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                    where('tb_izin_kerja.status', null)->
                    group_start()->
                        where('tb_tks_meta.pegawai_atasan', $this->session->userdata('user_id'))->
                        or_where('tb_pegawai_meta.pegawai_atasan', $this->session->userdata('user_id'))->
                    group_end()->
                    order_by('tb_izin_kerja.id', 'desc')->
                    join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                    join('tb_tks_meta','tb_tks_meta.tks_id=tb_izin_kerja.pegawai_id', 'left')->
                    join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_izin_kerja.pegawai_id', 'left')->
                    get('tb_izin_kerja')->
                    num_rows();
        $jumlahAntrianAbsenManual = $this->db->
                        select('tb_absen_manual.id absen_manual_id, tb_absen_manual.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                        where('tb_absen_manual.status', null)->
                        group_start()->
                            where('tb_tks_meta.pegawai_atasan', $this->session->userdata('user_id'))->
                            or_where('tb_pegawai_meta.pegawai_atasan', $this->session->userdata('user_id'))->
                        group_end()->
                        order_by('tb_absen_manual.id', 'desc')->
                        join('tb_tks_meta','tb_tks_meta.tks_id=tb_absen_manual.pegawai_id', 'left')->
                        join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_absen_manual.pegawai_id', 'left')->
                        get('tb_absen_manual')->
                        num_rows();

		$data = [
			"page"				        => "home",
			"jumlahAntrianIzinKerja"    => $jumlahAntrianIzinKerja,
			"jumlahAntrianAbsenManual"  => $jumlahAntrianAbsenManual,
			'kategori' => kategoriAbsensi()
		];
		
		$this->load->view('template/default', $data);
    }

    
}
