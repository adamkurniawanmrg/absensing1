<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifikasi extends CI_Controller {

    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];


	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Skpd_model','Izin_model', 'Sms_model']);
		is_logged_in();
    }

    public function izinkerja()
    {
        $start_date     = isset($_GET['bulan']) ? date("Y-m-01", strtotime("01-".$_GET['bulan']))   : date("Y-m-01");
        $end_date       = isset($_GET['bulan']) ? date("Y-m-t", strtotime("01-".$_GET['bulan']))    : date("Y-m-t");
        $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($this->session->userdata('user_id'), $this->session->userdata('jenis_pegawai'));
        
        $jumlahAntrian  = 0;
        $izinKerja      = array();
        
        if(count($pegawai)>0){
            $this->db->group_start();
                foreach($pegawai as $pegawai){
                    $this->db->or_group_start();
                        $this->db->where('tb_izin_kerja.pegawai_id', $pegawai['pegawai_id']);
                        $this->db->where('tb_izin_kerja.jenis_pegawai', $pegawai['jenis_pegawai']);
                    $this->db->group_end();
                }
            $this->db->group_end();

            $izinKerja = $this->db->
                            select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                            where('tb_izin_kerja.status', null)->
                            where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')>=", $start_date)->
                            where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')<=", $end_date)->
                            join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                            join('tb_tks_meta','tb_tks_meta.tks_id=tb_izin_kerja.pegawai_id', 'left')->
                            join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_izin_kerja.pegawai_id', 'left')->
                            order_by('tb_izin_kerja_meta.tanggal_awal', 'desc')->
                            get('tb_izin_kerja')->
                            result_array();
            $jumlahAntrian = count($izinKerja);
        }


        $data = [
			"page"				=> "verifikasi/izinkerja",
			"title"             => "Verifikasi Permohonan Izin Kerja",
            "skpds"             => $this->Skpd_model->getSkpd(true),
            "pegawais"          => $this->Pegawai_model->getPegawai(),
            "tkss"              => $this->Pegawai_model->getPegawaiTks(),
            "izinkerja"         => $izinKerja,
			"jumlahAntrian"     => $jumlahAntrian,
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

     function getDataIzinKerja(){

        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");
        
        $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($this->session->userdata('user_id'), $this->session->userdata('jenis_pegawai'));
        
        $list      = array();

        if(count($pegawai)>0){
            $this->db->group_start();
                foreach($pegawai as $pegawai){
                    $this->db->or_group_start();
                        $this->db->where('tb_izin_kerja.pegawai_id', $pegawai['pegawai_id']);
                        $this->db->where('tb_izin_kerja.jenis_pegawai', $pegawai['jenis_pegawai']);
                    $this->db->group_end();
                }
            $this->db->group_end();
            $list = $this->db->
                            select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                            where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')>=", $start_date)->
                            where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')<=", $end_date)->
                            join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                            join('tb_tks_meta','tb_tks_meta.tks_id=tb_izin_kerja.pegawai_id', 'left')->
                            join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_izin_kerja.pegawai_id', 'left')->
                            order_by('tb_izin_kerja_meta.tanggal_awal', 'desc')->
                            get('tb_izin_kerja')->
                            result_array();

        }



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
            $row[] = $this->hari[date("w", strtotime($field['tanggal_awal']))] . ", " . date('d F Y', strtotime($field['tanggal_awal']));
            $row[] = $this->hari[date("w", strtotime($field{'tanggal_akhir'}))] . ", " . date('d F Y', strtotime($field['tanggal_akhir']));
            
            $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
            $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

            $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
            $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
            $totimeDisetujuiPada    = strtotime($field['aproved_at']);
            $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";
            $nama                   = $gelarDepan.$pegawai['nama'].$gelarBelakang;

            $row[] = (strlen($nama)>10 ? substr($nama,0,10)."..." : $nama);
            $row[] = (strlen($skpd['nama_skpd'])>18 ? substr($skpd['nama_skpd'],0,18).".." : $skpd['nama_skpd']);
            $row[] =(strlen($field['jenis_izin'])>7 ? substr($field['jenis_izin'],0,7)."..." : $field['jenis_izin']);
            $row[] = '<a href="' . base_url() . 'resources/berkas/izin_kerja/' . $field['file_izin'] . '" class="text-center" target="_BLANK">Lihat Berkas</a>';
            $row[] = $field['status']==1 ? 
                            'Disetujui Oleh '.(isset($aprover['nama']) ? '<strong>'.$aproverGelarDepan.$aprover['nama'].$aproverGelarBelakang.'</strong><br><small class="text-info"><i>'.$disetujuiPada.'</i></small>' : 'undefined')  : 
                      ($field['status']==null ? 
                            '<span class="btn-warning" style="padding: 2px 7px; border-radius: 6px;">Menunggu</span>' : 
                            '<span class="btn-danger" style="padding: 2px 7px; border-radius: 6px;">Ditolak</span>'); 
            $row[] = $field['status']==null ? '
                <a href="' . site_url('verifikasi/prosesizinkerja/tolak/' . $field['izin_kerja_id'].'?token=' . $_GET['token']) . '" onclick="return confirm(\'Yakin tolak izin kerja ?\')"  class="btn btn-danger btn-sm" style="padding:6px; margin-top: -6px; margin-bottom: -6px;" title="Tolak izin kerja"><i class="ti-close"></i> </a>
                <a href="' . site_url('verifikasi/prosesizinkerja/setuju/' . $field['izin_kerja_id'].'?token=' . $_GET['token']) . '" onclick="return confirm(\'Yakin setujui izin kerja ?\')"  class="btn btn-success btn-sm" style="padding:6px; margin-top: -6px; margin-bottom: -6px;" title="Setuju izin kerja"><i class="ti-check"></i> </a>
                ' : null;
            $no++;

            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
    
    public function prosesizinkerja($act, $izin_kerja_id){
        $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($this->session->userdata('user_id'), $this->session->userdata('jenis_pegawai'));

        if(count($pegawai)==0){
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
              </div>
            ');
            redirect('verifikasi/izinkerja?token=' . $_GET['token']);
            return;
        }

        $this->db->group_start();
            foreach($pegawai as $pegawai){
                $this->db->or_group_start();
                    $this->db->where('tb_izin_kerja.pegawai_id', $pegawai['pegawai_id']);
                    $this->db->where('tb_izin_kerja.jenis_pegawai', $pegawai['jenis_pegawai']);
                $this->db->group_end();
            }
        $this->db->group_end();

        $rows = $this->db->
                        select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                        where('tb_izin_kerja.id', $izin_kerja_id)->
                        order_by('tb_izin_kerja.id', 'desc')->
                        join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                        join('tb_tks_meta','tb_tks_meta.tks_id=tb_izin_kerja.pegawai_id', 'left')->
                        join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_izin_kerja.pegawai_id', 'left')->
                        get('tb_izin_kerja')->num_rows();
        
        if($rows<=0){
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
              </div>
            ');
            redirect('verifikasi/izinkerja?token=' . $_GET['token']);
            return;
        }
        
        $data = [
                "status"            => $act=='setuju' ? 1 : 0,
                "aproved_by"        => $this->session->userdata('user_id'),
                "aproved_by_nama"   => $this->session->userdata('nama'),
                'aproved_at'        => date("Y-m-d H:i:s")
            ];
        $this->db->where('id', $izin_kerja_id)->update('tb_izin_kerja', $data);

        $izin_kerja             = $this->db
                                        ->select('tb_izin_kerja.*, tb_izin_kerja_meta.jenis_izin')
                                        ->where('tb_izin_kerja.id', $izin_kerja_id)
                                        ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                                        ->get('tb_izin_kerja')
                                        ->row_array();

        if(isset($izin_kerja['pegawai_id'])){
            $pegawais           = $this->Pegawai_model->getPegawai();
            $tkss               = $this->Pegawai_model->getPegawaiTks();

            $pegawai            = $this->generatePegawai($izin_kerja['pegawai_id'], $izin_kerja['jenis_pegawai'], $pegawais, $tkss);
            $pesan              = "[ABSENSI-NG]\n\n Permohonan izin ".$izin_kerja['jenis_izin']." Anda telah ".($act=='setuju' ? 'disetujui':'ditolak' ).".\n\nSilahkan lihat di : https://absensi-ng.labura.go.id";

            $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);
        }

        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Berhasil di'.($act=='setuju' ? 'setujui':'tolak' ).'!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
          </div>
        ');
        redirect('verifikasi/izinkerja?token=' . $_GET['token']);
        return;

        
    }
    
    private function generatePegawai($pegawai_id, $jenis_pegawai, $pegawais, $tkss){

        if($jenis_pegawai=='pegawai'){
            $indexPegawai   = array_search($pegawai_id, array_column($pegawais, 'user_id'));
            $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
            $pegawai        = (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']);
        }else{
            $indexTks       = array_search($pegawai_id, array_column($tkss, 'user_id'));
            $indexTks       = $indexTks!==false ? $indexTks : "none"; 
            $pegawai        = (isset($tkss[$indexTks]) ? $tkss[$indexTks] : ['nama'=>'undefined']);
        }
        $gelarDepan         = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
        $gelarBelakang      = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

        $pegawai['nama']    = $gelarDepan.$pegawai['nama'].$gelarBelakang;

        return $pegawai;
    }
    

    private function absenmanual_()
    {
        $jumlahAntrian = $this->db->
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
			"page"				=> "verifikasi/absenmanual_",
			"title"             => "Verifikasi Data Absen Manual",
			"skpd"              => $this->Skpd_model->getSkpd(),
			"jumlahAntrian"     => $jumlahAntrian,
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		
		$this->load->view('template/default', $data);   
    }
    
    public function absenmanual()
    {
        $start_date     = isset($_GET['bulan']) ? date("Y-m-01", strtotime("01-".$_GET['bulan']))   : date("Y-m-01");
        $end_date       = isset($_GET['bulan']) ? date("Y-m-t", strtotime("01-".$_GET['bulan']))    : date("Y-m-t");
        $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($this->session->userdata('user_id'), $this->session->userdata('jenis_pegawai'));
        
        $absenmanual    = array();
        $jumlahAntrian  = 0;
        if(count($pegawai)>0){
            $this->db->group_start();
                foreach($pegawai as $pegawai){
                    $this->db->or_group_start();
                        $this->db->where('tb_absensi.pegawai_id', $pegawai['pegawai_id']);
                        $this->db->where('tb_absensi.jenis_pegawai', $pegawai['jenis_pegawai']);
                    $this->db->group_end();
                }
            $this->db->group_end();

            $absenmanual = $this->db->
                            select('tb_absensi.id absensi_id, tb_absensi.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                            group_start()->
                                where('tb_absensi.keterangan!=', null)->
                                where('tb_absensi.keterangan!=', "")->
                            group_end()->
                            where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
                            where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
                            // where('tb_absensi.status', null)->
                            join('tb_tks_meta','tb_tks_meta.tks_id=tb_absensi.pegawai_id', 'left')->
                            join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_absensi.pegawai_id', 'left')->
                            order_by('tb_absensi.jam', 'desc')->
                            get('tb_absensi')->
                            result_array();
            $jumlahAntrian = count($absenmanual);
        }


        $data = [
			"page"				=> "verifikasi/absenmanual",
			"title"             => "Verifikasi Absen Manual",
            "skpds"             => $this->Skpd_model->getSkpd(true),
            "pegawais"          => $this->Pegawai_model->getPegawai(),
            "tkss"              => $this->Pegawai_model->getPegawaiTks(),
			"jumlahAntrian"     => $jumlahAntrian,
			"absenmanual"       => $absenmanual,
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

    
    function getDataAbsenManual(){
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");

        $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($this->session->userdata('user_id'), $this->session->userdata('jenis_pegawai'));
        $list           = array();
        if(count($pegawai)>0){
            $this->db->group_start();
                foreach($pegawai as $pegawai){
                    $this->db->or_group_start();
                        $this->db->where('tb_absensi.pegawai_id', $pegawai['pegawai_id']);
                        $this->db->where('tb_absensi.jenis_pegawai', $pegawai['jenis_pegawai']);
                    $this->db->group_end();
                }
            $this->db->group_end();

            $list = $this->db->
                            select('tb_absensi.id absensi_id, tb_absensi.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                            group_start()->
                                where('tb_absensi.keterangan!=', null)->
                                where('tb_absensi.keterangan!=', "")->
                            group_end()->
                            where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
                            where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
                            // where('tb_absensi.status', null)->
                            join('tb_tks_meta','tb_tks_meta.tks_id=tb_absensi.pegawai_id', 'left')->
                            join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_absensi.pegawai_id', 'left')->
                            order_by('tb_absensi.jam', 'desc')->
                            get('tb_absensi')->
                            result_array();
        }

        $skpds      = $this->Skpd_model->getSkpd(true);
        $pegawais   = $this->Pegawai_model->getPegawai();
        $tkss       = $this->Pegawai_model->getPegawaiTks();
        $data = array();
        foreach ($list as $field) {
            $skpd   = isset($skpds[$field['skpd_id']]) ? $skpds[$field['skpd_id']] : array(['nama_skpd'=>'undefined']);
            
            $indexPegawai   = array_search($field['pegawai_id'], array_column($pegawais, 'user_id'));
            $indexTks       = array_search($field['pegawai_id'], array_column($tkss, 'user_id'));

            $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
            $indexTks       = $indexTks!==false ? $indexTks : "none"; 

            $pegawai        = $field['jenis_pegawai']=='pegawai' ? 
                              (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                              (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);

            $row = array();

            $row[0] = $this->hari[date("w", strtotime($field['jam']))] . ", " . date('d F Y - H:i', strtotime($field['jam']));

            $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
            $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
            $nama           = $gelarDepan.$pegawai['nama'].$gelarBelakang;

            $row[1] = (strlen($nama)>14 ? substr($nama,0,14)."..." : $nama);
            $row[2] = (strlen($skpd['nama_skpd'])>20 ? substr($skpd['nama_skpd'],0,20)."..." : $skpd['nama_skpd']);
            $row[3] = $field['jenis_absen'];
            $row[4] = $field['keterangan'];
            $row[5] = "<a href='".$field['file_absensi']."' target='_blank'>Berkas</a>";
            $status = ["<span class='text-danger'>Ditolak</span>", "<span class='text-success'>Disetujui</span>"];
            $row[6] = $field['status']!=null ?  $status[$field['status']] : "<span class='text-warning'>Menunggu</span>";
            $row[7] = $field['status']==null ? '
                <a href="' . site_url('verifikasi/prosesabsenmanual/tolak/' . $field['absensi_id'].'?token=' . $_GET['token']) . '" onclick="return confirm(\'Yakin tolak absen manual ?\')"  class="btn btn-danger btn-sm" style="padding:6px; margin-top: -6px; margin-bottom: -6px;" title="Tolak"><i class="ti-close"></i></a>
                <a href="' . site_url('verifikasi/prosesabsenmanual/setuju/' . $field['absensi_id'].'?token=' . $_GET['token']) . '" onclick="return confirm(\'Yakin setujui absen manual ?\')"  class="btn btn-success btn-sm" style="padding:6px; margin-top: -6px; margin-bottom: -6px;" title="Setuju"><i class="ti-check"></i></a>
                ' : null;

            $data[] = $row;

        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }
    
    
    
    public function prosesabsenmanual($act, $absensi_id){
        $rows = $this->db->
                        select('tb_absensi.id absensi_id, tb_absensi.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                        where('tb_absensi.id', $absensi_id)->
                        group_start()->
                            where('tb_tks_meta.pegawai_atasan', $this->session->userdata('user_id'))->
                            or_where('tb_pegawai_meta.pegawai_atasan', $this->session->userdata('user_id'))->
                        group_end()->
                        order_by('tb_absensi.id', 'desc')->
                        join('tb_tks_meta','tb_tks_meta.tks_id=tb_absensi.pegawai_id', 'left')->
                        join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_absensi.pegawai_id', 'left')->
                        get('tb_absensi')->num_rows();
        
        if($rows<=0){
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
              </div>
            ');
            redirect('verifikasi/absenmanual?token=' . $_GET['token']);
            return;
        }
        
        $data = [
                "status"            => $act=='setuju' ? 1 : 0,
                "approved_by"       => $this->session->userdata('user_id'),
                "approved_by_nama"  => $this->session->userdata('nama'),
                'approved_at'       => date("Y-m-d H:i:s")
            ];
        $this->db->where('id', $absensi_id)->update('tb_absensi', $data);

        $absenmanual            = $this->db->where('id', $absensi_id)->get('tb_absensi')->row_array();
        if(isset($absenmanual['pegawai_id'])){
            $pegawais           = $this->Pegawai_model->getPegawai();
            $tkss               = $this->Pegawai_model->getPegawaiTks();

            $pegawai            = $this->generatePegawai($absenmanual['pegawai_id'], $absenmanual['jenis_pegawai'], $pegawais, $tkss);
            $pesan              = "[ABSENSI-NG]\n\nPermohonan Absen Manual Anda telah ".($act=='setuju' ? 'disetujui':'ditolak' ).".\n\nSilahkan lihat di : https://absensi-ng.labura.go.id";

            $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);
        }

        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Berhasil di'.($act=='setuju' ? 'setujui':'tolak' ).'!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
          </div>
        ');
        redirect('verifikasi/absenmanual?token=' . $_GET['token']);
        return;

        
    }
    
    
}
