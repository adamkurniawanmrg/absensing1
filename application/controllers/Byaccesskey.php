<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Byaccesskey extends CI_Controller {
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		$this->load->model(['Pegawai_model', 'Sms_model']);
    }

    public function index(){
		$this->load->view('template/custom', [
		    "title"                     => "INVALID!",
			"page"				        => "byaccesskey/invalid",
		]);
		return;
        
    }

    public function setujuiizinkerja($id, $accesskey){
        $izin_kerja = $this->db->
                            select('tb_izin_kerja.*, tb_izin_kerja_meta.*')->
                            where('tb_izin_kerja.access_key', $accesskey)->
                            where('tb_izin_kerja_meta.id', $id)->
                            where('status', null)->
                            join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                            get('tb_izin_kerja')->row();

        if(!$izin_kerja){
            redirect('byaccesskey');
            return;
        }

        $pegawais           = $this->Pegawai_model->getPegawai();
        $tkss               = $this->Pegawai_model->getPegawaiTks();

        $pegawai            = $this->generatePegawai($izin_kerja->pegawai_id, $izin_kerja->jenis_pegawai, $pegawais, $tkss);
        $pesan              = "[ABSENSI-NG]\n\n Permohonan izin ".$izin_kerja->jenis_izin." Anda telah disetujui.\n\nSilahkan lihat di : https://absensi-ng.labura.go.id";

        $atasan             = $this->Pegawai_model->getPegawaiAtasan($izin_kerja->pegawai_id, $izin_kerja->jenis_pegawai);

        $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);
        
        $this->db->where('meta_id', $id)->update('tb_izin_kerja', [
                "status"            => 1,
                "aproved_by"        => isset($atasan['pegawai_atasan_id']) ? $atasan['pegawai_atasan_id'] : null,
                "aproved_by_nama"   => isset($atasan['nama_pegawai_atasan']) ? $atasan['nama_pegawai_atasan'] : "Unknown",
                'aproved_at'        => date("Y-m-d H:i:s")
            ]);

		$this->load->view('template/custom', [
		    "title"     => "Berhasil disetujui",
			"page"		=> "byaccesskey/izinkerja",
		]);
		return;
    }
    public function tolakizinkerja($id, $accesskey){
        $izin_kerja = $this->db->
                            select('tb_izin_kerja.*, tb_izin_kerja_meta.*')->
                            where('tb_izin_kerja.access_key', $accesskey)->
                            where('tb_izin_kerja_meta.id', $id)->
                            where('status', null)->
                            join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                            get('tb_izin_kerja')->row();

        if(!$izin_kerja){
            redirect('byaccesskey');
            return;
        }

        $pegawais           = $this->Pegawai_model->getPegawai();
        $tkss               = $this->Pegawai_model->getPegawaiTks();

        $pegawai            = $this->generatePegawai($izin_kerja->pegawai_id, $izin_kerja->jenis_pegawai, $pegawais, $tkss);

        $atasan             = $this->Pegawai_model->getPegawaiAtasan($izin_kerja->pegawai_id, $izin_kerja->jenis_pegawai);

        $pesan              = "[ABSENSI-NG]\n\n Permohonan izin ".$izin_kerja->jenis_izin." Anda telah ditolak.\n\nSilahkan lihat di : https://absensi-ng.labura.go.id";

        $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);

        $this->db->where('meta_id', $id)->update('tb_izin_kerja', [
                "status"            => 0,
                "aproved_by"        => isset($atasan['pegawai_atasan_id']) ? $atasan['pegawai_atasan_id'] : null,
                "aproved_by_nama"   => isset($atasan['nama_pegawai_atasan']) ? $atasan['nama_pegawai_atasan'] : "Unknown",
                'aproved_at'        => date("Y-m-d H:i:s")
            ]);

		$this->load->view('template/custom', [
		    "title"     => "Berhasil ditolak",
			"page"		=> "byaccesskey/izinkerja",
		]);
		return;
    }
    
    public function setujuiabsenmanual($id, $accesskey){
        $absenmanual = $this->db->
                            where('access_key', $accesskey)->
                            where('id', $id)->
                            where('status', null)->
                            get('tb_absensi')->row();

        if(!$absenmanual){
            redirect('byaccesskey');
            return;
        }

        $pegawais           = $this->Pegawai_model->getPegawai();
        $tkss               = $this->Pegawai_model->getPegawaiTks();

        $pegawai            = $this->generatePegawai($absenmanual->pegawai_id, $absenmanual->jenis_pegawai, $pegawais, $tkss);
        $pesan              = "[ABSENSI-NG]\n\n Permohonan Absen Manual \'".$absenmanual->keterangan."\' Anda telah disetujui.\n\nSilahkan lihat di : https://absensi-ng.labura.go.id";

        $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);

        $atasan             = $this->Pegawai_model->getPegawaiAtasan($absenmanual->pegawai_id, $absenmanual->jenis_pegawai);

        $this->db->where('id', $id)->update('tb_absensi', [
                "status"            => 1,
                "approved_by"       => isset($atasan['pegawai_atasan_id']) ? $atasan['pegawai_atasan_id'] : null,
                "approved_by_nama"  => isset($atasan['nama_pegawai_atasan']) ? $atasan['nama_pegawai_atasan'] : "Unknown",
                'approved_at'        => date("Y-m-d H:i:s")
            ]);

		$this->load->view('template/custom', [
		    "title"     => "Berhasil disetujui",
			"page"		=> "byaccesskey/absenmanual",
		]);
		return;
    }
    
    public function tolakabsenmanual($id, $accesskey){
        $absenmanual = $this->db->
                            where('access_key', $accesskey)->
                            where('id', $id)->
                            where('status', null)->
                            get('tb_absensi')->row();

        if(!$absenmanual){
            redirect('byaccesskey');
            return;
        }

        $pegawais           = $this->Pegawai_model->getPegawai();
        $tkss               = $this->Pegawai_model->getPegawaiTks();

        $pegawai            = $this->generatePegawai($absenmanual->pegawai_id, $absenmanual->jenis_pegawai, $pegawais, $tkss);
        $pesan              = "[ABSENSI-NG]\n\n Permohonan Absen Manual \'".$absenmanual->keterangan."\' Anda telah ditolak.\n\nSilahkan lihat di : https://absensi-ng.labura.go.id";

        $atasan             = $this->Pegawai_model->getPegawaiAtasan($absenmanual->pegawai_id, $absenmanual->jenis_pegawai);

        $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);

        $this->db->where('id', $id)->update('tb_absensi', [
                "status"            => 0,
                "approved_by"       => isset($atasan['pegawai_atasan_id']) ? $atasan['pegawai_atasan_id'] : null,
                "approved_by_nama"  => isset($atasan['nama_pegawai_atasan']) ? $atasan['nama_pegawai_atasan'] : "Unknown",
                'approved_at'        => date("Y-m-d H:i:s")
            ]);

		$this->load->view('template/custom', [
		    "title"     => "Berhasil ditolak",
			"page"		=> "byaccesskey/absenmanual",
		]);
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

    
}
