<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan extends CI_Controller
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

    public function getTableJamKerjaPegawai(){
        if (isset($_POST['bulan']) && $_POST['bulan'] !="") {
            $pegawai_id      = isset($_POST['pegawai_id']) ? $_POST['pegawai_id'] : $this->session->userdata('user_id'); 
            $jenis_pegawai   = isset($_POST['jenis_pegawai']) ? $_POST['pegawai_id'] : $this->session->userdata('jenis_pegawai'); 
            $skpd_id         = isset($_POST['skpd_id']) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id'); 
            
            $jamKerja        = $this->db->get('tb_jam_kerja')->result();
            
            $akses           = [1,2,3];
            $pegawai_id      =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['pegawai_id'] : $this->session->userdata('user_id');
            $jenis_pegawai   =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');

            $begin = new DateTime(date("01-m-Y", strtotime("01-".$_POST['bulan'])));
            $end = new DateTime(date("t-m-Y", strtotime("01-".$_POST['bulan'])));
            $end->modify('+1 day');
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            $no = 1;


            foreach ($period as $dt) {
                $jamKerjaPegawai        = $this->db->
                                            where('pegawai_id', $pegawai_id)->
                                            where('jenis_pegawai', $jenis_pegawai)->
                                            where('tanggal', $dt->format('Y-m-d'))->
                                            get('tb_jam_kerja_pegawai')->row();
                $dt_sekarang = $dt->format('Y-m-d');
                $opt = '<select class="jamPegawaiSelect2" name="jam_kerja_pegawai['.$dt_sekarang.']">';
                    $opt.='<option value="">Default</option>';
                    foreach($jamKerja as $JK){
                        $opt.='<option value="'.$JK->id.'" '.(isset($jamKerjaPegawai->jam_kerja_id) && $jamKerjaPegawai->jam_kerja_id==$JK->id ? "selected" : null).'>'.$JK->nama_jam_kerja.'</option>';
                    }
                $opt.= '</select>';


                $upacaralibur   = $this->db
                                     ->where('tanggal', $dt->format('Y-m-d'))
                                     ->get('tb_upacara_libur')->row();
                
                
                $tanggalLog = "<div>".$this->hari[$dt->format("w")] . "</div><div style='margin-top: 7px'>" . $dt->format("d") . " " . $this->bulan[(int) $dt->format("m")] . " " . $dt->format("Y")."</div>";

                $data = array();
                $data[] = $tanggalLog.(isset($upacaralibur->nama_hari) ?  "<div class='tb-wrap' style='margin-top: 7px; width:100%;font-weight: 700;'>".$upacaralibur->nama_hari."</div>" : null);
                $data[] = $opt;
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


    public function kordinat()
    {
		$data = [
		    "title"             => "Pengaturan Kordinat Unit Kerja",
			"page"				=> "pengaturan/kordinat",
			"skpds"             => $this->Skpd_model->getSkpd(),
			"kordinats"         => $this->db->get('tb_kordinat')->result_array(),
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
    
    public function kordinattambahan()
    {
		$data = [
		    "title"             => "Pengaturan Kordinat Tambahan",
			"page"				=> "pengaturan/kordinattambahan",
			"kordinat_tambahan" => $this->db->order_by('id', 'desc')->get('tb_kordinat_tambahan')->result_array(),
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
    
    public function hapuskordinattambahan($id=false)
    {
        if($id){
            $kordinat    = $this->db->where('id', $id)->get('tb_kordinat_tambahan')->row_array();
            if($kordinat) {
                $this->db->where('id', $id)->delete('tb_kordinat_tambahan');
                $this->session->set_flashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Berhasil dihapus!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                ');
    		    redirect('pengaturan/kordinattambahan?token='.$_GET['token']);
    		    return;
            }
        }
        $this->session->set_flashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Invalid!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
        ');
	    redirect('pengaturan/kordinattambahan?token='.$_GET['token']);
	    return;
        
    }
    public function setkordinattambahan($id=false)
    {
        if($id){
            $kordinat    = $this->db->where('id', $id)->get('tb_kordinat_tambahan')->row_array();
            if(!$kordinat) {redirect('pengaturan/kordinattambahan?token='.$_GET['token']);return;}
        }
        
        $this->form_validation->set_rules('skpd_id', 'OPD', 'required');
        $this->form_validation->set_rules('nama_kordinat', 'Nama Kordinat', 'required');
        // $this->form_validation->set_rules('latitude', 'Latitude', 'required');
        // $this->form_validation->set_rules('longitude', 'Longitude', 'required');
        // $this->form_validation->set_rules('radius', 'Radius', 'required');
		if($this->form_validation->run()){
            extract($_POST);
            $skpd = explode("-", $skpd_id);
            $data = [
                "skpd_id"       => $skpd[0],
                "nama_skpd"     => $skpd[1],
                "nama_kordinat" => $nama_kordinat,
                "latitude"      => $latitude,
                "longitude"     => $longitude,
                "radius"        => $radius,
                "created_at"    => date("Y-m-d H:i:s")
            ];
            
            if(isset($kordinat) && $kordinat){
                $this->db->where('id', $id)->update('tb_kordinat_tambahan', $data);
            }else{
                $this->db->insert('tb_kordinat_tambahan', $data);
            }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('pengaturan/kordinattambahan?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => "Pengaturan Kordinat Tambahan",
			"page"				=> "pengaturan/setkordinattambahan",
			"skpds"             => $this->Skpd_model->getSkpd(true),
			"kordinat"          => isset($kordinat) ? $kordinat : false,
		];
		
		$this->load->view('template/default', $data);
    }

    
    public function absensipegawai()
    {
		$data = [
		    "title"             => "Pengaturan Absensi Pegawai",
			"page"				=> "pengaturan/absensipegawai",
			"skpds"             => $this->Skpd_model->getSkpd(),
			"pengaturan"        => $this->db->get('tb_peraturan_absensi')->result_array(),
            "pengaturanabsensi" => $this->db->where('jenis_pegawai', null)->where('opd_id', null)->get('tb_peraturan_absensi')->row_array(),

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
    public function absensitks()
    {
		$data = [
		    "title"             => "Pengaturan Absensi TKS",
			"page"				=> "pengaturan/absensitks",
			"skpds"             => $this->Skpd_model->getSkpd(),
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
    public function jamkerja()
    {
        $akses = [1]; 

        if(isset($_GET['skpd_id']) && $_GET['skpd_id']!=0){
            $this->db->where('skpd_id', $_GET['skpd_id']);
        }else if(!in_array($this->session->userdata('role_id'), $akses)){
            $this->db->where('skpd_id', $this->session->userdata('skpd_id'));
        }

        $jamkerjas = $this->db->where('deleted', null)->get('tb_jam_kerja')->result_array();
		
		$data = [
		    "title"             => "Pengaturan Jam Kerja",
			"page"				=> "pengaturan/jamkerja",
			"skpdsOpt"          => $this->Skpd_model->getSkpd(),
			"skpds"             => $this->Skpd_model->getSkpd(true),
			"jam_kerjas"        => $jamkerjas,
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
    public function aturjamkerjapegawai()
    {
        $this->form_validation->set_rules('pegawai_id', 'Pegawai', 'required');
        $this->form_validation->set_rules('jenis_pegawai', 'Jenis Pegawai', 'required');
		if($this->form_validation->run()){
            if(isset($_POST['jam_kerja_pegawai'])){
                // echo "<pre>";
                // print_r($_POST);
                // return;
                extract($_POST);
                foreach($jam_kerja_pegawai as $tanggal=>$jkp){
                    $jam_kerja_pegawai = $this->db-> 
                                where('pegawai_id', $pegawai_id)->
                                where('jenis_pegawai', $jenis_pegawai)->
                                where('tanggal', $tanggal)->
                                get('tb_jam_kerja_pegawai')->row();

                    if($jam_kerja_pegawai) {
                        $this->db->where('id', $jam_kerja_pegawai->id)->delete('tb_jam_kerja_pegawai');
                    }

                    if(!$jkp) continue;
                    
                    $data = [
                        "pegawai_id"        => $pegawai_id,
                        "jenis_pegawai"     => $jenis_pegawai,
                        "tanggal"           => $tanggal,
                        "jam_kerja_id"      => $jkp
                    ];

                    if($jkp){
                        $this->db->insert('tb_jam_kerja_pegawai', $data);
                    }
        
                }

                $this->session->set_flashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Berhasil diperbaharui!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                ');
    		    redirect('pengaturan/aturjamkerjapegawai?token='.$_GET['token']);
    		    return;

                
            }
		}

		$data = [
		    "title"             => "Atur Jam Kerja Pegawai",
			"page"				=> "pengaturan/jamkerjapegawai",
			"skpdsOpt"          => $this->Skpd_model->getSkpd(),
			"skpds"             => $this->Skpd_model->getSkpd(true),
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
    
    public function setkordinat($id)
    {
        $skpd        = $this->Skpd_model->getSkpdById($id);
        $kordinat    = $this->db->where('skpd_id', $id)->get('tb_kordinat')->row_array();
        if(!$skpd) {redirect('pengaturan/kordinat?token='.$_GET['token']);return;}

        $this->form_validation->set_rules('latitude', 'Latitude', 'required');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required');
        $this->form_validation->set_rules('radius', 'Radius', 'required');
		if($this->form_validation->run()){
            extract($_POST);
            $data = [
                "skpd_id"       => $id,
                "latitude"      => $latitude,
                "longitude"     => $longitude,
                "radius"        => $radius,
                "updated_at"     => date("Y-m-d H:i:s")
            ];
            
            if($kordinat){
                $this->db->where('id', $kordinat['id'])->update('tb_kordinat', $data);
            }else{
                $this->db->insert('tb_kordinat', $data);
            }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil diperbaharui!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('pengaturan/kordinat?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => "Atur Kordinat Unit Kerja ".$skpd['nama_skpd'],
			"page"				=> "pengaturan/setkordinat",
			"skpd"              => $skpd,
			"kordinat"          => $kordinat
		];
		
		$this->load->view('template/default', $data);
    }
    
    public function setpengaturanabsensipegawai()
    {

        $this->form_validation->set_rules('TMK', 'TMK', 'required');
        $this->form_validation->set_rules('TAU', 'TAU', 'required');
        $this->form_validation->set_rules('TDHE1', 'TDHE1', 'required');
        $this->form_validation->set_rules('TDHE2', 'TDHE2', 'required');
        $this->form_validation->set_rules('TM1', 'TM1', 'required');
        $this->form_validation->set_rules('TM2', 'TM2', 'required');
        $this->form_validation->set_rules('TM3', 'TM3', 'required');
        $this->form_validation->set_rules('TM4', 'TM4', 'required');
        $this->form_validation->set_rules('PLA1', 'PLA1', 'required');
        $this->form_validation->set_rules('PLA2', 'PLA2', 'required');
        $this->form_validation->set_rules('PLA3', 'PLA3', 'required');
        $this->form_validation->set_rules('PLA4', 'PLA4', 'required');
        
        $pengaturanabsensi = $this->db->where('jenis_pegawai', null)->where('opd_id', null)->get('tb_peraturan_absensi')->row_array();
        
		if($this->form_validation->run()){

            $_POST['updated_at'] = date("Y-m-d H:i:s");
            
            if($pengaturanabsensi){
                $this->db->where('id', $pengaturanabsensi['id'])->update('tb_peraturan_absensi', $_POST);
            }else{
                $this->db->insert('tb_peraturan_absensi', $_POST);
            }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil diperbaharui!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('pengaturan/absensipegawai?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => "Atur Pengaturan Absensi",
			"page"				=> "pengaturan/setpengaturanabsensipegawai",
			"pengaturanabsensi" => $pengaturanabsensi
		];
		
		$this->load->view('template/default', $data);
    }
    public function setpengaturanabsensitks($id)
    {

        $this->form_validation->set_rules('TMK', 'TMK', 'required');
        $this->form_validation->set_rules('TAU', 'TAU', 'required');
        $this->form_validation->set_rules('TDHE1', 'TDHE1', 'required');
        $this->form_validation->set_rules('TDHE2', 'TDHE2', 'required');
        $this->form_validation->set_rules('TM1', 'TM1', 'required');
        $this->form_validation->set_rules('TM2', 'TM2', 'required');
        $this->form_validation->set_rules('TM3', 'TM3', 'required');
        $this->form_validation->set_rules('TM4', 'TM4', 'required');
        $this->form_validation->set_rules('PLA1', 'PLA1', 'required');
        $this->form_validation->set_rules('PLA2', 'PLA2', 'required');
        $this->form_validation->set_rules('PLA3', 'PLA3', 'required');
        $this->form_validation->set_rules('PLA4', 'PLA4', 'required');
        
        $pengaturanabsensi = $this->db->where('jenis_pegawai', 'tks')
                                        ->where('opd_id', $id)
                                        ->get('tb_peraturan_absensi')->row_array();
        $skpd = $this->Skpd_model->getSkpdById($id);

        if(!$skpd) {redirect('pengaturan/absensitks?token='.$_GET['token']);return;}


		if($this->form_validation->run()){
            $_POST['opd_id']       = $id;
            $_POST['jenis_pegawai'] = 'tks';
            $_POST['updated_at']    = date("Y-m-d H:i:s");
            
            if($pengaturanabsensi){
                $this->db->where('id', $pengaturanabsensi['id'])->update('tb_peraturan_absensi', $_POST);
            }else{
                $this->db->insert('tb_peraturan_absensi', $_POST);
            }
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil diperbaharui!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('pengaturan/absensitks?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => "Atur Pengaturan Absensi ".$skpd['nama_skpd'],
			"page"				=> "pengaturan/setpengaturanabsensipegawai",
			"pengaturanabsensi" => $pengaturanabsensi
		];
		
		$this->load->view('template/default', $data);
    }


    public function hapusjamkerja($id)
    {
        $this->db->where('id', $id)->update('tb_jam_kerja', ['deleted'=>1]);
        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Berhasil dihapus!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
        ');
	    redirect('pengaturan/jamkerja?token='.$_GET['token']);
	    return;
        
    }
    public function setjamkerja($id=false)
    {
        
        $jamkerja       = !$id ? array() : $this->db->where('id', $id)->get('tb_jam_kerja')->row_array();
        $jamkerjaMetas  = !$id ? array() : $this->db->where('jam_kerja_id', $id)->order_by('id', 'asc')->get('tb_jam_kerja_meta')->result_array();

        

        $this->form_validation->set_rules('nama_jam_kerja', 'Nama Jam Kerja', 'required');
		if($this->form_validation->run()){
            extract($_POST);

            if($jamkerja){
                $this->db->where('id', $id)->update('tb_jam_kerja', [
                        "nama_jam_kerja"    => $nama_jam_kerja
                    ]);
                $this->db->where('jam_kerja_id', $id)->delete('tb_jam_kerja_meta');
                $jam_kerja_id = $id;
                
            
            }else{
                $this->db->insert('tb_jam_kerja', [
                        "nama_jam_kerja"    => $nama_jam_kerja
                    ]);
                
                $jam_kerja_id = $this->db->insert_id();
                
            }


            for($i=0; $i<count($hari); $i++){
                $this->db->insert('tb_jam_kerja_meta', [
                        "jam_kerja_id"                  => $jam_kerja_id,
                        "hari"                          => $hari[$i],
                        "jam_awal_masuk"                => $jam_awal_masuk[$i],
                        "jam_akhir_masuk"               => $jam_akhir_masuk[$i],
                        "jam_awal_pulang"               => $jam_awal_pulang[$i],
                        "jam_akhir_pulang"              => $jam_akhir_pulang[$i],
                        "jam_awal_istirahat"            => $jam_awal_istirahat[$i] ? $jam_awal_istirahat[$i] : null,
                        "jam_akhir_istirahat"           => $jam_akhir_istirahat[$i] ? $jam_akhir_istirahat[$i] : null,
                        "jam_awal_selesai_istirahat"    => $jam_awal_selesai_istirahat[$i] ? $jam_awal_selesai_istirahat[$i] : null,
                        "jam_akhir_selesai_istirahat"   => $jam_akhir_selesai_istirahat[$i] ? $jam_akhir_selesai_istirahat[$i] : null
                    ]);
            }

            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Berhasil diperbaharui!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
            ');
		    redirect('pengaturan/jamkerja?token='.$_GET['token']);
		    return;
		}



		$data = [
		    "title"             => $id ? "Atur Jam Kerja" : "Buat Jam Kerja Baru",
			"page"				=> "pengaturan/setjamkerja",
			"id"                => $id,
			"jamkerja"          => $jamkerja,
			"jamkerjaMetas"     => $jamkerjaMetas
		];
		
		$this->load->view('template/default', $data);
    }


}
