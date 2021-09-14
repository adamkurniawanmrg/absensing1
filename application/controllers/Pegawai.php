<?php
defined('BASEPATH') OR exit('No direct script access allowed');
# Includes the autoloader for libraries installed with composer

# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

class Pegawai extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model(['Pegawai_model','Unitkerja_model','Skpd_model']);
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
    }

    public function index(){
		$data = [
		    "title"             => "Data Pegawai",
			"page"				=> "pegawai/datapegawai",
			"skpd"              => $this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==2 ? $this->Skpd_model->getSkpd() : $this->Unitkerja_model->get($this->session->userdata('skpd_id')),
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
    public function tks(){
		$data = [
		    "title"             => "Data TKS",
			"page"				=> "pegawai/datatks",
			"skpd"              => $this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==2 ? $this->Skpd_model->getSkpd() : $this->Unitkerja_model->get($this->session->userdata('skpd_id')),
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
    public function ubahpegawai($pegawai_id='none'){
        $listroleAcc = [1,2];

        $datapegawai        = $this->Pegawai_model->getPegawai($pegawai_id);
        $unitkerja          = $this->Unitkerja_model->get($this->session->userdata('skpd_id'));
        
        if(!$datapegawai){
             $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Redirected!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
             redirect('pegawai?token=' . $_GET['token']);
            return;
        }
        $listskpd_id = array();
        foreach($unitkerja as $uj){
            $listskpd_id[] = $uj['skpd_id'];
        }
        if(!in_array($this->session->userdata('role_id'), $listroleAcc) && !in_array($datapegawai[0]['skpd_id'], $listskpd_id)){
             $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Redirected!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
             redirect('pegawai?token=' . $_GET['token']);
            return;            
        }
        

        $pegawai            = $datapegawai[0];
	    $pegawaiMeta        = $this->db->where('pegawai_id', $pegawai['user_id'])->get('tb_pegawai_meta')->row_array();

        $gelarDepan         = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
        $gelarBelakang      = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
        $pegawai['nama']    = $gelarDepan.$pegawai['nama'].$gelarBelakang;
        

        $this->form_validation->set_rules('jabatan_golongan', 'Jabatan/Golongan', 'required');
        $this->form_validation->set_rules('jabatan_opd', 'Jabatan OPD', 'required');
        $this->form_validation->set_rules('jabatan_perbub_tpp', 'Jabatan Pada Perbub TPP', 'required');
        $this->form_validation->set_rules('opd_id', 'OPD', 'required');
        if(isset($_POST['kordinat_khusus']) && $_POST['kordinat_khusus']=="Ya"){
            $this->form_validation->set_rules('kordinats[]', 'Kordinats', 'required');
        }
        if ($this->input->post('plt') && $this->input->post('plt') == 1) {
            $this->form_validation->set_rules('jabatan_rangkap_perbub', 'Jabatan PLT', 'required');
        }
		if($this->form_validation->run()){
            $data = [
                "pegawai_id"                => $pegawai_id,
                "nama"                      => $pegawai['nama'],
                "nip"                       => $pegawai['username'],
                "jabatan_golongan"          => $this->input->post('jabatan_golongan'),
                "jabatan_opd"               => $this->input->post('jabatan_opd'),
                "jabatan_perbub_tpp"        => $this->input->post('jabatan_perbub_tpp'),
                "opd_id"                    => $this->input->post('opd_id'),
                "plt"                       => $this->input->post('plt')==1 ? $this->input->post('plt') : null,
                "jabatan_rangkap_perbub"    => $this->input->post('plt')==1 ? $this->input->post('jabatan_rangkap_perbub') : null,
                "cpns"                      => $this->input->post('cpns') && $this->input->post('cpns')==1 ? $this->input->post('cpns') : null,
                "guru_sertifikasi"          => $this->input->post('guru_sertifikasi') && $this->input->post('guru_sertifikasi')==1 ? $this->input->post('guru_sertifikasi') : null,
            ];		    
            
            if($this->session->userdata('role_id')==1){
                $data["kordinat_bebas"] = $this->input->post('kordinat_bebas');
                $data["kordinat_khusus"] = $this->input->post('kordinat_khusus');
                if($this->input->post('kordinat_khusus')=="Ya"){
                    $data["kordinats"]   = serialize($this->input->post('kordinats'));
                }
            }
            
		    if($pegawaiMeta){
                $this->db->where('pegawai_id', $pegawai_id)->update('tb_pegawai_meta', $data);
		    }else{
                $this->db->insert('tb_pegawai_meta', $data);		        
		    }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data pegawai <strong>'.$pegawai['nama'].'</strong> berhasil diubah!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
            redirect('pegawai?token=' . $_GET['token']);
            return;
		}
        $kordinats              = $this->db->where('skpd_id', $this->session->userdata('skpd_id'))->order_by('nama_kordinat', 'asc')->get('tb_kordinat_tambahan')->result_array();
		$data = [
		    "title"             => "Ubah Data Pegawai",
			"page"				=> "pegawai/ubahpegawai",
			"pegawai"           => $pegawai,
			"jabatangolongan"   => $this->db->where('deleted', null)->get('tb_jabatan_golongan')->result_array(),
			"jabatanpenghasilan"=> $this->db->where('deleted', null)->get('tb_jabatan_penghasilan')->result_array(),
			"pegawaiMeta"       => $pegawaiMeta,
			"kordinats"         => $kordinats,
			"skpd"              => in_array($this->session->userdata('role_id'), $listroleAcc) ? $this->Skpd_model->getSkpd(true) : $unitkerja
		];
		
	
		$this->load->view('template/default', $data);
    }

    public function ubahtks($tks_id='none'){
        $datatks        = $this->Pegawai_model->getPegawaiTks($tks_id);
        
        if(!$datatks){
             $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Redirected!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
             redirect('pegawai/tks?token=' . $_GET['token']);

        }

        $tks            = $datatks[0];
	    $tksMeta        = $this->db->where('tks_id', $tks['user_id'])->get('tb_tks_meta')->row_array();

        $this->form_validation->set_rules('opd_id', 'OPD', 'required');
        $this->form_validation->set_rules('gaji', 'Gaji', 'required');
        if(isset($_POST['kordinat_khusus']) && $_POST['kordinat_khusus']=="Ya"){
            $this->form_validation->set_rules('kordinats[]', 'Kordinats', 'required');
        }
        if ($this->input->post('plt') && $this->input->post('plt') == 1) {
            $this->form_validation->set_rules('jabatan_rangkap_perbub', 'Jabatan PLT', 'required');
        }
		if($this->form_validation->run()){
            $data = [
                "tks_id"                    => $tks_id,
                "nama"                      => $tks['nama'],
                "nik"                       => $tks['username'],
                "gaji"                      => $this->input->post('gaji'),
                "opd_id"                    => $this->input->post('opd_id'),
            ];
            if($this->session->userdata('role_id')==1){
                $data["kordinat_bebas"] = $this->input->post('kordinat_bebas');
            }
            if($this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==3){
                $data["kordinat_khusus"] = $this->input->post('kordinat_khusus');
                if($this->input->post('kordinat_khusus')=="Ya"){
                    $data["kordinats"]   = serialize($this->input->post('kordinats'));
                }
            }

		    if($tksMeta){
                $this->db->where('tks_id', $tks_id)->update('tb_tks_meta', $data);
		    }else{
                $this->db->insert('tb_tks_meta', $data);		        
		    }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data pegawai berhasil diubah!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
            redirect('pegawai/tks?token=' . $_GET['token']);
            return;
		}
		
        $kordinats              = $this->db->where('skpd_id', $this->session->userdata('skpd_id'))->order_by('nama_kordinat', 'asc')->get('tb_kordinat_tambahan')->result_array();
		$data = [
		    "title"             => "Ubah Data TKS",
			"page"				=> "pegawai/ubahtks",
			"pegawai"           => $tks,
			"tksMeta"           => $tksMeta,
			"kordinats"         => $kordinats,
			"gskpd"             => $this->Skpd_model->getSkpd(true),
			"skpd"              => $this->Skpd_model->getSkpd()
		];
		
	
		$this->load->view('template/default', $data);
    }


    public function setfaceabsen($pegawai_id='none'){
        $akses           = [1];
        $is_akses      =  in_array($this->session->userdata('role_id'), $akses);
        
        $pegawai = $this->Pegawai_model->getPegawai($pegawai_id)[0];
        if(!$pegawai || !$is_akses){
            redirect('pegawai?token=' . $_GET['token']);
            return;
        }

        $all_files = glob("file_model/".$pegawai['username']."/*.*");

		$data = [
		    "title"             => "Face Absen ".$pegawai['nama'],
			"page"				=> "pegawai/setfaceabsen",
			"pegawai"           => $pegawai,
			"skpd"              => $this->Skpd_model->getSkpd(),
			"all_files"         => $all_files
		];
		
		
		$this->load->view('template/default', $data);
    }
    public function setfaceabsentks($pegawai_id='none'){
		
		$pegawai = $this->Pegawai_model->getPegawaiTks($pegawai_id)[0];
		
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

		$all_files = $bucket->objects([
			'prefix' => 'file_model/'.$pegawai['username'],
    		'fields' => 'items/name'
		]);

        $pegawai = $this->Pegawai_model->getPegawaiTks($pegawai_id)[0];
        $akses           = [1];
        $is_akses      =  in_array($this->session->userdata('role_id'), $akses);

        if(!$pegawai || !$is_akses){
            redirect('pegawai?token=' . $_GET['token']);
            return;
        }
        
        // if($this->session->userdata('role_id')==1){
        //     echo "<pre>";
        //     print_r($all_files);
        //     return;
        // }
		$data = [
		    "title"             => "Face Absen TKS - ".$pegawai['nama'],
			"page"				=> "pegawai/setfaceabsentks",
			"pegawai"           => $pegawai,
			"skpd"              => $this->Skpd_model->getSkpd(),
			"all_files"         => $all_files
		];
		
		
		$this->load->view('template/default', $data);
    }
    
    function saveFace($data)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://34.101.95.18/save-face",
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
    
    function savefaceabsen($nip){
        $akses          = [1];
        $is_akses       =  in_array($this->session->userdata('role_id'), $akses);

        if(!$is_akses){
            redirect('pegawai?token=' . $_GET['token']);
            return;
        }
        
        
        $saveFace = $this->saveFace(json_encode(['username'=>$nip,'files'=>$_POST['file']]));
        $saveFace = json_decode($saveFace);
        
        if($saveFace->status == 'fail')
        {
            echo json_encode($saveFace);
            return;
        }
        

        if (!is_dir('file_model/'.$nip)) {
            // rmdir('file_model/'.$nip);
            mkdir('file_model/'.$nip, 0777);
        }
        
        $files = glob('file_model/'.$nip.'/*');
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }


        foreach($_POST['file'] as $key => $img)
        {
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $fileData = base64_decode($img);
            //saving
            $jam = date("Y-m-d H:i:s");
            $fileName = 'file_model/'.$nip.'/'.strtotime('now').$key.'.png';
            file_put_contents($fileName, $fileData);
        }
        // $this->upload_sample(json_encode(['username'=>$nip,'data'=>$data['detections']]));
        echo json_encode($saveFace);
        return;
    }
    function savefaceabsentks($nik){
        $akses          = [1];
        $is_akses       =  in_array($this->session->userdata('role_id'), $akses);

        if(!$is_akses){
            redirect('pegawai?token=' . $_GET['token']);
            return;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        // file_put_contents("samples/".$nik.".json", "models=".json_encode($data['detections']).";models=models.map(arr => new Float32Array(Object.values(arr)))");
        file_put_contents("samples/".$nik.".json", json_encode($data['detections']));
        if (!is_dir('file_model/'.$nik)) {
            // rmdir('file_model/'.$nik);
            mkdir('file_model/'.$nik, 0777);
        }
        
        $files = glob('file_model/'.$nik.'/*');
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }


        foreach($data['images'] as $key => $img)
        {
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $fileData = base64_decode($img);
            //saving
            $jam = date("Y-m-d H:i:s");
            $fileName = 'file_model/'.$nik.'/'.($key+1).'.png';
            file_put_contents($fileName, $fileData);
        }
        
        $this->upload_sample(json_encode(['username'=>$nik,'data'=>$data['detections']]));
        echo 1;
        
    }
    
    function upload_sample($data)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://34.101.95.18/save-samples",
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
