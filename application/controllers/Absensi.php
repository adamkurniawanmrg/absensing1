<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Google\Cloud\Storage\StorageClient;

class Absensi extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
		$this->load->model([
            'LogAbsen_model',
            'Pegawai_model',
            'Skpd_model',
            'Sms_model',
            'AbsenManual_model'
        ]);
    }
    
    public function index()
    {
        $data = [
		    "title"             => "Absensi Harian Pegawai",
			"page"				=> "absensi/absensiharian",
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
    
    public function foto()
    {
        $data = [
		    "title"             => "Foto Absensi Harian Pegawai",
			"page"				=> "absensi/fotoabsensiharian",
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
    public function getFotoAbsensiHarianPegawai(){
        if (!isset($_POST['tanggal']) || 
            $_POST['tanggal'] == "") {
            return;
        }
        
        extract($_POST);

        $jenis_pegawai  = $jenis_pegawai=="" ? null : $jenis_pegawai;
        $skpd_id        = $skpd_id=="" ? null : $skpd_id;

        $akses           = [1,2];
        $skpd_id         = in_array($this->session->userdata('role_id'), $akses) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id');
        $tanggal         = date("Y-m-d", strtotime($_POST['tanggal']));
        $skpd            = $this->Skpd_model->getSkpd();
        $datas = array();
        $pegawai     =  $jenis_pegawai==null ? array_merge($this->Pegawai_model->getPegawai(null, $skpd_id), $this->Pegawai_model->getPegawaiTks(null, $skpd_id)) : ($jenis_pegawai == 'pegawai' ? 
                        $this->Pegawai_model->getPegawai(null, $skpd_id) : 
                        $this->Pegawai_model->getPegawaiTks(null, $skpd_id));
                        
        array_multisort(array_column($pegawai, 'nama'), SORT_ASC, $pegawai);
        $no=1;
        $jumlah = 0;
        foreach ($pegawai as $pg) {
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
				'prefix' => "file_absensi/".$pg['username']."/".$tanggal,
				'fields' => 'items/name'
			]);
            // $all_files = glob("file_absensi/".$pg['username']."/*.*");
            // for ($i=0; $i<count($all_files); $i++){
			foreach($all_files as $key => $file){
                $image_name = $file->name();
                $supported_format = array('gif','jpg','jpeg','png');
                $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                if (in_array($ext, $supported_format) && strpos(basename($image_name), date("Y-m-d", strtotime($tanggal)))!==false){
                    echo "<div class='col-md-3 col-lg-2' style='margin-bottom: 15px;'>";
                    echo '<img src="https://storage.googleapis.com/file-absensi/'.$image_name.'" width="100%" alt="'.$image_name.'" class="img-thumbnail" />
                            <div style="margin-bottom: 0px;margin-top: 0px;max-width: 100%;overflow: hidden;white-space: nowrap;">
                                <center>
                                    <div style="font-weight: 700;font-size: 12px">'.$pg['nama'].'</div>
                                    <div style="font-weight: 300;font-size: 12px">'.$pg['nama_skpd'].'</div>
                                </center>
                            </div>
                            
                            
                            <div style="margin-bottom: 3px; font-size: 12px; background: #cef5d0;">
                                <center>
                                    <small>'.basename($image_name).'</small>
                                </center>
                            </div>';
                    echo '<div style="margin-top: 0px;margin-bottom:10px;background:#F4A460">
                            <center>
                                <small>
                                    <a href="'.base_url('absensi/pesan/'.(isset($pg['tks_id']) ? 'tks' : 'pegawai').'/'.$pg['user_id'].'/'.$i .'?token='.$_GET['token'].'').'" style=" text-decoration: none;color:white;" >Pesan</a>
                                </small>
                            </center>
                        </div>';
                  
                    echo "</div>";
                    $jumlah++;
                }else{
                    continue;
                }
            }
        }
        if($jumlah==0){
            echo '<h3 align="center">Tidak ada foto absensi!</h3>';
        }
    }
    
    
    public function pesan($jenis_pegawai, $pegawai_id, $index)
    {
        if($jenis_pegawai=="tks"){
            $pegawai = $this->Pegawai_model->getPegawaiTks($pegawai_id, $skpd_id);
        }else{
            $pegawai = $this->Pegawai_model->getPegawai($pegawai_id, $skpd_id);
        }
        
        if(isset($pegawai[0]) && $pegawai[0]){
            $pegawaiMeta = $this->Pegawai_model->getPegawaiMeta($pegawai_id, $jenis_pegawai);
            $pegawai  = $pegawai[0];
            
    
            $all_files = glob("file_absensi/".$pegawai['username']."/*.*");
            $image_name = $all_files[$index];
            $tanggal = explode('/',$image_name);
            $tanggal = explode('.', $tanggal[2]);
            $tanggal = strtotime($tanggal[0]);  
            
            if($pegawaiMeta){
                $no_hp = $pegawaiMeta['no_hp'];
                $pesan      = "*[Halo, ".$pegawai['nama'] ."]* \n\nAnda terdeksi melakukan manipulasi absensi, pada tanggal : " .date('d-m-Y', $tanggal)." pukul ".date('H:i', $tanggal).". Tindakan tersebut dapat dikenakan sanksi disiplin. \n\nBerikut bukti manipulasi : \n". base_url(str_replace(' ','%20',$image_name)); 
                $this->Sms_model->send($no_hp, $pesan);
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Pesan berhasil dikirim</div>');
                redirect('absensi/foto?token=' . $_GET['token']);
            }elseif($pegawai){
                $no_hp = $pegawai['no_hp'];
                $pesan      = "*[Halo, ".$pegawai['nama'] ."]* \n\nAnda terdeksi melakukan manipulasi absensi, pada tanggal : " .date('d-m-Y', $tanggal)." pukul ".date('H:i', $tanggal).". Tindakan tersebut dapat dikenakan sanksi disiplin. \n\nBerikut bukti manipulasi : \n". base_url(str_replace(' ','%20',$image_name)); 
                $this->Sms_model->send($no_hp, $pesan);
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Pesan berhasil dikirim</div>');
                redirect('absensi/foto?token=' . $_GET['token']);
                
            }else{
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Pesan gagal dikirim, Tidak ada Nomor WhatsApp </div>');
                redirect('absensi/foto?token=' . $_GET['token']);
                    
            }
            // echo "<pre>";
            // print_r($pegawai);
            // print_r($pegawaiMeta);
            // return;
    
            
        }
        
    }

    public function getAbsensiHarianPegawai(){
        if (!isset($_POST['tanggal']) || 
            !isset($_POST['jenis_pegawai']) || 
            $_POST['tanggal'] == "" || 
            $_POST['jenis_pegawai'] == "") {

            echo json_encode(["data"=>array()]);
            return;
        }
        
        extract($_POST);
        
        $akses           = [1,2];
        $skpd_id         = in_array($this->session->userdata('role_id'), $akses) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id');
        $tanggal         = date("Y-m-d", strtotime($_POST['tanggal']));
        
        $datas = array();

        $pegawai = $jenis_pegawai == 'pegawai' ? $this->Pegawai_model->getPegawai(null, $skpd_id) : $this->Pegawai_model->getPegawaiTks(null, $skpd_id) ;

        array_multisort(array_column($pegawai, 'nama'), SORT_ASC, $pegawai);


        $no=1;
        foreach ($pegawai as $pg) {
            $izinKerja = $this->db
                            ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                            ->where('tb_izin_kerja.pegawai_id', $pg['user_id'])
                            ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                            ->group_start()
                                ->where("tb_izin_kerja_meta.tanggal_awal<=", $tanggal)
                                ->where("tb_izin_kerja_meta.tanggal_akhir>=", $tanggal)
                            ->group_end()
                            ->where("tb_izin_kerja.status", 1)
                            ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                            ->get('tb_izin_kerja')->row_array();
            
            $absensi = $this->db
                            ->where('pegawai_id', $pg['user_id'])
                            ->where('jenis_pegawai', $jenis_pegawai)
                            ->where('skpd_id', $skpd_id)
                            ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $tanggal)
                            ->where('status', 1)
                            ->order_by('id', 'asc')
                            ->get('tb_absensi')->result();
            
            $jam_masuk              = null;
            $jam_pulang             = null;
            $jam_istirahat_keluar   = null;
            $jam_istirahat_masuk    = null;

            $isAbsenManualMasuk             = null;
            $isAbsenManualPulang            = null;
            $isAbsenManualIstirahat         = null;
            $isAbsenManualSelesiIstirahat   = null;

            $jamKerjaPegawai    = $this->jamKerjaPegawai($pg['user_id'], $jenis_pegawai, $tanggal);
            
            foreach($absensi as $abs){
                $labels             = array();
                $jam                = $this->getJamAbsen($abs->jam, $abs->pegawai_id, $abs->jenis_pegawai, $abs->jenis_absen, $jamKerjaPegawai);

                if(isset($jam['label'])) $labels[] = $jam['label'];
                if($abs->jenis_absen == 'Absen Upacara' && isset($upacaralibur->kategori)) $labels[] = $upacaralibur->kategori;
                
                if($labels) { 
                    $label = " (".implode(", ", $labels).")";
                }else{
                    $label = null;
                }

                $isAbsenManualMasuk             = !$jam_masuk && $abs->jenis_absen=="Absen Masuk" && $abs->keterangan ? "<small>AMP (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                $isAbsenManualPulang            = !$jam_pulang && $abs->jenis_absen=="Absen Pulang" && $abs->keterangan ? "<small>AMS (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                $isAbsenManualIstirahat         = !$jam_istirahat_masuk && $abs->jenis_absen=="Absen Istirahat" && $abs->keterangan ? "<small>AMI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                $isAbsenManualSelesiIstirahat   = !$jam_istirahat_keluar && $abs->jenis_absen=="Absen Selesai" && $abs->keterangan ? "<small>AMSI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;

                $jam_masuk              = isset($jam['jam_masuk']) && (!$jam_masuk || $jam['jam_masuk']=="Upacara" ||  $jam['jam_masuk']=="Senam") ? "<span class='mb-show'>Masuk</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_masuk']."</a>".$label : $jam_masuk;
                $jam_istirahat_masuk    = isset($jam['jam_istirahat']) && !$jam_istirahat_masuk ? "<span class='mb-show'>Istirahat</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_istirahat']."</a>".$label : $jam_istirahat_masuk;
                $jam_istirahat_keluar   = isset($jam['jam_selesai_istirahat']) && !$jam_istirahat_keluar ? "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_selesai_istirahat']."</a>".$label : $jam_istirahat_keluar;
                $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang ? "<span class='mb-show'>Pulang</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_pulang']."</a>".$label : $jam_pulang;
            }
            $gelarDepan      = isset($pg['gelar_depan']) && $pg['gelar_depan'] && $pg['gelar_depan']!=="" ? $pg['gelar_depan']."." : null;
            $gelarBelakang   = isset($pg['gelar_belakang']) && $pg['gelar_belakang'] && $pg['gelar_belakang']!="" ? " ".$pg['gelar_belakang'] : null;
  
            $nama = "<div class='tb-wrap'>".$gelarDepan.$pg['nama'].$gelarBelakang."</div>"
                            .($jamKerjaPegawai ? "<div style='margin-top: 7px; font-size: 12px' class='tb-wrap text-primary'>".$jamKerjaPegawai['nama_jam_kerja']."</div>" :null);
            $returnJam  = $izinKerja ? 
                            "<div class='col-md-4 tb-wrap text-center'><strong>".$izinKerja['jenis_izin']."</strong></div>".
                            "<div class='col-md-4 tb-wrap text-center'><a href='".base_url("resources/berkas/izin_kerja/".$izinKerja['file_izin'])."'>Berkas</a></div>".
                            "<div class='col-md-4 tb-wrap text-center'>Disetujui Oleh : <br><strong>".$izinKerja['aproved_by_nama']."</strong></div>"
                            : 
                            "<div class='col-md-3 tb-wrap text-center ".($jam_masuk ? "p-1" : null)."'>".$jam_masuk."<br>".$isAbsenManualMasuk."</div>".
                            "<div class='col-md-3 tb-wrap text-center ".($jam_istirahat_masuk ? "p-1" : null)."'>".$jam_istirahat_masuk."<br>".$isAbsenManualIstirahat."</div>".
                            "<div class='col-md-3 tb-wrap text-center ".($jam_istirahat_keluar ? "p-1" : null)."'>".$jam_istirahat_keluar."<br>".$isAbsenManualSelesiIstirahat."</div>".
                            "<div class='col-md-3 tb-wrap text-center ".($jam_pulang ? "p-1" : null)."'>".$jam_pulang."<br>".$isAbsenManualPulang."</div>"
                            ;
            $data = array();
            $data[] = $nama;
            $data[] = "<div class='row'>".$returnJam."</div>";
            $data[] = date("N", strtotime($tanggal));
            $data[] = isset($upacaralibur->kategori) ? $upacaralibur->kategori : null;
            $data[] = isset($upacaralibur->upacara_hari_libur) ? $upacaralibur->upacara_hari_libur : null;
            $datas[] = $data;
        }
        echo json_encode(array("data" => $datas));
    }

    private function jamKerjaPegawai($pegawai_id, $jenis_pegawai, $tanggal){
        return $this->db->
                        select('tb_jam_kerja_pegawai.*, tb_jam_kerja.nama_jam_kerja')->
                        where('pegawai_id', $pegawai_id)->
                        where('jenis_pegawai', $jenis_pegawai)->
                        where('tanggal', date("Y-m-d", strtotime($tanggal)))->
                        join('tb_jam_kerja', 'tb_jam_kerja.id=tb_jam_kerja_pegawai.jam_kerja_id', 'left')->
                        get('tb_jam_kerja_pegawai')->row_array();

    }

    private function getJamAbsen($tanggal, $pegawai_id, $jenis_pegawai, $jenis_absen, $jamKerjaPegawai){
        
        

        $now                = strtotime($tanggal);

        

        $jam_kerja  = $jamKerjaPegawai ? 
                            $this->db
                               ->where('jam_kerja_id', $jamKerjaPegawai['jam_kerja_id'])
                               ->group_start()
                                   ->where('hari', date('N', $now))
                                   ->or_where('hari', 0)
                               ->group_end()
                               ->get('tb_jam_kerja_meta')
                               ->row() : 
                            $this->db
                               ->where('jam_kerja_id', 1)
                               ->where('hari', date('N', $now))
                               ->get('tb_jam_kerja_meta')
                               ->row();
                               

                               
        if(!$jam_kerja) return [];

                               
        $jam_awal_masuk                 = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_masuk);
        $jam_akhir_masuk                = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_masuk);
        $jam_awal_pulang                = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_pulang);
        $jam_akhir_pulang               = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_pulang);
        $jam_awal_istirahat             = $jam_kerja->jam_awal_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_istirahat) : null;
        $jam_akhir_istirahat            = $jam_kerja->jam_akhir_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_istirahat) : null;
        $jam_awal_selesai_istirahat     = $jam_kerja->jam_awal_selesai_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_selesai_istirahat) : null;
        $jam_akhir_selesai_istirahat    = $jam_kerja->jam_akhir_selesai_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_selesai_istirahat) : null;

        
        if($jenis_absen=='Absen Upacara'){
            return [
                        'jam_masuk'     => "Upacara",
                ];
        }

        if($jenis_absen=='Absen Senam'){
            return [
                        'jam_masuk'     => "Senam",
                ];
        }
        
        if($jenis_absen=='Absen Masuk' && $now >= $jam_awal_masuk && $now<=($jam_akhir_masuk+7200)){
            return [
                        'jam_masuk'     => date("H:i", $now),
                        'label'         => $this->_hitungTerlambatMasuk($now, $jam_akhir_masuk)
                ];
        }
        
        if($jenis_absen=='Absen Istirahat' && $now >= $jam_awal_istirahat && $now <= $jam_akhir_istirahat){
            return [
                        'jam_istirahat'       => date("H:i", $now),
                ];
        }
        if($jenis_absen=='Absen Selesai Istirahat' && $now >= $jam_awal_selesai_istirahat && $now <= $jam_akhir_selesai_istirahat){
            return [
                        'jam_selesai_istirahat'      => date("H:i", $now),
                ];
        }
        
        
        if($jenis_absen=='Absen Pulang' && $now >= ($jam_awal_pulang-7200) && $now<=$jam_akhir_pulang){
            return [
                        'jam_pulang'    => date("H:i", $now),
                        'label'         => $this->_hitungPulangLebihAwal($now, $jam_awal_pulang)
                ];
        }
        
        return [];        
    }


    private function _hitungTerlambatMasuk($jam, $batasMasukAkhir)
    {
        if ($jam > ($batasMasukAkhir+7200)) return "TDHE1";
        if ($jam > ($batasMasukAkhir+5400)) return "TM4";
        if ($jam > ($batasMasukAkhir+3600)) return "TM3";
        if ($jam > ($batasMasukAkhir+1800)) return "TM2";
        if ($jam > $batasMasukAkhir) return "TM1";
    }
    
    private function _hitungPulangLebihAwal($jam, $batasPulangAwal)
    {
        if ($jam < ($batasPulangAwal-7200)) return "TDHE2";
        if ($jam < ($batasPulangAwal-5400)) return "PLA4";
        if ($jam < ($batasPulangAwal-3600)) return "PLA3";
        if ($jam < ($batasPulangAwal-1800)) return "PLA2";
        if ($jam < $batasPulangAwal) return "PLA1";
    }

    
}
