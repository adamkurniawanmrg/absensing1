<?php
defined('BASEPATH') OR exit('No direct script access allowed');
# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

class Absen extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
		$this->load->model([
            'LogAbsen_model',
            'Pegawai_model',
            'Sms_model',
            'Skpd_model',
            'AbsenManual_model'
        ]);
    }
    
    public function test()
    {
        return $this->load->view('absen/test');
    }
    
    public function absensiwajah()
    {
        // $data = $this->db->get('tb_absensi')->result();
        $data = [
		    "title"             => "Absensi",
			"page"				=> "absen/index",
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
    
    function getDataAbsen(){
        $akses = [1,2,3]; 
        $akses2 = [1,2]; 
        $hak_akses = in_array($this->session->userdata('role_id'), $akses);
        $hak_akses2= in_array($this->session->userdata('role_id'), $akses2);
        
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");

        $this->db->
            where("tb_absensi.pegawai_id", $this->session->userdata('user_id'))->
            where("tb_absensi.jenis_pegawai", $this->session->userdata('jenis_pegawai'))->
            where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
            where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
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
            $indexAprover   = $field['aproved_by'] ? array_search($field['aproved_by'], array_column($pegawais, 'user_id')) : null;
            

            $pegawai        = $field['jenis_pegawai']=='pegawai' ? 
                              (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                              (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
            
            $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];
    
            $row = array();

            $row[0] = $no;
            $row[1] = $this->hari[date("w", strtotime($field['created_at']))] . ", " . date('d F Y', strtotime($field['created_at']));

            $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
            $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

            $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
            $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
            $totimeDisetujuiPada    = strtotime($field['aproved_at']);
            $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";
            
            $row[2] = $gelarDepan.$pegawai['nama'].$gelarBelakang;
            $row[3] = $skpd['nama_skpd'];
            $row[4] = $field['jenis_absen'];
            $row[5] = date('H:i:s',strtotime($field['jam']));
            $row[6] = '

            <a href="' . site_url('absenmanual/deletemanual/' .  $field['id']) . '?token=' . $_GET['token'] . '" onclick="alert(\'belum boleh\'); return false; return confirm(\'Yakin hapus data?\')"  class="btn btn-danger btn-sm" style="padding: 5px 15px" title="Hapus"><i class="fa fa-trash"></i> Hapus</a>';



            $no++;
            $data[] = $row;

        }
        $output = array(
            "data" => $data
        );
        echo json_encode($output);
    }

    public function wajah(){
        $skpd_id = $_SESSION['skpd_id'];
        $koordinat = $this->db->get_where('tb_kordinat',['skpd_id'=>$skpd_id])->row();
        $is_manual = $_GET['is_manual'];
        $kategori = $_GET['kategori'];
        if($is_manual=='true'){
            if($kategori == 'Absen Upacara' || $kategori == 'Absen Senam'){
                if($kategori == 'Absen Upacara'){
                    $data['keterangan'] = "Absen Senam";
                    $upacara = $this->db->where('tanggal', date('Y-m-d'))->get('tb_upacara_libur')->row();
                    if(!$upacara){
                        redirect('home?token='.$_GET['token']);
                    }
                    $data['keterangan'] = $upacara->nama_hari;
                }                
                $this->load->view('absen/wajah_manual2', $data);
            }else{
                $this->load->view('absen/wajah_manual');
            }
        }else{
		    $this->load->view('absen/wajah',['koordinat'=>$koordinat]);
        }
    }
    
    public function absen_berhasil(){
		$data = [
		    "title"             => "Absensi Berhasil",
			"page"				=> "absen/absen_berhasil",
			"absensi"           => $this->db->get_where('tb_absensi',['id'=>$_GET['id']])->row()
		];
		
		$this->load->view('template/default', $data);
    }

    public function pushAbsen(){
        // $fr_log = file_get_contents('fr_log.txt');
        $line = $_SESSION['username'].' started at '.date('Y-m-d H:i:s')."\r\n";
        $img = $_POST['file_absensi'];
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $fileData = base64_decode($img);
        
        $face_recognition = $this->faceRecognition(json_encode(['username'=>$_SESSION['username'],'fileData'=>$_POST['file_absensi']]));
        $result = json_decode($face_recognition);
        if($result->_label == "unknown" || $result->status == 'fail')
        {
            $line .= $_SESSION['username'].' fail at '.date('Y-m-d H:i:s')."\r\n";
            $line .= $_SESSION['username'].' distance at '.($result->_distance??-1)."\r\n";
            $line .= $_SESSION['username'].' finish at '.date('Y-m-d H:i:s')."\r\n";
            // file_put_contents('fr_log.txt',$fr_log.$line,FILE_APPEND);
            echo json_encode([
                'status' => 'fail',
                'message' => 'Absensi gagal',
                'face_recognition' => $result 
            ]);
            return;
        }
        
        $line .= $_SESSION['username'].' success at '.date('Y-m-d H:i:s')."\r\n";
        $line .= $_SESSION['username'].' distance at '.$result->_distance."\r\n";
        $line .= $_SESSION['username'].' finish at '.date('Y-m-d H:i:s')."\r\n";
        
        // file_put_contents('fr_log.txt',$fr_log.$line,FILE_APPEND);
        
		//saving
		# Your Google Cloud Platform project ID
		$projectId = 'absensi-325704';

		# Instantiates a client
		$storage = new StorageClient([
			'projectId' => $projectId
		]);

		# The name for the new bucket
		$bucketName = 'file-absensi';

		# Creates the new bucket
		$bucket = $storage->bucket($bucketName);

        $jam = date("Y-m-d H:i:s");
        $fileName = 'file_absensi/'.$_SESSION['username'].'/'.$jam.'.png';
        // if(!file_exists('file_absensi/'.$_SESSION['username']))
        //     mkdir('file_absensi/'.$_SESSION['username'], 0777);
		// file_put_contents($fileName, $fileData);
		$options = [
			'resumable' => true,
			'name' => $fileName,
			'metadata' => [
				'contentLanguage' => 'en'
			]
		];
		$object = $bucket->upload(
			$fileData,
			$options
		);
        $access_key         = rand(90000,99999)."-".substr(md5(time()), 0, 7);
        $this->db->insert("tb_absensi", [
            "pegawai_id"        => $this->session->userdata('user_id'),
            "jenis_pegawai"     => $this->session->userdata('jenis_pegawai'),
            "nama_pegawai"      => $this->session->userdata('nama'),
            "skpd_id"           => $this->session->userdata('skpd_id'),
            "nama_opd"          => $this->session->userdata('nama_opd'),
            "jam"               => $jam,
            "jenis_absen"       => $_POST['jenis_absen'],
            "keterangan"        => isset($_POST['keterangan']) && $_POST['keterangan'] ? $_POST['keterangan'] : null,
            "status"            => isset($_POST['keterangan']) && $_POST['keterangan'] ? null : 1,
            "access_key"        => isset($_POST['keterangan']) && $_POST['keterangan'] ? $access_key : null,
            "file_absensi"      => $fileName
        ]);
        
        $id = $this->db->insert_id();

        if(isset($_POST['keterangan']) && $_POST['keterangan']) {
            $pegawai_id         = $this->session->userdata('user_id');
            $jenis_pegawai      = $this->session->userdata('jenis_pegawai');
            $pegawai            = $this->Pegawai_model->getPegawaiAtasan($pegawai_id, $jenis_pegawai);

            if(isset($pegawai['nama_pegawai'])){

                $fileNameEncoded= preg_replace('/ /i', '%20', $fileName);

                $pesan          = "*[ABSENSI-NG]*\n\nAda permohonan Absen Manual (".$_POST['jenis_absen'].") dari *".$pegawai['nama_pegawai']."* dengan alasan : *".$_POST['keterangan']."*\n\n*Lampiran :*\n".base_url($fileNameEncoded)."\n\n*Setujui dengan tap link ini :*\n".base_url('byaccesskey/setujuiabsenmanual/'.$id."/".$access_key)."\n\n*Tolak dengan tap link ini:*\n".base_url('byaccesskey/tolakabsenmanual/'.$id."/".$access_key);
                $this->Sms_model->send($pegawai['no_hp_pegawai_atasan'], $pesan);
            }
        }

        echo json_encode([
            'status'            => 'success',
            'id'                => $id,
            'message'           => 'Absensi berhasil',
            'face_recognition'  => $result 
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

    public function faceRecognition($data)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://34.101.95.18/face-absen",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }
    }
}
