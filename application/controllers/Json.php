<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Json extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model(['Pegawai_model','Skpd_model']);
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
    }

    public function index(){
        return;
    }

    public function selectOptionPegawaiAtasan(){
        if(isset($_POST['skpd_id']) && $_POST['skpd_id']){
            $pegawai = $this->Pegawai_model->getPegawai(null, $_POST['skpd_id']);
            if($pegawai && count($pegawai)>0){
                echo "<option value=''>-- Pilih Pegawai Atasan --</option>";
                foreach ($pegawai as $p) {
                    $nama = ($p['gelar_depan'] && $p['gelar_depan']!="" ? $p['gelar_depan'].". " : null).$p['nama'].($p['gelar_belakang'] && $p['gelar_belakang']!="" ? ", ".$p['gelar_belakang'] : null);
                    echo "<option value='" . $p['user_id'] . "'>" . $nama . "</option>";
                }
                return;
            }
        }elseif(isset($_POST['skpd_id'])){
            echo "<option value=''>-- Pilih Pegawai Atasan --</option>";
            return;

        }
        echo "<option value=''>-- Tidak ada data --</option>";
        return;
    }
    public function selectOptionPegawaiBySkpd(){
        if(isset($_POST['skpd_id']) && $_POST['skpd_id']){
            $pegawai = $this->Pegawai_model->getPegawai(null, $_POST['skpd_id']);
            $label = isset($_GET['is_single']) ? "Pilih Pegawai" : "Semua Pegawai";
            if($pegawai && count($pegawai)>0){
                echo "<option value=''>".$label."</option>";
                foreach ($pegawai as $p) {
                    $nama = ($p['gelar_depan'] && $p['gelar_depan']!="" ? $p['gelar_depan'].". " : null).$p['nama'].($p['gelar_belakang'] && $p['gelar_belakang']!="" ? ", ".$p['gelar_belakang'] : null);
                    echo "<option value='" . $p['user_id'] . "'>" . $nama . "</option>";
                }
                return;
            }
        }elseif(isset($_POST['skpd_id'])){
            echo "<option value=''>".$label."</option>";
            return;

        }
        echo "<option value=''>-- Tidak ada data --</option>";
        return;
    }

    public function selectOptionTksBySkpd(){
        if(isset($_POST['skpd_id']) && $_POST['skpd_id']){
            $pegawai = $this->Pegawai_model->getPegawaiTks(null, $_POST['skpd_id']);
            if($pegawai && count($pegawai)>0){
                echo "<option value=''>-- Semua Pegawai --</option>";
                foreach ($pegawai as $p) {
                    echo "<option value='" . $p['user_id'] . "'>" . $p['nama'] . "</option>";
                }
                return;
            }
        }elseif(isset($_POST['skpd_id'])){
            echo "<option value=''>-- Semua Pegawai --</option>";
            return;

        }
        echo "<option value=''>-- Tidak ada data --</option>";
        return;

    }

    public function getDataPegawai(){
        if(isset($_POST['skpd_id'])){
            $akses2          = [1,2,3];
            $hak_akses2      = in_array($this->session->userdata('role_id'), $akses2);
            
            $skpd_id         = $hak_akses2 ? $_POST['skpd_id'] : $this->session->userdata('skpd_id');
    
            $pegawai        = $this->Pegawai_model->getPegawai(null, $skpd_id);
            $pegawaiMeta    = $this->db
                                   ->select('
                                                tb_pegawai_meta.pegawai_id,
                                                tb_pegawai_meta.opd_id,
                                                tb_pegawai_meta.jabatan_opd,
                                                tb_jabatan_golongan.nama_golongan,
                                                jabatan.nama_jabatan,
                                                jabatanplt.nama_jabatan nama_jabatan_plt
                                    ')
                                    ->join('tb_jabatan_golongan', 'tb_jabatan_golongan.id=tb_pegawai_meta.jabatan_golongan', 'left')
                                    ->join('tb_jabatan_penghasilan as jabatan', 'jabatan.id=tb_pegawai_meta.jabatan_perbub_tpp', 'left')
                                    ->join('tb_jabatan_penghasilan as jabatanplt', 'jabatanplt.id=tb_pegawai_meta.jabatan_rangkap_perbub', 'left')
                                    ->get('tb_pegawai_meta')
                                    ->result_array();
            $skpd           = $this->Skpd_model->getSkpd(true);
            $pegawaiMetas = array();

            $akses          = [1];
            $is_akses       =  in_array($this->session->userdata('role_id'), $akses);


            foreach($pegawaiMeta as $pgm){
                $pegawaiMetas[$pgm['pegawai_id']] = $pgm;
            }
            if($pegawai){
                $datapegawai = array();
                $no=1;
                $faceSaved      = 0;
                $faceUnsaved    = 0;
                foreach($pegawai as $p){
                    $all_files  = count(glob("file_model/".$p['username']."/*.*"));
                    $status     = '<button class="label btn-primary">'.$all_files.'</button>';
                    $status     = $all_files>0 ? $status : '<button class="label btn-danger">'.$all_files.'</button>';
                    if($all_files>0){
                        $faceSaved++;
                    }else{
                        $faceUnsaved++;
                    }
                    $pm = isset($pegawaiMetas[$p['user_id']]) ? $pegawaiMetas[$p['user_id']] : array();
                    $sk = isset($skpd[(isset($pm['opd_id']) ? $pm['opd_id'] : "none")]) ? $skpd[$pm['opd_id']] : array();
                    $pg = array();
                    // $pg[] = $no;$no++;
                    $pg[] = ($p['gelar_depan'] && $p['gelar_depan']!="" ? $p['gelar_depan'].". " : null).$p['nama'].($p['gelar_belakang'] && $p['gelar_belakang']!="" ? ", ".$p['gelar_belakang'] : null);
                    $pg[] = $p['username']; 
                    // $pg[] = isset($pm['nama_golongan']) ? $pm['nama_golongan'] : null; 
                    $pg[] = isset($pm['opd_id']) && isset($sk['id_skpd']) && $pm['opd_id']==$p['skpd_id'] ? 
                            $sk['nama_skpd'] : 
                            ((isset($sk['nama_skpd']) ? 
                                $sk['nama_skpd']."<br>" : null)."<small>Unit Kerja : ".$p['nama_skpd']."</small>"); 
                    // $pg[] = isset($pm['jabatan_opd']) ? $pm['jabatan_opd'] : null; 
                    // $pg[] = isset($pm['nama_jabatan']) ? (strlen($pm['nama_jabatan'])>30 ? substr($pm['nama_jabatan'],0 ,30)."...<em title='".$pm['nama_jabatan']."' style='cursor:pointer;' class='ti-help-alt'></em>" : $pm['nama_jabatan']) : null; 
                    // $pg[] = isset($pm['nama_jabatan_plt']) ? (strlen($pm['nama_jabatan_plt'])>30 ? substr($pm['nama_jabatan_plt'],0 ,30)."...<em title='".$pm['nama_jabatan_plt']."' style='cursor:pointer;' class='ti-help-alt'></em>" : $pm['nama_jabatan_plt']) : null; 

                    $pg[] = ($is_akses ? "<a href='pegawai/setfaceabsen/".$p['user_id']."?token=".$_GET['token']."' class='btn btn-info'><em class='ti-user'></em> Atur Absensi Wajah ".$status."</a>" : null)."
                            <a href='pegawai/ubahpegawai/".$p['user_id']."?token=".$_GET['token']."' class='btn btn-warning'><em class='ti-pencil-alt'></em> Ubah</a>
                            "; 

                    $datapegawai[] = $pg; 
                } 
                echo json_encode(['data' => $datapegawai, 'totalSaved'=>$faceSaved, 'totalUnsaved'=>$faceUnsaved]);
                return;
            }
            echo json_encode(['data'=>array()]);
            return;
        }

    }
    public function getDataTks(){
        if(isset($_POST['skpd_id']) && isset($_POST['pegawai_id'])){
            $akses           = [1,2,3]; 
            $hak_akses       = in_array($this->session->userdata('role_id'), $akses);
            $akses2          = [1,2]; 
            $hak_akses2      = in_array($this->session->userdata('role_id'), $akses2);
            
            $skpd_id         = $hak_akses2 ? $_POST['skpd_id'] : $this->session->userdata('skpd_id');
            $pegawai_id      = $hak_akses  ? $_POST['pegawai_id'] : $this->session->userdata('user_id');

            $akses          = [1];
            $is_akses       =  in_array($this->session->userdata('role_id'), $akses);

            $pegawai        = $this->Pegawai_model->getPegawaiTks($pegawai_id, $skpd_id);
            if($pegawai){
                $datapegawai = array();
                $no=1;
                $faceSaved      = 0;
                $faceUnsaved    = 0;
                foreach($pegawai as $p){
                    $pg         = array();
                    $all_files  = count(glob("file_model/".$p['username']."/*.*"));
                    $status     = '<button class="label btn-primary">'.$all_files.'</button>';
                    $status     = $all_files>0 ? $status : '<button class="label btn-danger">'.$all_files.'</button>';
                    if($all_files>0){
                        $faceSaved++;
                    }else{
                        $faceUnsaved++;
                    }

                    // $pg[] = $no;$no++;
                    $pg[] = $p['nama']; 
                    $pg[] = $p['username']; 
                    $pg[] = $p['nama_skpd']; 
                    $pg[] = ($is_akses ? "<a href='pegawai/setfaceabsentks/".$p['user_id']."?token=".$_GET['token']."' class='btn btn-info'><em class='ti-user'></em> Atur Absensi Wajah ".$status."</a>" : null)."
                            <a href='pegawai/ubahtks/".$p['user_id']."?token=".$_GET['token']."' class='btn btn-warning'><em class='ti-pencil-alt'></em> Ubah</a>
                            "; 

                    $datapegawai[] = $pg; 
                } 
                echo json_encode(['data' => $datapegawai, 'totalSaved'=>$faceSaved, 'totalUnsaved'=>$faceUnsaved]);
                return;
            }
            echo json_encode(['data'=>array()]);
            return;
        }

    }

    
}
