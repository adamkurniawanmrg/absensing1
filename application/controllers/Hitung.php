<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hitung extends CI_Controller
{
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        is_logged_in();
        // error_reporting(0);
        $this->load->model([
            'Pegawai_model',
            'Skpd_model',
        ]);
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {

        $data = [
            "page"				=> "hitung/datahitung",
            "skpds"             => $this->Skpd_model->getSkpd(),
    		"title"             => "Hitung Rekap Absen dan TPP",
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

    public function getPegawai()
    {
        if (isset($_POST['bulan']) && isset($_POST['opd_id'])) {
            extract($_POST);
            $no             = 1;
            $pegawais       = $this->Pegawai_model->getPegawaiByOpd($opd_id);
            $tkss           = $this->Pegawai_model->getTksByOpd($opd_id);
            $opd            = $this->Skpd_model->getSkpdById($opd_id);

            $col_tks        = array_column($tkss, 'nama');
            array_multisort($col_tks, SORT_ASC, $tkss);

        ?>
            <div class="card" style="margin-top:20px; margin-bottom:20px">
                <div class="card-header">
                    <div class="h5 mb-4 text-gray-800"><?= $opd['nama_skpd']; ?></div>
                    <input type="hidden" id="view_nama_opd" value="<?= $opd['nama_skpd']; ?>">
                </div>
                <div class="card-body table-responsive card-modal">
                    <table class="table-striped table-hover" id="tablelistpegawai" cellpadding="6" width="100%">
                        <thead class="text-center">
                            <th>#</th>
                            <th>Nama Pegawai</th>
                            <th>NIP/NIK</th>
                            <th>Unit Kerja</th>
                            <th>Semua <input type='checkbox' id="rekap_semua" name='rekap_semua' value='1' /></th>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($pegawais as $pegawai) {
                                    if(!$pegawai['username']){
                                        continue;
                                    }
                                    $gelarDepan         = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
                                    $gelarBelakang      = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
                                    $pegawai['nama']    = $gelarDepan.$pegawai['nama'].$gelarBelakang;
                                    $rekap              = $this->db->
                                                                 where("bulan", $bulan)->
                                                                 where("opd_id", $opd_id)->
                                                                 where("pegawai_id", $pegawai['user_id'])->
                                                                 where("jenis_pegawai", "pegawai")->
                                                                 get('tb_rekap_absen')->
                                                                 num_rows();
                                    
                                    $checked            = $rekap > 0 ? "checked" : null;
    
                                    echo "<tr>";
                                    echo "<td align='center'>" . $no . "</td>";
                                    echo "<td>" . $pegawai['nama'] . "</td>";
                                    echo "<td align='center'>" . $pegawai['username'] . "</td>";
                                    echo "<td>PNS di " . $pegawai['nama_skpd'] . "</td>";
                                    echo "<td align='center'><input type='checkbox' class='rekap_pegawai' name='data_pegawai' value='" . $pegawai['user_id'] . "_+_pegawai_+_".$pegawai['nama']."_+_".$pegawai['skpd_id']."_+_".$pegawai['nama_skpd']."' " . $checked . " /></td>";
                                    echo "</tr>";
                                    $no++;
                                }
                                foreach ($tkss as $tks) {
                                    if(!$tks['username']){
                                        continue;
                                    }

                                    $rekap = $this->db->
                                            where("bulan", $bulan)->
                                            where("opd_id", $opd_id)->
                                            where("pegawai_id", $tks['user_id'])->
                                            where("jenis_pegawai", "tks")->
                                            get('tb_rekap_absen')->num_rows();
                                    
                                    $checked = $rekap > 0 ? "checked" : null;
    
                                    echo "<tr>";
                                    echo "<td align='center'>" . $no . "</td>";
                                    echo "<td>".$tks['nama']."</td>";
                                    echo "<td align='center'>" . $tks['username'] . "</td>";
                                    echo "<td>TKS di " . $tks['nama_skpd'] . "</td>";
                                    echo "<td align='center'><input type='checkbox' class='rekap_tks' name='data_tks' value='" . $tks['tks_id'] . "_+_tks_+_".$tks['nama']."_+_".$tks['skpd_id']."_+_".$tks['nama_skpd']."' " . $checked . " /></td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            ?>
                        </tbody>
                    </table>

                </div>
                <ul class="list-group list-group-flush table-responsive">
                    <li class="list-group-item">
                        <button id="btnSelesaiPegawai" class="btn btn-sm btn-primary" disabled>Selesai & Hitung</button>
                        <button id="closeLaburaModal" class="btn btn-sm btn-danger">Close</button>
                    </li>
                </ul>
            </div>

        <?php
        }
    }
    public function getOpd()
    {
        $opds = array();
        if (isset($_POST['bulan'])) {
            extract($_POST);

            $skpds = $this->Skpd_model->getSkpd();
            $no = 1;
            foreach ($skpds as $skpd) {

                $nama_skpd = explode(" ", $skpd['nama_skpd']);
                if( $skpd['nama_skpd']=='Satuan Polisi Pamong Praja' ||
                    $nama_skpd[0]=='Dinas' ||
                    $nama_skpd[0]=='Badan' ||
                    $nama_skpd[0]=='Sekretariat' ||
                    $nama_skpd[0]=='Kecamatan' ||
                    $nama_skpd[0]=='Inspektorat'){}else{continue;}


                $jumlahPNS = $this->db->where('opd_id', $skpd['id_skpd'])->get('tb_pegawai_meta')->num_rows();
                $jumlahTKS = $this->db->where('opd_id', $skpd['id_skpd'])->get('tb_tks_meta')->num_rows();
        
                $o = array();
                $o[] = $no;
                $o[] = $skpd['nama_skpd'];
                $o[] = $jumlahPNS."/".($jumlahPNS+$jumlahTKS);
                
                // $presentasi = $total_pegawai == 0 ? 0 : ($total_terinput / $total_pegawai) * 100;
                // $presentasi = (int) $presentasi;
                // $o[] = "<div class='rajoe-progress-bar'><div class='child' style='width: " . $presentasi . "%'></div></div><i>Terhitung " . $total_terinput . " dari " . $total_pegawai . "</i>";
                $o[] = null;
                $o[] = $skpd['id_skpd'];
                $o[] = null;

                $opds[] = $o;
                $no++;
            }
        }

        echo json_encode(array("data" => $opds));
        return;
    }
    public function getRekapAbsen()
    {
        if (isset($_POST['bulan']) && isset($_POST['opd_id']) && isset($_POST['kategori_pegawai'])) {
            extract($_POST);
            $rekap      = $this->db->where('bulan', $bulan)
                                    ->where('opd_id', $opd_id)
                                    ->where('jenis_pegawai', $_POST['kategori_pegawai'])
                                    ->get('tb_rekap_absen')
                                    ->result();
            $no = 1;
        ?>
            <h4 class="bt-2">REKAPITULASI ABSENSI <?=strtoupper($_POST['kategori_pegawai']);?></h4>
            <hr>
            <table class="table table-bordered table-striped table-hover " id="stageDatatable" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>JHK</th>
                        <th>H</th>
                        <th>I</th>
                        <th>S</th>
                        <th>AU</th>
                        <th>DL</th>
                        <th>AMP</th>
                        <th>AMS</th>
                        <th>TMK</th>
                        <th>TAU</th>
                        <th>TDHE1</th>
                        <th>TDHE2</th>
                        <th>TM1</th>
                        <th>TM2</th>
                        <th>TM3</th>
                        <th>TM4</th>
                        <th>PLA1</th>
                        <th>PLA2</th>
                        <th>PLA3</th>
                        <th>PLA4</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no=1;foreach ($rekap as $rk):?>
                        <tr>
                            <td scope="row"><?= $no;$no++;?></td>
                            <td><?= $rk->nama_pegawai; ?></td>
                            <td><?= $rk->JHK; ?></td>
                            <td><?= $rk->H; ?></td>
                            <td><?= $rk->I; ?></td>
                            <td><?= $rk->S; ?></td>
                            <td><?= $rk->AU; ?></td>
                            <td><?= $rk->DL; ?></td>
                            <td><?= $rk->AMP; ?></td>
                            <td><?= $rk->AMS; ?></td>
                            <td><?= $rk->TMK; ?></td>
                            <td><?= $rk->TAU; ?></td>
                            <td><?= $rk->TDHE1; ?></td>
                            <td><?= $rk->TDHE2; ?></td>
                            <td><?= $rk->TM1; ?></td>
                            <td><?= $rk->TM2; ?></td>
                            <td><?= $rk->TM3; ?></td>
                            <td><?= $rk->TM4; ?></td>
                            <td><?= $rk->PLA1; ?></td>
                            <td><?= $rk->PLA2; ?></td>
                            <td><?= $rk->PLA3; ?></td>
                            <td><?= $rk->PLA4; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php
        }
    }
    public function getTpp()
    {
        if (isset($_POST['bulan']) && isset($_POST['opd_id']) && isset($_POST['kategori_pegawai'])) {
            extract($_POST);
            if($kategori_pegawai=="pegawai"){
            $data_tpp = $this->db->select('
                                            tb_hitung.*,
                                            tb_hitung.nama_pegawai nama,
                                            tb_pegawai_meta.cpns,
                                            tb_jabatan_golongan.nama_golongan golongan,
                                            tb_jabatan_penghasilan.nama_jabatan jabatan_opd,
                                            tb_jabatan_plt.nama_jabatan jabatan_plt
                                 ')
                                 ->where('tb_hitung.bulan', $bulan)
                                 ->where('tb_hitung.opd_id', $opd_id)
                                 ->join('tb_pegawai_meta', 'tb_pegawai_meta.pegawai_id=tb_hitung.pegawai_id', 'left')
                                 ->join('tb_jabatan_penghasilan', 'tb_jabatan_penghasilan.id=tb_pegawai_meta.jabatan_perbub_tpp', 'left')
                                 ->join('tb_jabatan_penghasilan as tb_jabatan_plt', 'tb_jabatan_plt.id=tb_pegawai_meta.jabatan_rangkap_perbub', 'left')
                                 ->join('tb_jabatan_golongan', 'tb_jabatan_golongan.id=tb_pegawai_meta.jabatan_golongan', 'left')
                                 ->order_by('NSKP', "DESC")
                                 ->get('tb_hitung')->result();

            $no = 1;
            $total["NPKP"]      = 0;
            $total["NSKP"]      = 0;
            $total["TAU"]       = 0;
            $total["TMK"]       = 0;
            $total["TDHE1"]     = 0;
            $total["TDHE2"]     = 0;
            $total["TM1"]       = 0;
            $total["TM2"]       = 0;
            $total["TM3"]       = 0;
            $total["TM4"]       = 0;
            $total["PLA1"]      = 0;
            $total["PLA2"]      = 0;
            $total["PLA3"]      = 0;
            $total["PLA4"]      = 0;
            $total["totalPKP"]  = 0;
            $total["SKP"]       = 0;
            $total["totalSKP"]  = 0;
            $total["totalTPP"]  = 0;
            $total["PPH"]       = 0;
            $total["bersih"]    = 0;

            foreach ($data_tpp as $tpp) {
                $this->db->select('tb_rekap_absen.*')
                        ->where('tb_rekap_absen.bulan', $bulan)
                        ->where('tb_rekap_absen.opd_id', $opd_id)
                        ->where('tb_rekap_absen.jenis_pegawai', $_POST['kategori_pegawai'])
                        ->where('tb_rekap_absen.pegawai_id', $tpp->pegawai_id)
                        ->where('tb_rekap_absen.jenis_pegawai', 'pegawai');

                $data_rekap = $this->db->get('tb_rekap_absen');
                $num_rekap  = $data_rekap->num_rows();
                $rekap      = $data_rekap->row();

                $pre_kehadiran = (int) (($rekap->H * 100) / $rekap->JHK);
                $totalMinusPkp = $tpp->TMK + $tpp->TAU + $tpp->TDHE1 + $tpp->TDHE2 + $tpp->TM1 + $tpp->TM2 + $tpp->TM3 + $tpp->TM4 + $tpp->PLA1 + $tpp->PLA2 + $tpp->PLA3 + $tpp->PLA4;
                $SKP = $tpp->SKP != null ? $tpp->SKP : 0;

                $RNPKP = $tpp->RNPKP != null ? $tpp->RNPKP : 0;
                $RNSKP = $tpp->RNSKP != null ? $tpp->RNSKP : 0;

                $TPP_NPKP = $tpp->cpns == 1 ? $tpp->NPKP  * (80 / 100) : $tpp->NPKP;
                $TPP_NSKP = $tpp->cpns == 1 ? $tpp->NSKP  * (80 / 100) : $tpp->NSKP;

                $jumlahPKP = ($TPP_NPKP > 0 ? $TPP_NPKP : 0) + ($RNPKP > 0 ? $RNPKP : 0);
                $jumlahSKP = ($TPP_NSKP > 0 ? $TPP_NSKP : 0) + ($RNSKP > 0 ? $RNSKP : 0);

                if ($pre_kehadiran > 0) {

                    $totalPKP = ($TPP_NPKP + $RNPKP) - $totalMinusPkp;
                    $totalSKP = ($TPP_NSKP + $RNSKP) * ($SKP / 100);

                    $totalPKP = $totalPKP > 0 ? $totalPKP : 0;

                    $totalTPP = $totalPKP + $totalSKP;

                    $totalPPH = ($tpp->PPH / 100) * ($totalTPP >= 0 ? $totalTPP : 0);
                    $bersih =  ($totalTPP >= 0 ? $totalTPP : 0) - $totalPPH;
                } else {
                    $totalMinusPkp = 0;
                    $SKP = 0;
                    $totalPKP = 0;
                    $totalSKP = 0;
                    $totalPPH = 0;
                    $bersih = 0;
                    $totalTPP = 0;
                }

                $total["NPKP"]      += $totalPKP;
                $total["NSKP"]      += $totalSKP;
                $total["TAU"]       += $tpp->TAU;
                $total["TMK"]       += $tpp->TMK;
                $total["TDHE1"]     += $tpp->TDHE1;
                $total["TDHE2"]     += $tpp->TDHE2;
                $total["TM1"]       += $tpp->TM1;
                $total["TM2"]       += $tpp->TM2;
                $total["TM3"]       += $tpp->TM3;
                $total["TM4"]       += $tpp->TM4;
                $total["PLA1"]      += $tpp->PLA1;
                $total["PLA2"]      += $tpp->PLA2;
                $total["PLA3"]      += $tpp->PLA3;
                $total["PLA4"]      += $tpp->PLA4;
                $total["totalPKP"]  += $totalPKP;
                $total["SKP"]       += $SKP;
                $total["totalSKP"]  += $totalSKP;
                $total["totalTPP"]  += $totalTPP >= 0 ? $totalTPP : 0;
                $total["PPH"]       += $totalPPH;
                $total["bersih"]    += $bersih;
            }

        ?>
            <h4 class="mb-3">REKAPITULASI TPP <?=strtoupper($_POST['kategori_pegawai']);?></h4>
            <div>&nbsp;</div>
            <div class="row" style="border-top: 1px solid#eaeaea">
                <div class="col-md-2" style="font-weight: 800">Total TPP Kotor</div>
                <div class="col-md-8"><?= $this->_rupiah($total["totalTPP"], 'left', "rp"); ?></div>
            </div>
            <div class="row">
                <div class="col-md-2" style="font-weight: 800">PPH</div>
                <div class="col-md-8"><?= $this->_rupiah($total["PPH"], 'left', "rp"); ?></div>
            </div>
            <div class="row" style="border-bottom: 1px solid#eaeaea">
                <div class="col-md-2" style="font-weight: 800">Total TPP Bersih</div>
                <div class="col-md-8"><?= $this->_rupiah($total["bersih"], 'left', "rp"); ?></div>
            </div>
            <div class="row">&nbsp;</div>
            <table class="table table table-striped table-hover " id="stageDatatable" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Pangkat/Golongan</th>
                        <th>NPKP</th>
                        <th>NSKP</th>
                        <th>TMK</th>
                        <th>TAU</th>
                        <th>TDHE1</th>
                        <th>TDHE2</th>
                        <th>TM1</th>
                        <th>TM2</th>
                        <th>TM3</th>
                        <th>TM4</th>
                        <th>PLA1</th>
                        <th>PLA2</th>
                        <th>PLA3</th>
                        <th>PLA4</th>
                        <th>Total PKP</th>
                        <th>SKP</th>
                        <th>Total SKP</th>
                        <th>Total TPP</th>
                        <th>PPH 21</th>
                        <th>TPP Bersih</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; ?>
                    <?php

                    foreach ($data_tpp as $tpp) {
                        $this->db->where('tb_rekap_absen.bulan', $bulan)
                                ->where('tb_rekap_absen.opd_id', $opd_id)
                                ->where('tb_rekap_absen.jenis_pegawai', $_POST['kategori_pegawai'])
                                ->where('tb_rekap_absen.pegawai_id', $tpp->pegawai_id)
                                ->where('tb_rekap_absen.jenis_pegawai', 'pegawai');

                    $data_rekap = $this->db->get('tb_rekap_absen');
                    $num_rekap  = $data_rekap->num_rows();
                    $rekap      = $data_rekap->row();

                    $pre_kehadiran = (int) (($rekap->H * 100) / $rekap->JHK);
                    $totalMinusPkp = $tpp->TMK + $tpp->TAU + $tpp->TDHE1 + $tpp->TDHE2 + $tpp->TM1 + $tpp->TM2 + $tpp->TM3 + $tpp->TM4 + $tpp->PLA1 + $tpp->PLA2 + $tpp->PLA3 + $tpp->PLA4;
                    $SKP = $tpp->SKP != null ? $tpp->SKP : 0;

                    $RNPKP = $tpp->RNPKP != null ? $tpp->RNPKP : 0;
                    $RNSKP = $tpp->RNSKP != null ? $tpp->RNSKP : 0;

                    $TPP_NPKP = $tpp->cpns == 1 ? $tpp->NPKP  * (80 / 100) : $tpp->NPKP;
                    $TPP_NSKP = $tpp->cpns == 1 ? $tpp->NSKP  * (80 / 100) : $tpp->NSKP;

                    $jumlahPKP = ($TPP_NPKP > 0 ? $TPP_NPKP : 0) + ($RNPKP > 0 ? $RNPKP : 0);
                    $jumlahSKP = ($TPP_NSKP > 0 ? $TPP_NSKP : 0) + ($RNSKP > 0 ? $RNSKP : 0);

                    if ($pre_kehadiran > 0) {
    
                        $totalPKP = ($TPP_NPKP + $RNPKP) - $totalMinusPkp;
                        $totalSKP = ($TPP_NSKP + $RNSKP) * ($SKP / 100);
    
                        $totalPKP = $totalPKP > 0 ? $totalPKP : 0;
    
                        $totalTPP = $totalPKP + $totalSKP;
    
                        $totalPPH = ($tpp->PPH / 100) * ($totalTPP >= 0 ? $totalTPP : 0);
                        $bersih =  ($totalTPP >= 0 ? $totalTPP : 0) - $totalPPH;
                    } else {
                        
                        
                        
                        $totalMinusPkp = 0;
                        $SKP = 0;
                        $totalPKP = 0;
                        $totalSKP = 0;
                        $totalPPH = 0;
                        $bersih = 0;
                        $totalTPP = 0;
                    }

                    ?>
                        <tr>
                            <td scope="row"><?= $no;$no++; ?></td>
                            <td><?= $tpp->nama; ?></td>
                            <td ><?= $tpp->jabatan_opd; ?></td>
                            <td><?= $tpp->golongan; ?></td>
                            <td><?= $TPP_NPKP>0 ? $this->_rupiah($TPP_NPKP) : $this->_rupiah($RNPKP); ?></td>
                            <td><?= $TPP_NSKP>0 ? $this->_rupiah($TPP_NSKP) : $this->_rupiah($RNSKP); ?></td>
                            <td><?= $this->_rupiah($tpp->TMK); ?></td>
                            <td><?= $this->_rupiah($tpp->TAU); ?></td>
                            <td><?= $this->_rupiah($tpp->TDHE1); ?></td>
                            <td><?= $this->_rupiah($tpp->TDHE2); ?></td>
                            <td><?= $this->_rupiah($tpp->TM1); ?></td>
                            <td><?= $this->_rupiah($tpp->TM2); ?></td>
                            <td><?= $this->_rupiah($tpp->TM3); ?></td>
                            <td><?= $this->_rupiah($tpp->TM4); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA1); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA2); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA3); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA4); ?></td>
                            <td><?= $this->_rupiah($totalPKP); ?></td>
                            <td><?= $SKP; ?>%</td>
                            <td><?= $this->_rupiah($totalSKP); ?></td>
                            <td><?= $this->_rupiah($totalPKP + $totalSKP); ?></td>
                            <td><?= $this->_rupiah($totalPPH); ?></td>
                            <td><?= $this->_rupiah($bersih); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php
            }else if($kategori_pegawai=="tks"){
                $data_tpp = $this->db->where('tb_hitung_tks.bulan', $bulan)
                                     ->where('tb_hitung_tks.opd_id', $opd_id)
                                    //  ->order_by('total', "DESC")
                                     ->get('tb_hitung_tks')->result();
        ?>
            <h4 class="bt-2">REKAPITULASI HONOR TKS</h4>
            <hr>
            <hr>
            <table class="table table-bordered table-striped table-hover " id="stageDatatable" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th width="500">Nama TKS</th>
                        <th>Gaji</th>
                        <th>TMK</th>
                        <th>TAU</th>
                        <th>TDHE1</th>
                        <th>TDHE2</th>
                        <th>TM1</th>
                        <th>TM2</th>
                        <th>TM3</th>
                        <th>TM4</th>
                        <th>PLA1</th>
                        <th>PLA2</th>
                        <th>PLA3</th>
                        <th>PLA4</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; ?>
                    <?php

                    foreach ($data_tpp as $tpp) {
                        $bersih = $tpp->gaji-$tpp->TMK-$tpp->TAU-$tpp->TDHE1-$tpp->TDHE2-$tpp->TM1-$tpp->TM2-$tpp->TM3-$tpp->TM4-$tpp->PLA1-$tpp->PLA2-$tpp->PLA3-$tpp->PLA4;
                        
                    ?>
                        <tr>
                            <td scope="row"><?= $no;$no++; ?></td>
                            <td><?= $tpp->nama_pegawai; ?></td>
                            <td><?= $this->_rupiah($tpp->gaji); ?></td>
                            <td><?= $this->_rupiah($tpp->TMK); ?></td>
                            <td><?= $this->_rupiah($tpp->TAU); ?></td>
                            <td><?= $this->_rupiah($tpp->TDHE1); ?></td>
                            <td><?= $this->_rupiah($tpp->TDHE2); ?></td>
                            <td><?= $this->_rupiah($tpp->TM1); ?></td>
                            <td><?= $this->_rupiah($tpp->TM2); ?></td>
                            <td><?= $this->_rupiah($tpp->TM3); ?></td>
                            <td><?= $this->_rupiah($tpp->TM4); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA1); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA2); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA3); ?></td>
                            <td><?= $this->_rupiah($tpp->PLA4); ?></td>
                            <td><?= $this->_rupiah($bersih); ?></td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php
            }else{
                echo "<h3>Tidak ditemukan!</h3>";
            }
        }
    }
    public function cetakTpp($opd_id = false, $tahun = false, $kategori_pegawai = false)
    {
        if($kategori_pegawai=="pegawai"){
            $data_tpp = $this->db->select('
                                            tb_hitung.*,
                                            tb_hitung.nama_pegawai nama,
                                            tb_pegawai_meta.cpns,
                                            tb_jabatan_golongan.nama_golongan golongan,
                                            tb_jabatan_penghasilan.nama_jabatan jabatan_opd,
                                            tb_jabatan_plt.nama_jabatan jabatan_plt
                                 ')
                                 ->where('tb_hitung.bulan', $bulan)
                                 ->where('tb_hitung.opd_id', $opd_id)
                                 ->join('tb_pegawai_meta', 'tb_pegawai_meta.pegawai_id=tb_hitung.pegawai_id', 'left')
                                 ->join('tb_jabatan_penghasilan', 'tb_jabatan_penghasilan.id=tb_pegawai_meta.jabatan_perbub_tpp', 'left')
                                 ->join('tb_jabatan_penghasilan as tb_jabatan_plt', 'tb_jabatan_plt.id=tb_pegawai_meta.jabatan_rangkap_perbub', 'left')
                                 ->join('tb_jabatan_golongan', 'tb_jabatan_golongan.id=tb_pegawai_meta.jabatan_golongan', 'left')
                                 ->order_by('NSKP', "DESC")
                                 ->get('tb_hitung')->result();
    
            $opd = $this->db->where('id', $opd_id)->get('tb_opd')->row();
    
            $no = 1;
    
    
    
            ?>
    
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Cetak - Rekapitulasi Tambahan Penghasilan Pegawai Negeri Sipil</title>
                <link rel="icon" href="<?= base_url('assets/') ?>img/logo/logo_labura.jpg">
                <link href="<?= base_url('assets/') ?>datepicker/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @page {
                        size: 21.56cm 33cm;
                        margin: 5mm 5mm 5mm 5mm;
                    }
                </style>
                <style type="text/css" media="print">
                    @page {
                        size: landscape;
                        margin: 0 auto;
                    }
                </style>
    
            </head>
    
            <body>
                <div>
                    <?php if ($num_tpp > 0) { ?>
                        <br />
                        <h6 style="font-weight: 800" align="center">
                            REKAPITULASI TAMBAHAN PENGHASILAN PEGAWAI NEGERI SIPIL<br />
                            <?= strtoupper($opd->nama_opd); ?> KABUPATEN LABUHANBATU UTARA<br />
                            BULAN <?= strtoupper($this->bulan[(int) $bulan]); ?> TAHUN <?= $tahun; ?>
                        </h6>
                        <br />
                        <table class="table-bordered table-striped" cellpadding="5" style="width:100%; font-size:10px" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th style="padding: 12px">Nama Pegawai</th>
                                    <th>Jabatan</th>
                                    <th>Pangkat/Golongan</th>
                                    <th class="text-center">NPKP</th>
                                    <th class="text-center">NSKP</th>
                                    <th class="text-center">TMK</th>
                                    <th class="text-center">TAU</th>
                                    <th class="text-center">TDHE1</th>
                                    <th class="text-center">TDHE2</th>
                                    <th class="text-center">TM1</th>
                                    <th class="text-center">TM2</th>
                                    <th class="text-center">TM3</th>
                                    <th class="text-center">TM4</th>
                                    <th class="text-center">PLA1</th>
                                    <th class="text-center">PLA2</th>
                                    <th class="text-center">PLA3</th>
                                    <th class="text-center">PLA4</th>
                                    <th class="text-center">Total PKP</th>
                                    <th class="text-center">SKP (%)</th>
                                    <th class="text-center">Total SKP</th>
                                    <th class="text-center">Total TPP</th>
                                    <th class="text-center">PPH 21</th>
                                    <th class="text-center">TPP Bersih</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                <?php $no = 1; ?>
                                <?php
    
                                $total["NPKP"]      = 0;
                                $total["NSKP"]      = 0;
                                $total["TAU"]       = 0;
                                $total["TMK"]       = 0;
                                $total["TDHE1"]     = 0;
                                $total["TDHE2"]     = 0;
                                $total["TM1"]       = 0;
                                $total["TM2"]       = 0;
                                $total["TM3"]       = 0;
                                $total["TM4"]       = 0;
                                $total["PLA1"]      = 0;
                                $total["PLA2"]      = 0;
                                $total["PLA3"]      = 0;
                                $total["PLA4"]      = 0;
                                $total["totalPKP"]  = 0;
                                $total["SKP"]       = 0;
                                $total["totalSKP"]  = 0;
                                $total["totalTPP"]  = 0;
                                $total["PPH"]       = 0;
                                $total["bersih"]    = 0;
    
                                $allData = array();
                                $totalMinusPkp = 0;
                                $SKP = 0;
                                $totalPKP = 0;
                                $totalSKP = 0;
                                $totalPPH = 0;
                                $bersih = 0;
                                $totalTPP = 0;
    
                                foreach ($tpp as $tpp) {
                                    $this->db->select("tb_rekap_absen.*, tb_rekap_absen_meta.*");
                                    $this->db->from("tb_rekap_absen");
                                    $this->db->like("tb_rekap_absen.tanggal", $tahun . "-" . $bulan);
                                    $this->db->where("tb_rekap_absen.opd_id", $opd_id);
                                    $this->db->where("tb_rekap_absen.pegawai_id", $tpp->pegawai_id);
                                    $this->db->join('tb_rekap_absen_meta', 'tb_rekap_absen_meta.rekap_absen_id = tb_rekap_absen.id', 'left');
                                    $data_rekap = $this->db->get();
                                    $num_rekap = $data_rekap->num_rows();
                                    $rekap = $data_rekap->row();
    
                                    $pre_kehadiran = (int) (($rekap->H * 100) / $rekap->JHK);
                                    $totalMinusPkp = $tpp->TMK + $tpp->TAU + $tpp->TDHE1 + $tpp->TDHE2 + $tpp->TM1 + $tpp->TM2 + $tpp->TM3 + $tpp->TM4 + $tpp->PLA1 + $tpp->PLA2 + $tpp->PLA3 + $tpp->PLA4;
                                    $SKP = $tpp->persen != null ? $tpp->persen : 0;
    
                                    $RNPKP = $tpp->RNPKP != null ? $tpp->RNPKP : 0;
                                    $RNSKP = $tpp->RNSKP != null ? $tpp->RNSKP : 0;
    
                                    $TPP_NPKP = $tpp->cpns == 1 ? $tpp->NPKP  * (80 / 100) : $tpp->NPKP;
                                    $TPP_NSKP = $tpp->cpns == 1 ? $tpp->NSKP  * (80 / 100) : $tpp->NSKP;
    
                                    $jumlahPKP = ($TPP_NPKP > 0 ? $TPP_NPKP : 0) + ($RNPKP > 0 ? $RNPKP : 0);
                                    $jumlahSKP = ($TPP_NSKP > 0 ? $TPP_NSKP : 0) + ($RNSKP > 0 ? $RNSKP : 0);
    
                                    if ($pre_kehadiran > 0) {
    
                                        $totalPKP = ($TPP_NPKP + $RNPKP) - $totalMinusPkp;
                                        $totalSKP = ($TPP_NSKP + $RNSKP) * ($SKP / 100);
    
                                        $totalPKP = $totalPKP > 0 ? $totalPKP : 0;
    
                                        $totalTPP = $totalSKP + $totalPKP;
    
                                        $totalPPH = ($tpp->PPH / 100) * ($totalTPP >= 0 ? $totalTPP : 0);
    
                                        $bersih =  ($totalTPP >= 0 ? $totalTPP : 0) - $totalPPH;
                                    } else {
                                        $totalMinusPkp = 0;
                                        $SKP = 0;
                                        $totalPKP = 0;
                                        $totalSKP = 0;
                                        $totalPPH = 0;
                                        $bersih = 0;
                                        $totalTPP = 0;
                                    }

                                    $total["NPKP"]      += $jumlahPKP;
                                    $total["NSKP"]      += $jumlahSKP;
                                    $total["TAU"]       += $tpp->TAU;
                                    $total["TMK"]       += $tpp->TMK;
                                    $total["TDHE1"]     += $tpp->TDHE1;
                                    $total["TDHE2"]     += $tpp->TDHE2;
                                    $total["TM1"]       += $tpp->TM1;
                                    $total["TM2"]       += $tpp->TM2;
                                    $total["TM3"]       += $tpp->TM3;
                                    $total["TM4"]       += $tpp->TM4;
                                    $total["PLA1"]      += $tpp->PLA1;
                                    $total["PLA2"]      += $tpp->PLA2;
                                    $total["PLA3"]      += $tpp->PLA3;
                                    $total["PLA4"]      += $tpp->PLA4;
                                    $total["totalPKP"]  += $totalPKP;
                                    $total["SKP"]       += $SKP;
                                    $total["totalSKP"]  += $totalSKP;
    
                                    $total["totalTPP"]  += $totalTPP >= 0 ? $totalTPP : 0;
    
                                    $total["PPH"]       += $totalPPH;
                                    $total["bersih"]    += $bersih;
    
                                    $subData = array(
                                        'nama_pegawai'    => $tpp->nama,
                                        'jabatan_opd'     => $tpp->jabatan_opd,
                                        'golongan'        => $tpp->golongan,
                                        'NPKP'            => $this->_rupiah($jumlahPKP),
                                        'NSKP'            => $this->_rupiah($jumlahSKP),
                                        'TMK'             => $this->_rupiah($tpp->TMK),
                                        'TAU'             => $this->_rupiah($tpp->TAU),
                                        'TDHE1'           => $this->_rupiah($tpp->TDHE1),
                                        'TDHE2'           => $this->_rupiah($tpp->TDHE2),
                                        'TM1'             => $this->_rupiah($tpp->TM1),
                                        'TM2'             => $this->_rupiah($tpp->TM2),
                                        'TM3'             => $this->_rupiah($tpp->TM3),
                                        'TM4'             => $this->_rupiah($tpp->TM4),
                                        'PLA1'            => $this->_rupiah($tpp->PLA1),
                                        'PLA2'            => $this->_rupiah($tpp->PLA2),
                                        'PLA3'            => $this->_rupiah($tpp->PLA3),
                                        'PLA4'            => $this->_rupiah($tpp->PLA4),
                                        'totalPKP'        => $this->_rupiah($totalPKP),
                                        'SKP'             => $SKP,
                                        'totalSKP'        => $this->_rupiah($totalSKP),
                                        'totalTPP'        => $this->_rupiah($totalTPP),
                                        'totalPPH'        => $this->_rupiah($totalPPH),
                                        'TPPBersih'       => $this->_rupiah($bersih),
                                        'SortTPPBersih'   => $bersih,
    
                                    );
                                    $allData[] = $subData;
                                }
    
                                $column  = array_column($allData, 'SortTPPBersih');
                                array_multisort($column, SORT_DESC, $allData);
    
                                foreach ($allData as $adt) {
                                ?>
                                    <tr>
                                        <td scope="row"><?= $no;
                                                        $no++; ?></td>
                                        <td><?= $adt['nama_pegawai']; ?></td>
                                        <td><?= $adt['jabatan_opd']; ?></td>
                                        <td><?= $adt['golongan']; ?></td>
                                        <td class="text-right"><?= $adt['NPKP']; ?></td>
                                        <td class="text-right"><?= $adt['NSKP']; ?></td>
                                        <td class="text-right"><?= $adt['TMK']; ?></td>
                                        <td class="text-right"><?= $adt['TAU']; ?></td>
                                        <td class="text-right"><?= $adt['TDHE1']; ?></td>
                                        <td class="text-right"><?= $adt['TDHE2']; ?></td>
                                        <td class="text-right"><?= $adt['TM1']; ?></td>
                                        <td class="text-right"><?= $adt['TM2']; ?></td>
                                        <td class="text-right"><?= $adt['TM3']; ?></td>
                                        <td class="text-right"><?= $adt['TM4']; ?></td>
                                        <td class="text-right"><?= $adt['PLA1']; ?></td>
                                        <td class="text-right"><?= $adt['PLA2']; ?></td>
                                        <td class="text-right"><?= $adt['PLA3']; ?></td>
                                        <td class="text-right"><?= $adt['PLA4']; ?></td>
                                        <td class="text-right"><?= $adt['totalPKP']; ?></td>
                                        <td class="text-right"><?= $adt['SKP']; ?></td>
                                        <td class="text-right"><?= $adt['totalSKP']; ?></td>
                                        <td class="text-right"><?= $adt['totalTPP']; ?></td>
                                        <td class="text-right"><?= $adt['totalPPH']; ?></td>
                                        <td class="text-right"><?= $adt['TPPBersih']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td style="padding: 12px; padding-top: 8px; padding-bottom: 8px;" class="text-center" colspan="4"><b>Total</b></td>
                                    <td class="text-right"><?= $this->_rupiah($total["NPKP"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["NSKP"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["TMK"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["TAU"]); ?></td>
    
                                    <td class="text-right"><?= $this->_rupiah($total["TDHE1"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["TDHE2"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["TM1"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["TM2"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["TM3"]); ?></td>
    
                                    <td class="text-right"><?= $this->_rupiah($total["TM4"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["PLA1"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["PLA2"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["PLA3"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["PLA4"]); ?></td>
    
                                    <td class="text-right"><?= $this->_rupiah($total["totalPKP"]); ?></td>
                                    <td class="text-right"><?= (int) ($total["SKP"] / ($no - 1)); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["totalSKP"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["totalTPP"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["PPH"]); ?></td>
                                    <td class="text-right"><?= $this->_rupiah($total["bersih"]); ?></td>
                                </tr>
                            </tbody>
                        </table>
    
                        <?php
    
                        $data_kepala = $this->db->where("kepala", 1)->where("opd_id", $opd_id)->get('tb_pegawai');
                        $data_jabatan = $this->db->where('jabatan_opd')->get('tb_pegawai');
                        $jabatan = $data_jabatan->row();
                        $num_kepala = $data_kepala->num_rows();
                        $kepala = $data_kepala->row();
                        $data_operator = $this->db->where("opd_id", $opd_id)->where("bendahara_opd", 1)->get('tb_pegawai');
                        $num_operator = $data_operator->num_rows();
                        $operator = $data_operator->row();
                        ?>
    
                        <table style="width:100%; margin-top:50px; font-size:12px" cellspacing="0">
                            <tr>
                                <th width="50%" style="padding:12px;" class="text-center"></th>
                                <th width="50%" class="text-center">Aek Kanopan, <?= $this->bulan[(int) $bulan]; ?> <?= $tahun; ?></th>
                            </tr>
                            <tr>
                                <th class="text-center">Diketahui/Disetujui</th>
                                <th class="text-center">Dibuat Oleh,</th>
                            </tr>
                            <tr>
    
                                <th class="text-center"><?= $num_kepala > 0 ? $kepala->jabatan_opd : "Kepala OPD Tidak Ada"; ?></th>
                                <th class="text-center"><?= $num_operator > 0 ? $operator->jabatan_opd . " " . $opd->nama_opd : "-"; ?></th>
                            </tr>
                            <tr>
                                <th class="text-center">Kabupaten Labuhanbatu Utara</th>
                                <th class="text-center">Kabupaten Labuhanbatu Utara</th>
                            </tr>
                            <tr>
                                <th style="padding:40px;"></th>
                                <th style="padding:40px;"></th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= $num_kepala > 0 ? $kepala->nama : "Jabatan OPD Tidak Ada"; ?></th>
                                <th class="text-center"><?= $num_operator > 0 ? $operator->nama : "User tidak ditemukan"; ?></th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= $num_kepala > 0 ? $kepala->nip : "-"; ?></th>
                                <th class="text-center"><?= $num_operator > 0 ? $operator->nip : "-"; ?></th>
                            </tr>
                        </table>
                    <?php
                    } else {
                        $data['title'] = 'Halaman tidak ditemukan !';
    
                        $this->load->view('template/header');
                        $this->load->view('auth/blocked404');
                        $this->load->view('template/auth_footer');
                    }
                    ?>
                </div>
            </body>
        
    <?php
        }else if($kategori_pegawai=="tks"){
            
        }
    }
    public function cetakRekap($opd_id = false, $tahun = false, $bulan = false, $kategori_pegawai = false)
    {
        $this->db->select("tb_rekap_absen_meta.*, tb_rekap_absen.*, tb_rekap_absen_meta.id id_meta");
        $this->db->from("tb_rekap_absen");
        $this->db->where("tb_rekap_absen.tanggal", $tahun . "-" . $bulan . "-1");
        $this->db->where("tb_rekap_absen.opd_id", $opd_id);
        $this->db->join('tb_rekap_absen_meta', 'tb_rekap_absen_meta.rekap_absen_id = tb_rekap_absen.id');
        $data_rekap = $this->db->get();
        $num_rekap = $data_rekap->num_rows();
        $rekap = $data_rekap->result();

        $this->db->where('id', $opd_id);
        $opd = $this->db->get('tb_opd')->row();

        $no = 1;
    ?>

        <head>
            <title>Cetak - Rekapitulasi Absensi Pegawai Negeri Sipil</title>
            <link rel="icon" href="<?= base_url('assets/') ?>img/logo/logo_labura.jpg">
            <link href="<?= base_url('assets/') ?>datepicker/css/bootstrap.min.css" rel="stylesheet">
            <style type="text/css" media="print">
                @page {
                    size: 21cm 33cm;
                    margin: 5mm 5mm 5mm 5mm;
                    size: landscape;
                }
            </style>
        </head>

        <body>
            <div>
                <?php if ($num_rekap > 0) { ?>
                    <br />
                    <h6 style="font-weight: 800" align="center">
                        REKAPITULASI ABSENSI PEGAWAI NEGERI SIPIL<br />
                        <?= strtoupper($opd->nama_opd); ?> KABUPATEN LABUHANBATU UTARA<br />
                        BULAN <?= strtoupper($this->bulan[(int) $bulan]); ?> TAHUN <?= $tahun; ?>
                    </h6>
                    <br />
                    <table class="table-bordered table-striped" cellpadding="5" style="width:100%; font-size:10px" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th style="padding: 12px">Nama Pegawai</th>
                                <th class="text-center">JHK</th>
                                <th class="text-center">AU</th>
                                <th class="text-center">H</th>
                                <th class="text-center">I</th>
                                <th class="text-center">S</th>
                                <th class="text-center">DL</th>
                                <th class="text-center">AMP</th>
                                <th class="text-center">AMS</th>
                                <th class="text-center">TMK</th>
                                <th class="text-center">TAU</th>
                                <th class="text-center">TDHE1</th>
                                <th class="text-center">TDHE2</th>
                                <th class="text-center">TM1</th>
                                <th class="text-center">TM2</th>
                                <th class="text-center">TM3</th>
                                <th class="text-center">TM4</th>
                                <th class="text-center">PLA1</th>
                                <th class="text-center">PLA2</th>
                                <th class="text-center">PLA3</th>
                                <th class="text-center">PLA4</th>

                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            foreach ($rekap as $rk) {
                                $this->db->where('id', $rk->pegawai_id);
                                if ($kategori_pegawai != false) {
                                    $this->db->where('kategori_pegawai', $kategori_pegawai);
                                }
                                $data_pegawai = $this->db->get('tb_pegawai');
                                $pegawai = $data_pegawai->row();
                                if ($data_pegawai->num_rows() == 0) {
                                    continue;
                                }
                            ?>
                                <tr>
                                    <td class="text-center" scope="row"><?= $no;
                                                                        $no++; ?></td>
                                    <td><?= $pegawai->nama; ?></td>
                                    <td class="text-center"><?= $rk->JHK; ?></td>
                                    <td class="text-center"><?= $rk->AU; ?></td>
                                    <td class="text-center"><?= $rk->H; ?></td>
                                    <td class="text-center"><?= $rk->I; ?></td>
                                    <td class="text-center"><?= $rk->S; ?></td>
                                    <td class="text-center"><?= $rk->DL; ?></td>
                                    <td class="text-center"><?= $rk->AMP; ?></td>
                                    <td class="text-center"><?= $rk->AMS; ?></td>
                                    <td class="text-center"><?= $rk->TMK; ?></td>
                                    <td class="text-center"><?= $rk->TAU; ?></td>
                                    <td class="text-center"><?= $rk->TDHE1; ?></td>
                                    <td class="text-center"><?= $rk->TDHE2; ?></td>
                                    <td class="text-center"><?= $rk->TM1; ?></td>
                                    <td class="text-center"><?= $rk->TM2; ?></td>
                                    <td class="text-center"><?= $rk->TM3; ?></td>
                                    <td class="text-center"><?= $rk->TM4; ?></td>
                                    <td class="text-center"><?= $rk->PLA1; ?></td>
                                    <td class="text-center"><?= $rk->PLA2; ?></td>
                                    <td class="text-center"><?= $rk->PLA3; ?></td>
                                    <td class="text-center"><?= $rk->PLA4; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php
                    $data_kepala = $this->db->where("kepala", 1)->where("opd_id", $opd_id)->get('tb_pegawai');
                    $num_kepala = $data_kepala->num_rows();
                    $kepala = $data_kepala->row();
                    $data_operator = $this->db->where("opd_id", $opd_id)->where("bendahara_opd", 1)->get('tb_pegawai');
                    $num_operator = $data_operator->num_rows();
                    $operator = $data_operator->row();
                    ?>

                    <table style="width:100%; margin-top:50px; font-size:12px" cellspacing="0">
                        <tr>
                            <th width="50%" style="padding:12px;" class="text-center"></th>
                            <th width="50%" class="text-center">Aek Kanopan, <?= $this->bulan[(int) $bulan]; ?> <?= $tahun; ?></th>
                        </tr>
                        <tr>
                            <th class="text-center">Diketahui/Disetujui</th>
                            <th class="text-center">Dibuat Oleh,</th>
                        </tr>
                        <tr>
                            <th class="text-center"><?= $num_kepala > 0 ? $kepala->jabatan_opd : "Jabatan OPD Tidak Ada"; ?></th>
                            <th class="text-center"><?= $num_operator > 0 ? $operator->jabatan_opd . " " . $opd->nama_opd : "-"; ?></th>
                        </tr>
                        <tr>
                            <th class="text-center">Kabupaten Labuhanbatu Utara</th>
                            <th class="text-center">Kabupaten Labuhanbatu Utara</th>
                        </tr>
                        <tr>
                            <th style="padding:40px;"></th>
                            <th style="padding:40px;"></th>
                        </tr>
                        <tr>
                            <th class="text-center"><?= $num_kepala > 0 ? $kepala->nama : "Kepala OPD Tidak Ada"; ?></th>
                            <th class="text-center"><?= $num_operator > 0 ? $operator->nama : "User tidak ditemukan"; ?></th>
                        </tr>
                        <tr>
                            <th class="text-center"><?= $num_kepala > 0 ? $kepala->nip : "-"; ?></th>
                            <th class="text-center"><?= $num_operator > 0 ? $operator->nip : "-"; ?></th>
                        </tr>
                    </table>
                <?php
                } else {
                    $data['title'] = 'Halaman tidak ditemukan !';

                    $this->load->view('template/header');
                    $this->load->view('auth/blocked404');
                    $this->load->view('template/auth_footer');
                }
                ?>
            </div>
        </body>
<?php

    }
    public function hitungbaru(){
        $data = [
                    "page"				=> "hitung/hitungbaru",
            		"title"             => "Hitung Baru",
    	];
		
        $this->load->view('template/default', $data);

    }
    public function hitung_semua()
    {
        if (isset($_POST['opd_id']) && isset($_POST['bulan'])) {
            extract($_POST);
            $jumlah_berhasil = 0;
            // $this->_deleteHitungLama($bulan, $opd_id);

            $datas = array();
            if(isset($data)){
                foreach($data as $pegawai){
                    $pegawai            = explode("_+_", $pegawai);
                    if(isset($pegawai[0]) && isset($pegawai[1]) && isset($pegawai[2]) && isset($pegawai[3]) && isset($pegawai[4])){
                        $pegawai_id     = $pegawai[0];
                        $jenis_pegawai  = $pegawai[1];
                        $nama_pegawai   = $pegawai[2];
                        $skpd_id        = $pegawai[3];
                        $nama_skpd      = $pegawai[4];
                        $datas[] = $this->_hitungPerPegawai("pegawai", $pegawai_id, $nama_pegawai, $skpd_id, $nama_skpd, $opd_id, $nama_opd, $bulan);
                    }
                }
            }    
            if(isset($datatks)){
                foreach($datatks as $tks){
                    $tks                = explode("_+_", $tks);
                    if(isset($pegawai[0]) && isset($pegawai[1]) && isset($pegawai[2]) && isset($pegawai[3]) && isset($pegawai[4])){
                        $pegawai_id     = $tks[0];
                        $jenis_pegawai  = $tks[1];
                        $nama_pegawai   = $tks[2];
                        $skpd_id        = $tks[3];
                        $nama_skpd      = $tks[4];
                        $datas[] = $this->_hitungPerPegawai("tks", $pegawai_id, $nama_pegawai, $skpd_id, $nama_skpd, $opd_id, $nama_opd, $bulan);
                    }
                }
            }    
            
            echo json_encode($datas);
            
        }
        
        return;

    }

    private function _deleteHitungLama($bulan, $opd_id)
    {

        $approved = 0;

        $this->db->where("bulan", $bulan)
                 ->where("opd_id", $opd_id)
                 ->where("status!=", 'disetujui')
                 ->delete('tb_hitung');
        $this->db->where("bulan", $bulan)
                 ->where("opd_id", $opd_id)
                 ->where("status!=", 'disetujui')
                 ->delete('tb_rekap_absen');
        return;
    }

    public function hitungPerpegawai(){
        if (isset($_POST['opd_id']) && isset($_POST['bulan']) && isset($_POST['pegawai'])) {
            extract($_POST);
            $pegawai            = explode("_+_", $pegawai);
            if(isset($pegawai[0]) && isset($pegawai[1]) && isset($pegawai[2]) && isset($pegawai[3]) && isset($pegawai[4])){
                $pegawai_id     = $pegawai[0];
                $jenis_pegawai  = $pegawai[1];
                $nama_pegawai   = $pegawai[2];
                $skpd_id        = $pegawai[3];
                $nama_skpd      = $pegawai[4];

                $opd            = $this->Skpd_model->getSkpdById($opd_id);
                $nama_opd       = isset($opd['nama_opd']) ? $opd['nama_opd'] : null;
                $this->_hitungPerPegawai($jenis_pegawai, $pegawai_id, $nama_pegawai, $skpd_id, $nama_skpd, $opd_id, $nama_opd, $bulan);
                return;
            }
        }
        
        echo json_encode([
            'nama_pegawai'  => isset($nama_pegawai) && $nama_pegawai ? $nama_pegawai : '<span class="text-danger">Error data!</span>',
            'nama_skpd'     => isset($nama_skpd) && $nama_skpd ? $nama_skpd : null,
            'rekap'         => null,
            'tpp'           => null,
        ]);
        return;
    }
    private function _hitungPerPegawai($jenis_pegawai, $pegawai_id, $nama_pegawai, $skpd_id, $nama_skpd, $opd_id, $nama_opd, $bulan)
    {

        $data['JHK']     = 0;
        $data['AMP']     = 0;
        $data['AMS']     = 0;
        $data['AU']      = 0;
        $data['TDHE1']   = 0;
        $data['TDHE2']   = 0;
        $data['TM1']     = 0;
        $data['TM2']     = 0;
        $data['TM3']     = 0;
        $data['TM4']     = 0;
        $data['PLA1']    = 0;
        $data['PLA2']    = 0;
        $data['PLA3']    = 0;
        $data['PLA4']    = 0;
        $data['TAU']     = 0;
        $data['TMK']     = 0;
        $data['H']       = 0;
        $data['S']       = 0;
        $data['I']       = 0;
        $data['DL']      = 0;
        
        $bulan = explode("-", $bulan);
        $tahun = $bulan[1];
        $bulan = $bulan[0];
        $jumlahTanggal = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $tanggal_awal = $tahun . "-" . $bulan . "-1";
        $tanggal_akhir = $tahun . "-" . $bulan . "-" . $jumlahTanggal;
        $begin = new DateTime($tanggal_awal);
        $end = new DateTime($tanggal_akhir);
        $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);


        foreach ($period as $dt) {
            if($dt->format("N")==6 || $dt->format("N")==7){
                continue;
            }
            $data['JHK']++;
            
            $izinkerja      = $this ->db
                                    ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                                    ->where('tb_izin_kerja.pegawai_id', $pegawai_id)
                                    ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                                    ->group_start()
                                        ->where('tb_izin_kerja_meta.tanggal_awal>=', $dt->format("Y-m-d"))
                                        ->where('tb_izin_kerja_meta.tanggal_akhir<=', $dt->format("Y-m-d"))
                                    ->group_end()
                                    ->where("tb_izin_kerja.status", 1)
                                    ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                                    ->get('tb_izin_kerja')->row();

            $absensi = $this->db
                            ->where('pegawai_id', $pegawai_id)
                            ->where('jenis_pegawai', $jenis_pegawai)
                            ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $dt->format("Y-m-d"))
                            ->where('status', 1)
                            ->order_by('id', 'asc')
                            ->get('tb_absensi')->result();
            
            $upacaralibur = $this->db
                                 ->where('tanggal', $dt->format('Y-m-d'))
                                 ->get('tb_upacara_libur')->row();
            
            $jam_masuk              = null;
            $jam_pulang             = null;
            $label_masuk            = null;
            $label_pulang           = null;
            
            $jam_istirahat_keluar   = null;
            $jam_istirahat_masuk    = null;

            $jamKerjaPegawai    = $this->jamKerjaPegawai($pegawai_id, $jenis_pegawai, $dt->format("Y-m-d"));

            foreach($absensi as $abs){
                $jam        = $this->getJamAbsen($abs->jam, $abs->pegawai_id, $abs->jenis_pegawai, $abs->jenis_absen, $jamKerjaPegawai);
                $label      = null;

                if(isset($jam['label'])) $label = $jam['label'];
                
                if(isset($jam['jam_masuk']) && $abs->keterangan){
                    $label = "AMP";
                }
                if(isset($jam['jam_pulang']) && $abs->keterangan){
                    $label = "AMS";
                }

                
                $jam_masuk              = isset($jam['jam_masuk']) && !$jam_masuk 
                                            ? $jam['jam_masuk'] : $jam_masuk;
                $label_masuk            = isset($jam['jam_masuk']) && (!$label_masuk && $jam['jam_masuk']=="Upacara") 
                                            ? "Upacara" : $label_masuk;
                $label_masuk            = isset($jam['jam_masuk']) && (!$label_masuk && $jam['jam_masuk']=="Senam") 
                                            ? "Senam" : $label_masuk;
                $label_masuk            = isset($jam['jam_masuk']) && (!$label_masuk) 
                                            ? $label : $label_masuk;
                $jam_istirahat_masuk    = isset($jam['jam_istirahat']) && !$jam_istirahat_masuk 
                                            ? $jam['jam_istirahat'] : $jam_istirahat_masuk;
                $jam_istirahat_keluar   = isset($jam['jam_selesai_istirahat']) && !$jam_istirahat_keluar 
                                            ? $jam['jam_selesai_istirahat'] : $jam_istirahat_keluar;
                $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang 
                                            ? $jam['jam_pulang'] : $jam_pulang;
                $label_pulang           = isset($jam['jam_pulang']) && !$label_pulang 
                                            ? $label : $jam_pulang;

            }
            
            
            if($izinkerja){
                if($izinkerja->jenis_izin == "Sakit"){
                    $data['S']++;

                }else if($izinkerja->jenis_izin == "Urusan Keluarga"){
                    $data['I']++;
 
                }else if($izinkerja->jenis_izin == "Dinas Luar"){
                    $data['DL']++;
                }

                $data['H']++;
                continue;   
                
            }
            
            if($label_masuk  == "TM1") $data['TM1']++;
            if($label_masuk  == "TM2") $data['TM2']++;
            if($label_masuk  == "TM3") $data['TM3']++;
            if($label_masuk  == "TM4") $data['TM4']++;
            if($label_masuk  == "AMP") $data['AMP']++;
            if($label_pulang == "PLA1") $data['PLA1']++;
            if($label_pulang == "PLA2") $data['PLA2']++;
            if($label_pulang == "PLA3") $data['PLA3']++;
            if($label_pulang == "PLA4") $data['PLA4']++;
            if($label_pulang == "AMS") $data['AMS']++;
            
            if($jam_masuk=="Upacara"){
                $data['AU']++;
            }
            
            if(($label_masuk == "TDHE1" && $label_pulang == "TDHE2") || (!$jam_masuk && !$jam_pulang)){
                $data['TMK']++;
                continue;
            }else{
                if(!$jam_masuk || $label_masuk == "TDHE1"){ 
                    $data['TDHE1']++;
                }else if(!$jam_pulang || $label_pulang == "TDHE2") {
                    $data['TDHE2']++;
                }
            }

            $data['H']++;

        }
        
        $data['opd_id']         = $opd_id;
        $data['skpd_id']        = $skpd_id;
        $data['pegawai_id']     = $pegawai_id;
        $data['jenis_pegawai']  = $jenis_pegawai;
        $data['nama_opd']       = $nama_opd;
        $data['nama_skpd']      = $nama_skpd;
        $data['nama_pegawai']   = $nama_pegawai;
        $data['bulan']          = $bulan."-".$tahun;
        $data['created_by']     = $this->session->userdata('user_id');
        $rekap                  = $data;
        $dataRekapAbsen         = $this->db->where('bulan', $bulan."-".$tahun)->where('opd_id', $opd_id)->where('pegawai_id', $pegawai_id)->where('jenis_pegawai', $jenis_pegawai)->get('tb_rekap_absen')->row();
        
        if($dataRekapAbsen){
            $this->db->where('id', $dataRekapAbsen->id)->update('tb_rekap_absen', $data);
        }else{
            $this->db->insert('tb_rekap_absen', $data);
        }


        $pengaturan     = $jenis_pegawai == 'pegawai' ?
                                $this->db->where('opd_id', null)->where('jenis_pegawai', 'pegawai')->get('tb_peraturan_absensi')->row() :            
                                $this->db->where('opd_id', $opd_id)->where('jenis_pegawai', 'tks')->get('tb_peraturan_absensi')->row();            

        if(!$pengaturan){
            echo json_encode([
                'nama_pegawai'  => $nama_pegawai,
                'nama_skpd'     => $nama_skpd,
                'rekap'         => '<span class="text-primary">Rekap Absen</span>',
                'tpp'           => null,
            ]);
            return;
        }

        if($jenis_pegawai == "pegawai"){
            $dataPegawaiMeta = $this->db
                                    ->select('
                                        tb_pegawai_meta.*, 
                                        tb_jabatan_penghasilan.nama_jabatan, 
                                        tb_jabatan_penghasilan.pkp, 
                                        tb_jabatan_penghasilan.skp, 
                                        tb_jabatan_rangkap.nama_jabatan nama_jabatan_rangkap, 
                                        tb_jabatan_rangkap.pkp pkp_rangkap, 
                                        tb_jabatan_rangkap.skp skp_rangkap, 
                                        tb_jabatan_golongan.pph
                                    ')
                                    ->where('tb_pegawai_meta.pegawai_id', $pegawai_id)
                                    ->join('tb_jabatan_penghasilan', 'tb_jabatan_penghasilan.id=tb_pegawai_meta.jabatan_perbub_tpp', 'left')
                                    ->join('tb_jabatan_penghasilan as tb_jabatan_rangkap', 'tb_jabatan_rangkap.id=tb_pegawai_meta.jabatan_rangkap_perbub', 'left')
                                    ->join('tb_jabatan_golongan', 'tb_jabatan_golongan.id=tb_pegawai_meta.jabatan_golongan', 'left')
                                    ->get('tb_pegawai_meta')->row();
    
            $NPKP = isset($dataPegawaiMeta->pkp) ? $dataPegawaiMeta->pkp : 0;
            $NSKP = isset($dataPegawaiMeta->skp) ? $dataPegawaiMeta->skp : 0;
            $RNPKP = 0;
            $RNSKP = 0;

            if($dataPegawaiMeta->pkp_rangkap && $dataPegawaiMeta->pkp) {
                if ($dataPegawaiMeta->pkp_rangkap < $dataPegawaiMeta->pkp) {
                    $RNPKP = $dataPegawaiMeta->pkp_rangkap * (20 / 100);
                } else if ($dataPegawaiMeta->pkp_rangkap == $dataPegawaiMeta->pkp) {
                    $RNPKP = $dataPegawaiMeta->pkp_rangkap * (50 / 100);
                } else {
                    $RNPKP = $dataPegawaiMeta->pkp_rangkap;
                    $NPKP = 0;
                }
                if ($dataPegawaiMeta->skp_rangkap < $dataPegawaiMeta->skp) {
                    $RNSKP = $dataPegawaiMeta->skp_rangkap * (20 / 100);
                } else if ($dataPegawaiMeta->skp_rangkap == $dataPegawaiMeta->skp) {
                    $RNSKP = $dataPegawaiMeta->skp_rangkap * (50 / 100);
                } else {
                    $RNSKP = $dataPegawaiMeta->skp_rangkap;
                    $NSKP = 0;
                }
            }
            

    
            $DATA_TPP = [
                'opd_id'            => $opd_id,
                'skpd_id'           => $skpd_id,
                'pegawai_id'        => $pegawai_id,
                'nama_opd'          => $nama_opd,
                'nama_skpd'         => $nama_skpd,
                'nama_pegawai'      => $nama_pegawai,
                'bulan'             => $bulan."-".$tahun,
                'NPKP'              => $NPKP,
                'NSKP'              => $NSKP,
                'RNPKP'             => $RNPKP,
                'RNSKP'             => $RNSKP,
                'TMK'               => (($rekap["TMK"] * $pengaturan->TMK) / 100) * ($NPKP + $RNPKP),
                'TDHE1'             => (($rekap["TDHE1"] * $pengaturan->TDHE1) / 100) * ($NPKP + $RNPKP),
                'TDHE2'             => (($rekap["TDHE2"] * $pengaturan->TDHE2) / 100) * ($NPKP + $RNPKP),
                'TM1'               => (($rekap["TM1"] * $pengaturan->TM1) / 100) * ($NPKP + $RNPKP),
                'TM2'               => (($rekap["TM2"] * $pengaturan->TM2) / 100) * ($NPKP + $RNPKP),
                'TM3'               => (($rekap["TM3"] * $pengaturan->TM3) / 100) * ($NPKP + $RNPKP),
                'TM4'               => (($rekap["TM4"] * $pengaturan->TM4) / 100) * ($NPKP + $RNPKP),
                'PLA1'              => (($rekap["PLA1"] * $pengaturan->PLA1) / 100) * ($NPKP + $RNPKP),
                'PLA2'              => (($rekap["PLA2"] * $pengaturan->PLA2) / 100) * ($NPKP + $RNPKP),
                'PLA3'              => (($rekap["PLA3"] * $pengaturan->PLA3) / 100) * ($NPKP + $RNPKP),
                'PLA4'              => (($rekap["PLA4"] * $pengaturan->PLA4) / 100) * ($NPKP + $RNPKP),
                'TAU'               => (($rekap["TAU"] * $pengaturan->TAU) / 100) * ($NPKP + $RNPKP),
                'SKP'               => 100,
                'PPH'               => isset($dataPegawaiMeta->pph) ? $dataPegawaiMeta->pph : 0,
                'created_by'        => $this->session->userdata('user_id'),
            ];
            $hitung = $this->db->where('bulan', $bulan."-".$tahun)->where('opd_id', $opd_id)->where('pegawai_id', $pegawai_id)->get('tb_hitung')->row();
            if($hitung){
                $this->db->where('id', $hitung->id)->update('tb_hitung', $DATA_TPP);
            }else{
                $this->db->insert('tb_hitung', $DATA_TPP);
            }
            echo json_encode([
                'nama_pegawai'  => $nama_pegawai,
                'nama_skpd'     => $nama_skpd,
                'rekap'         => '<span class="text-primary">Rekap Absen</span>',
                'tpp'           => '<span class="text-danger">Hitung TPP</span>',
            ]);
            return;

        }else{
            
            $tksMeta                = $this->db->where('tks_id', $pegawai_id)->get('tb_tks_meta')->row();
            if(!$tksMeta){
                echo json_encode([
                    'nama_pegawai'  => $nama_pegawai,
                    'nama_skpd'     => $nama_skpd,
                    'rekap'         => '<span class="text-primary">Rekap Absen</span>',
                    'tpp'           => null,
                ]);
                return;
            }

            $DATA_TPP = [
                'opd_id'            => $opd_id,
                'skpd_id'           => $skpd_id,
                'pegawai_id'        => $pegawai_id,
                'nama_opd'          => $nama_opd,
                'nama_skpd'         => $nama_skpd,
                'nama_pegawai'      => $nama_pegawai,
                'bulan'             => $bulan."-".$tahun,
                'gaji'              => $tksMeta->gaji,
                'TMK'               => (($rekap["TMK"] * $pengaturan->TMK) / 100) * $tksMeta->gaji,
                'TDHE1'             => (($rekap["TDHE1"] * $pengaturan->TDHE1) / 100) * $tksMeta->gaji,
                'TDHE2'             => (($rekap["TDHE2"] * $pengaturan->TDHE2) / 100) * $tksMeta->gaji,
                'TM1'               => (($rekap["TM1"] * $pengaturan->TM1) / 100) * $tksMeta->gaji,
                'TM2'               => (($rekap["TM2"] * $pengaturan->TM2) / 100) * $tksMeta->gaji,
                'TM3'               => (($rekap["TM3"] * $pengaturan->TM3) / 100) * $tksMeta->gaji,
                'TM4'               => (($rekap["TM4"] * $pengaturan->TM4) / 100) * $tksMeta->gaji,
                'PLA1'              => (($rekap["PLA1"] * $pengaturan->PLA1) / 100) * $tksMeta->gaji,
                'PLA2'              => (($rekap["PLA2"] * $pengaturan->PLA2) / 100) * $tksMeta->gaji,
                'PLA3'              => (($rekap["PLA3"] * $pengaturan->PLA3) / 100) * $tksMeta->gaji,
                'PLA4'              => (($rekap["PLA4"] * $pengaturan->PLA4) / 100) * $tksMeta->gaji,
                'TAU'               => (($rekap["TAU"] * $pengaturan->TAU) / 100) * $tksMeta->gaji,
                'created_by'        => $this->session->userdata('user_id'),
            ];
            $hitung = $this->db->where('bulan', $bulan."-".$tahun)->where('opd_id', $opd_id)->where('pegawai_id', $pegawai_id)->get('tb_hitung_tks')->row();
            if($hitung){
                $this->db->where('id', $hitung->id)->update('tb_hitung_tks', $DATA_TPP);
            }else{
                $this->db->insert('tb_hitung_tks', $DATA_TPP);
            }
            echo json_encode([
                'nama_pegawai'  => $nama_pegawai,
                'nama_skpd'     => $nama_skpd,
                'rekap'         => '<span class="text-primary">Rekap Absen</span>',
                'tpp'           => '<span class="text-danger">Hitung TPP</span>',
            ]);
            return;
        }

    }
    
    public function getSKP($pegawai_id, $jenis_pegawai, $bulan){
        $user_key    = API()->user_key;
        $pass_key    = API()->pass_key;
        $URL         = API()->getSKP;

        $posts       = 'user_key='.$user_key.'&pass_key='.$pass_key.'&pegawai_id='.$pegawai_id.'&jenis_pegawai='.$jenis_pegawai.'&bulan='.$bulan;

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $URL);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $posts);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
        
        echo $results;
    }
    
    private function cobacoba(){
        $dataPegawaiMeta = $this->db
                        ->select('
                                    tb_pegawai_meta.*, 
                                    tb_jabatan_penghasilan.nama_jabatan, 
                                    tb_jabatan_penghasilan.pkp, 
                                    tb_jabatan_penghasilan.skp, 
                                    tb_jabatan_rangkap.nama_jabatan nama_jabatan_rangkap, 
                                    tb_jabatan_rangkap.pkp pkp_rangkap, 
                                    tb_jabatan_rangkap.skp skp_rangkap, 
                                    tb_jabatan_golongan.pph'
                        )
                        ->where('tb_pegawai_meta.pegawai_id', 2804)
                        ->join('tb_jabatan_penghasilan', 'tb_jabatan_penghasilan.id=tb_pegawai_meta.jabatan_perbub_tpp', 'left')
                        ->join('tb_jabatan_penghasilan as tb_jabatan_rangkap', 'tb_jabatan_rangkap.id=tb_pegawai_meta.jabatan_rangkap_perbub', 'left')
                        ->join('tb_jabatan_golongan', 'tb_jabatan_golongan.id=tb_pegawai_meta.jabatan_golongan', 'left')
                        ->get('tb_pegawai_meta')->row();

        echo "<pre>";
        print_r($dataPegawaiMeta);
        return;
    }
    
    private function _getRekapPerHari($jenis_pegawai, $pegawai_id, $skpd_id, $tanggal)
    {
        
        $datas          = array();
        $izinkerja      = $this ->db
                                ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                                ->where('tb_izin_kerja.pegawai_id', $pegawai_id)
                                ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                                ->group_start()
                                    ->where('tb_spt.tgl_pergi>=', $tanggal)
                                    ->where('tb_spt.tgl_kembali<=', $tanggal)
                                ->group_end()
                                ->where("tb_izin_kerja.status", 1)
                                ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                                ->get('tb_izin_kerja')->row();
        $absensi        = $this->db
                               ->where('pegawai_id', $pegawai_id)
                               ->where('jenis_pegawai', $jenis_pegawai)
                               ->where('skpd_id', $skpd_id)
                               ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $dt->format("Y-m-d"))
                               ->where('status', 1)
                               ->order_by('id', 'asc')
                               ->get('tb_absensi')->result();
        $upacaralibur   = $this->db
                               ->where('tanggal', $dt->format('Y-m-d'))
                               ->get('tb_upacara_libur')->row();

        $absensi['AMP']         = 0;
        $absensi['AMS']         = 0;
        $absensi['AU']          = 0;
        $absensi['DL']          = 0;
        $absensi['TDHE1']       = 0;
        $absensi['TDHE2']       = 0;
        $absensi['TM1']         = 0;
        $absensi['TM2']         = 0;
        $absensi['TM3']         = 0;
        $absensi['TM4']         = 0;
        $absensi['PLA1']        = 0;
        $absensi['PLA2']        = 0;
        $absensi['PLA3']        = 0;
        $absensi['PLA4']        = 0;
        $absensi['TAU']         = 0;
        $absensi['TMK']         = 0;
        $absensi['H']           = 0;
        $absensi['I']           = 0;
        $absensi['S']           = 0;
        $jam_masuk              = null;
        $jam_pulang             = null;
        $label_masuk            = null;
        $label_pulang           = null;
        $jamKerjaPegawai        = $this->jamKerjaPegawai($pegawai_id, $jenis_pegawai, $tanggal);

        foreach($absensi as $abs){
            $labels             = array();
            $jam                = $this->getJamAbsen($abs->jam, $abs->pegawai_id, $abs->jenis_pegawai, $abs->jenis_absen, $jamKerjaPegawai);

            if(isset($jam['label'])) $labels[] = $jam['label'];
            if($abs->jenis_absen == 'Absen Upacara' && isset($upacaralibur->kategori)) $labels[] = $upacaralibur->kategori;
            


            $jam_masuk              = isset($jam['jam_masuk']) && !$jam_masuk 
                                        ? $jam['jam_masuk'] : $jam_masuk;

            $label_masuk            = isset($jam['jam_masuk']) && (!$label_masuk && $jam['jam_masuk']=="Upacara") 
                                        ? "Upacara" : $label_masuk;

            $label_masuk            = isset($jam['jam_masuk']) && (!$label_masuk && $jam['jam_masuk']=="Senam") 
                                        ? "Senam" : $label_masuk;

            $label_masuk            = isset($jam['jam_masuk']) && (!$label_masuk) 
                                        ? $label : $label_masuk;


            $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang 
                                        ? $jam['jam_pulang'] : $jam_pulang;
                                        
            $label_pulang           = isset($jam['jam_pulang']) && !$label_pulang 
                                        ? $label : $jam_pulang;

        }
        echo $jam_masuk."<br>";
        echo $label_masuk."<br>";
        echo $jam_pulang."<br>";
        echo $label_pulang."<br>";
        echo "<hr>";
        return;
        if($jam_masuk=="Upacara"){ $data['AU']++; }
        
        if(!$label_masuk && !$label_pulang && $label_masuk){
            
        }


        if(!$label_masuk) $data['TDHE1']++;
        if(!$label_pulang) $data['TDHE2']++;
        if($label_masuk == "TM1") $data['TM1']++;
        if($label_masuk == "TM2") $data['TM2']++;
        if($label_masuk == "TM3") $data['TM3']++;
        if($label_masuk == "TM4") $data['TM4']++;
        if($label_masuk == "AMP") $data['AMP']++;
        if($label_pulang == "PLA1") $data['PLA1']++;
        if($label_pulang == "PLA2") $data['PLA2']++;
        if($label_pulang == "PLA3") $data['PLA3']++;
        if($label_pulang == "PLA4") $data['PLA4']++;
        if($label_pulang == "AMS") $data['AMS']++;

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

    public function _rupiah($number, $align = 'right', $rp = false)
    {
        if ($number > 0) {
            $r = number_format($number);
        } else {
            $r = 0;
        }

        $r = $rp == "rp" ? "Rp. " . $r : $r;
        return  "<div class='text-" . $align . "'>" . $r . "</div>";
    }
}
