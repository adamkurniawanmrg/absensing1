<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	public function __construct(){
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
	}
	
	public function role(){
		$this_user_key  = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
		$this_user_pass = 'b546a6dfc4';
		
	
		if(isset($_POST['user_key']) && isset($_POST['pass_key'])){
			extract($_POST);
			if($user_key!=$this_user_key || $pass_key!=$this_user_pass){
				echo json_encode([
					'alert'     => ['class'    => 'danger', 'capt'     => '<strong>Error</strong> Api key tidak valid, silahkan coba lagi!']
				]);
				exit();
			}

			if($method=='get'){
				$role = $this->db->order_by('role_id', 'desc')->get('tb_role')->result_array();
				echo json_encode([
					'data'      => $role,
				]);
				exit();
			}else if($method=='getone' && isset($_POST['role_id'])){
				$role = $this->db->where('role_id', $role_id)->order_by('role_id', 'desc')->get('tb_role')->row_array();
				echo json_encode([
					'data'      => $role,
				]);
				exit();
				
			}else{
				echo json_encode([
					'alert'     => ['class'    => 'danger', 'capt'     => 'Informasi salah, silahkan coba lagi!']
				]);
				exit();
			}
		}
 		
		echo json_encode([
			'alert'     => ['class'    => 'danger', 'capt'     => 'Api key tidak valid, silahkan coba lagi!']
		]);

	}
    
	public function pushSPT()
	{
        if(
            isset($_POST['user_key']) &&
            isset($_POST['pass_key']) &&
            isset($_POST['spt_id']) &&
            isset($_POST['skpd_id']) &&
            isset($_POST['skpd_nama']) &&
            isset($_POST['pegawai_id']) &&
            isset($_POST['jenis_pegawai']) &&
            isset($_POST['nama_pegawai']) &&
            isset($_POST['no_spt']) &&
            isset($_POST['tgl_pergi']) &&
            isset($_POST['tgl_kembali']) &&
            isset($_POST['tgl_keluar'])
        ){
            extract($_POST);
            if($user_key == '64240-d0ede73ccaf823f30d586a5ff9a35fa5' && $pass_key == 'b546a6dfc4'){
                $this->db->insert('tb_izin_kerja_meta', [
                        'tanggal_awal'      => $tgl_pergi,
                        'tanggal_akhir'     => $tgl_kembali,
                        'jenis_izin'        => 'Dinas Luar',
                        'spt_id'            => $spt_id,
                    ]);
                
                $this->db->insert('tb_izin_kerja', [
                        'meta_id'           => $this->db->insert_id(),
                        'skpd_id'           => $skpd_id,
                        'pegawai_id'        => $pegawai_id,
                        'jenis_pegawai'     => $jenis_pegawai,
                        'status'            => 1,
                        'aproved_at'        => date("Y-m-d H:i:s")
                    ]);
                echo json_encode(true);
                return;
            }

        }
        echo json_encode(false);
        return;
        
	}
	public function push_absen_wajah()
	{
        if(
            !isset($_POST['user_key']) || 
            !isset($_POST['pass_key']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['jenis_pegawai']) ||
            !isset($_POST['skpd_id'])
        ){
            echo json_encode(false);
            return;
        }
        
        $user_key = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
        $pass_key = 'b546a6dfc4';

        if(
            $user_key!=$_POST['user_key'] ||
            $pass_key!=$_POST['pass_key']
        ){
            echo json_encode(false);
            return;
        }
            
        extract($_POST);

        $absen_wajah = $this->db
                            ->where('pegawai_id', $pegawai_id)
                            ->where('jenis_pegawai', $jenis_pegawai)
                            ->where('tahun', date('Y'))
                            ->where('bulan', date('m'))
                            ->where('hari', date('d'))
                            ->get('tb_absen_wajah')
                            ->row();

        $dataAbsen = $this->getJamAbsen($pegawai_id);

        if(!$dataAbsen){
            echo json_encode(false);
            return;
        }
        

        if($absen_wajah){
            $dataAbsen['updated_at'] = date("Y-m-d H:i:s");
            $this->db->where('id', $absen_wajah->id)->update('tb_absen_wajah', $dataAbsen);
        }else{
            $data = [
                'pegawai_id'        => $pegawai_id,
                'jenis_pegawai'     => $jenis_pegawai,
                'skpd_id'           => $skpd_id,
                'tahun'             => date("Y"),
                'bulan'             => date("m"),
                'hari'              => date("d"),
            ];
            $data = array_merge($data, $dataAbsen);
            $this->db->insert('tb_absen_wajah', $data);
        }
        
        echo json_encode(true);
        return;

    }
    
    public function test(){
        echo "<pre>";
        $data1 = ["a"=>1];
        $data2 = ["b"=>2];
        print_r(array_merge($data1, $data2));
        return;
    }
    
    private function getJamAbsen($pegawai_id){

        $jam_kerja_pegawai = $this->db->where('pegawai_id', $pegawai_id)->get('tb_jam_kerja_pegawai')->row();
        if($jam_kerja_pegawai){
            $jam_kerja      = $this->db
                                   ->where('jam_kerja_id', $jam_kerja_pegawai->jam_kerja_id)
                                   ->where('hari', date('w'))
                                   ->get('tb_jam_kerja_meta')
                                   ->row();
        }else{
            $jam_kerja      = $this->db
                                   ->where('jam_kerja_id', 1)
                                   ->where('hari', date('w'))
                                   ->get('tb_jam_kerja_meta')
                                   ->row();
        }
        

        $now                    = strtotime(date("H:i:s"));
        $jam_awal_masuk         = strtotime($jam_kerja->jam_awal_masuk);
        $jam_akhir_masuk        = strtotime($jam_kerja->jam_akhir_masuk);
        $jam_awal_pulang        = strtotime($jam_kerja->jam_awal_pulang);
        $jam_akhir_pulang       = strtotime($jam_kerja->jam_akhir_pulang);
        $jam_awal_istirahat     = $jam_kerja->jam_awal_istirahat ? strtotime($jam_kerja->jam_awal_istirahat) : null;
        $jam_akhir_istirahat    = $jam_kerja->jam_akhir_istirahat ? strtotime($jam_kerja->jam_akhir_istirahat) : null;
        

        if($now >= $jam_awal_masuk && $now<=($jam_akhir_masuk+3600)){
            return ['jam_masuk'=>date("H:i:s", $now)];
        }
        if($jam_awal_istirahat && $jam_akhir_istirahat && $now >= $jam_awal_istirahat && $now<=$jam_akhir_istirahat){
            return ['jam_istirahat'=>date("H:i:s", $now)];
        }
        if($now >= ($jam_awal_pulang-3600) && $now<=$jam_akhir_pulang){
            return ['jam_pulang'=>date("H:i:s", $now)];
        }
        
        return false;        
    }

    
}
