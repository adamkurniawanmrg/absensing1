<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logabsen extends CI_Controller
{

    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model([
            'LogAbsen_model',
            'Pegawai_model',
            'Skpd_model'
        ]);
    }

    public function index()
    {
		$data = [
		    "title"             => "Log Absen",
			"page"				=> "logabsen/logabsen",
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

    public function getLogAbsen(){
        if (
               isset($_POST['tanggalAwal']) 
            && isset($_POST['tanggalAkhir']) 
            && $_POST['tanggalAwal'] != "" 
            && $_POST['tanggalAkhir'] != "") {
            $akses = [1,2,3];
            $pegawai_id =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['pegawai_id'] : $this->session->userdata('user_id');
            $jenis_pegawai =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');


            $begin = new DateTime($_POST['tanggalAwal']);
            $end = new DateTime($_POST['tanggalAkhir']);
            $end->modify('+1 day');
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            $no = 1;

            $datas = array();
            $ik         = $this ->db
                                ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                                ->where('tb_izin_kerja.pegawai_id', $pegawai_id)
                                ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                                ->where("tb_izin_kerja.status", 1)
                                ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                                ->get('tb_izin_kerja');

            $num_ik     = $ik->num_rows();
            $izin       = $ik->result();

            $hari_izin = [];
            $file_izin = [];

            if ($num_ik > 0) {
                foreach ($izin as $iz) {
                    $begin2 = new DateTime($iz->tanggal_awal);
                    $end2 = new DateTime($iz->tanggal_akhir);
                    $end2->modify('+1 day');
                    $interval2 = DateInterval::createFromDateString('1 day');
                    $period2 = new DatePeriod($begin2, $interval2, $end2);
                    foreach ($period2 as $dt2) {
                        if (isset($hari_izin[$dt2->format("Y-m-d")])) {
                            if ($hari_izin[$dt2->format("Y-m-d")] == "Sakit" || $hari_izin[$dt2->format("Y-m-d")] == "Izin") {
                                continue;
                            }
                        }
                        $hari_izin[$dt2->format("Y-m-d")] = $iz->jenis_izin;
                        $file_izin[$dt2->format("Y-m-d")] = $iz->file_izin;
                    }
                }
            }


            foreach ($period as $dt) {
                $this->db->select('tb_absen_wajah.*');
                $this->db->from('tb_absen_wajah');
                $this->db->where("tb_absen_wajah.jenis_pegawai", $jenis_pegawai);
                $this->db->where("tb_absen_wajah.pegawai_id", $pegawai_id);
                $this->db->where("tb_absen_wajah.tanggal", $dt->format("Y-m-d"));
                $abs = $this->db->get();
                $num_absen = $abs->num_rows();
                $absen = $abs->row();
                $num_bulan = (int) $dt->format("m");
                if ($num_absen > 0) {
                    $pegawaiID = $absen->pegawai_id;
                    $tanggalLog = $this->hari[$dt->format("w")] . ", " . $dt->format("d") . " " . $this->bulan[$num_bulan] . " " . $dt->format("Y") . "<br />";
                    $jam_masuk = $absen->jam_masuk != null ? date("H:i", strtotime($absen->jam_masuk)) : "-";
                    $jam_pulang = $absen->jam_pulang != null ? date("H:i", strtotime($absen->jam_pulang)) : "-";
                    $jam_masuk = $jam_masuk;
                    $jam_pulang = $jam_pulang;
                } else {
                    $pegawaiID = $pegawai_id;
                    $tanggalLog = $this->hari[$dt->format("w")] . ", " . $dt->format("d") . " " . $this->bulan[$num_bulan] . " " . $dt->format("Y") . "<br />";
                    $jam_masuk = "-";
                    $jam_pulang = "-";
                }

                $this->db->select('tb_upacara_libur.*');
                $this->db->from('tb_upacara_libur');
                $this->db->where("tb_upacara_libur.tanggal", $dt->format("Y-m-d"));
                $hl = $this->db->get();
                $num_hl = $hl->num_rows();
                $hari_libur = $hl->row();

                $data = array();

                $data[] = $no;
                $data[] = "<center>" . $pegawaiID . "</center>";
                $data[] = $tanggalLog;


                // $this->db->select('tb_absen_upacara.*, tb_absen_upacara_meta.status');
                // $this->db->from('tb_absen_upacara');
                // $this->db->where('tb_absen_upacara.opd_id', $_POST['opd_id']);
                // $this->db->where('tb_absen_upacara.tanggal', $dt->format("y-m-d"));
                // $this->db->join('tb_absen_upacara_meta', 'tb_absen_upacara_meta.pegawai_id=' . $pegawai_id . ' AND tb_absen_upacara_meta.absen_upacara_id=tb_absen_upacara.id', 'left');
                // $upc = $this->db->get();
                // $jml_upc = $upc->num_rows();
                // $upacara = $upc->row();

                // if ($jml_upc > 0) {
                //     if ($upacara->status == 1) {
                //         $jam_masuk = "AU";
                //     }
                // }

                // $this->db->select('tb_absen_senam.*, tb_absen_senam_meta.status');
                // $this->db->from('tb_absen_senam');
                // $this->db->where('tb_absen_senam.opd_id', $_POST['opd_id']);
                // $this->db->where('tb_absen_senam.tanggal', $dt->format("y-m-d"));
                // $this->db->join('tb_absen_senam_meta', 'tb_absen_senam_meta.pegawai_id=' . $pegawai_id . ' AND tb_absen_senam_meta.absen_senam_id=tb_absen_senam.id', 'left');
                // $sn = $this->db->get();
                // $jml_sn = $sn->num_rows();
                // $senam = $sn->row();

                // if ($jml_sn > 0) {
                //     if ($senam->status == 1) {
                //         $jam_masuk = "Senam";
                //     }
                // }

                // $this->db->select('tb_absen_finger.*, tb_absen_finger_meta.status');
                // $this->db->from('tb_absen_finger');
                // $this->db->where('tb_absen_finger.opd_id', $_POST['opd_id']);
                // $this->db->where('tb_absen_finger.tanggal', $dt->format("y-m-d"));
                // $this->db->join('tb_absen_finger_meta', 'tb_absen_finger_meta.pegawai_id=' . $pegawai_id . ' AND tb_absen_finger_meta.absen_finger_id=tb_absen_finger.id', 'left');
                // $sn = $this->db->get();
                // $jml_finger = $sn->num_rows();
                // $finger = $sn->row();

                // if ($jml_finger > 0) {
                //     if ($finger->status == 1) {
                //         $jam_masuk = "Finger";
                //     }
                // }


                $this->db->select('tb_absen_manual.*');
                $this->db->from('tb_absen_manual');
                $this->db->where("tb_absen_manual.pegawai_id", $pegawai_id);
                $this->db->where("tb_absen_manual.tanggal", $dt->format("Y-m-d"));
                $this->db->where("tb_absen_manual.status", 1);
                $mnl = $this->db->get();
                $jml_mnl = $mnl->num_rows();
                $manual = $mnl->result();
                foreach ($manual as $manual) {
                    if ($jml_mnl > 0) {
                        if ($manual->jenis_absen == "AMP dan AMS") {
                            $jam_masuk = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_amp . "' target='_blank' class='btn-link'>AMP</a>";
                            $jam_pulang = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_ams . "' target='_blank' class='btn-link'>AMS</a>";
                        } else if ($manual->jenis_absen == "AMP") {
                            $jam_masuk = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_amp . "' target='_blank' class='btn-link'>AMP</a>";
                        } else if ($manual->jenis_absen == "AMS") {
                            $jam_pulang = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_ams . "' target='_blank' class='btn-link'>AMS</a>";
                        }
                    }
                }

                if (isset($hari_izin[$dt->format("Y-m-d")]) && $dt->format("w") != 6 && $dt->format("w") != 0) {
                    $jam_masuk = $hari_izin[$dt->format("Y-m-d")];
                    $jam_pulang = "<a href='" . base_url() . "resources/berkas/izin_kerja/" . $file_izin[$dt->format("Y-m-d")] . "' target='_blank' class='btn-link'>Berkas Izin</a>";
                }

                // $jam_masuk .=  "<br />".$this->_hitungTerlambatMasuk($jam_masuk, $dt->format("w"));
                // $jam_pulang = "<br />".$this->_hitungPulangLebihAwal($jam_pulang, $dt->format("w"));

                $data[] = "<center>" . $jam_masuk . "</center>";
                $data[] = "<center>" . $jam_pulang . "</center>";
                $data[] = $dt->format("w");
                $data[] = $num_hl;
                $data[] = $num_hl > 0 ? $hari_libur->nama_hari : null;
                $data[] = $num_hl > 0 ? $hari_libur->upacara_hari_libur : null;
                $datas[] = $data;
                $no++;
            }
            echo json_encode(array("data" => $datas));
        } else {
            echo json_encode(array("data" => false));
        }
    }
    public function getLogAbsen2(){
        if (isset($_POST['bulan']) && $_POST['bulan'] !="") {
            $pegawai_id      = isset($_POST['pegawai_id']) ? $_POST['pegawai_id'] : $this->session->userdata('user_id'); 
            $jenis_pegawai   = isset($_POST['jenis_pegawai']) ? $_POST['pegawai_id'] : $this->session->userdata('jenis_pegawai'); 
            $skpd_id         = isset($_POST['skpd_id']) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id'); 
            
            $akses           = [1,3];
            $pegawai_id      =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['pegawai_id'] : $this->session->userdata('user_id');
            $jenis_pegawai   =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');

            $begin = new DateTime(date("01-m-Y", strtotime("01-".$_POST['bulan'])));
            $end = new DateTime(date("t-m-Y", strtotime("01-".$_POST['bulan'])));
            $end->modify('+1 day');
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            $no = 1;

            $datas = array();
            $ik         = $this ->db
                                ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                                ->where('tb_izin_kerja.pegawai_id', $pegawai_id)
                                ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                                ->where("tb_izin_kerja.status", 1)
                                ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                                ->get('tb_izin_kerja');

            $num_ik     = $ik->num_rows();
            $izin       = $ik->result();

            $hari_izin = [];
            $file_izin = [];

            if ($num_ik > 0) {
                foreach ($izin as $iz) {
                    $begin2 = new DateTime($iz->tanggal_awal);
                    $end2 = new DateTime($iz->tanggal_akhir);
                    $end2->modify('+1 day');
                    $interval2 = DateInterval::createFromDateString('1 day');
                    $period2 = new DatePeriod($begin2, $interval2, $end2);
                    foreach ($period2 as $dt2) {
                        if (isset($hari_izin[$dt2->format("Y-m-d")])) {
                            if ($hari_izin[$dt2->format("Y-m-d")] == "Sakit" || $hari_izin[$dt2->format("Y-m-d")] == "Izin") {
                                continue;
                            }
                        }
                        $hari_izin[$dt2->format("Y-m-d")] = $iz->jenis_izin;
                        $file_izin[$dt2->format("Y-m-d")] = $iz->file_izin;
                        $aproved[$dt2->format("Y-m-d")] = $iz->aproved_by_nama;
                    }
                }
            }
            
            $pegawais       = $jenis_pegawai == 'pegawai' ? $this->Pegawai_model->getPegawai(null, $skpd_id) : $this->Pegawai_model->getPegawaiTks(null, $skpd_id) ;
            $indexPegawai   = array_search($pegawai_id, array_column($pegawais, 'user_id'));
            $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
            $pg             = (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['username'=>'undefined']);


            foreach ($period as $dt) {
                
                $absensi = $this->db
                                ->where('pegawai_id', $pegawai_id)
                                ->where('jenis_pegawai', $jenis_pegawai)
                                ->where('skpd_id', $skpd_id)
                                ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $dt->format("Y-m-d"))
                                ->where('status', 1)
                                ->order_by('id', 'asc')
                                ->get('tb_absensi')->result();
                
                $upacaralibur = $this->db
                                     ->where('tanggal', $dt->format('Y-m-d'))
                                     ->get('tb_upacara_libur')->row();
                
                $jam_masuk                      = null;
                $jam_pulang                     = null;
                $jam_istirahat_keluar           = null;
                $jam_istirahat_masuk            = null;
                
                $isAbsenManualMasuk             = null;
                $isAbsenManualPulang            = null;
                $isAbsenManualIstirahat         = null;
                $isAbsenManualSelesiIstirahat   = null;

                $jamKerjaPegawai    = $this->jamKerjaPegawai($pegawai_id, $jenis_pegawai, $dt->format("Y-m-d"));
                
                
                
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

                    $isAbsenManualMasuk             = !$jam_masuk && $abs->jenis_absen=="Absen Masuk" && $abs->keterangan ? "<small>AMP (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                    $isAbsenManualPulang            = !$jam_pulang && $abs->jenis_absen=="Absen Pulang" && $abs->keterangan ? "<small>AMS (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                    $isAbsenManualIstirahat         = !$jam_istirahat_masuk && $abs->jenis_absen=="Absen Istirahat" && $abs->keterangan ? "<small>AMI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                    $isAbsenManualSelesiIstirahat   = !$jam_istirahat_keluar && $abs->jenis_absen=="Absen Selesai" && $abs->keterangan ? "<small>AMSI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                        
                    $jam_masuk              = isset($jam['jam_masuk']) && (!$jam_masuk || $jam['jam_masuk']=="Upacara" ||  $jam['jam_masuk']=="Senam") ? "<span class='mb-show'>Masuk</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_masuk']."</a>".$label : $jam_masuk;
                    $jam_istirahat_masuk    = isset($jam['jam_istirahat']) && !$jam_istirahat_masuk ? "<span class='mb-show'>Istirahat</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_istirahat']."</a>".$label : $jam_istirahat_masuk;
                    $jam_istirahat_keluar   = isset($jam['jam_selesai_istirahat']) && !$jam_istirahat_keluar ? "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_selesai_istirahat']."</a>".$label : $jam_istirahat_keluar;
                    $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang ? "<span class='mb-show'>Pulang</span><a target='_blank' href='".base_url("file_absensi/". $pg['username'] . "/" . $abs->jam.".png")."'>".$jam['jam_pulang']."</a>".$label : $jam_pulang;
    
                }
                                
                $tanggalLog = "<div>".$this->hari[$dt->format("w")] . "</div>
                                <div style='margin-top: 7px'>" . $dt->format("d") . " " . $this->bulan[(int) $dt->format("m")] . " " . $dt->format("Y")."
                               </div>"
                                .($jamKerjaPegawai ? "<div style='margin-top: 7px;' class='tb-wrap text-primary'>".$jamKerjaPegawai['nama_jam_kerja']."</div>" :null);
                $returnJam  = isset($hari_izin[$dt->format("Y-m-d")]) ? 
                                "<div class='col-md-4 tb-wrap text-center'><strong>".$hari_izin[$dt->format("Y-m-d")]."</strong></div>".
                                "<div class='col-md-4 tb-wrap text-center'><a href='".base_url("resources/berkas/izin_kerja/".$file_izin[$dt->format("Y-m-d")])."'>Berkas</a></div>".
                                "<div class='col-md-4 tb-wrap text-center'>".($aproved[$dt->format("Y-m-d")] ? "Disetujui Oleh: <br>".$aproved[$dt->format("Y-m-d")] : null). "</div>"
                                : 
                                "<div class='col-md-3 tb-wrap text-center p-1'>".$jam_masuk."<br>".$isAbsenManualMasuk."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".$jam_istirahat_masuk."<br>".$isAbsenManualIstirahat."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".$jam_istirahat_keluar."<br>".$isAbsenManualSelesiIstirahat."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".$jam_pulang."<br>".$isAbsenManualPulang."</div>"
                                ;
                $data = array();
                $data[] = $tanggalLog.(isset($upacaralibur->nama_hari) ?  "<div class='tb-wrap' style='margin-top: 7px; width:100%;font-weight: 700;'>".$upacaralibur->nama_hari."</div>" : null);
                $data[] = "<div class='row'>".$returnJam."</div>";
                $data[] = $dt->format('N');
                $data[] = isset($upacaralibur->kategori) ? $upacaralibur->kategori : null;
                $data[] = isset($upacaralibur->upacara_hari_libur) ? $upacaralibur->upacara_hari_libur : null;
                $datas[] = $data;
            }
            echo json_encode(array("data" => $datas));
        } else {
            echo json_encode(array("data" => false));
        }
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
