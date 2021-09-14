<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?= base_url("izinkerja/addizin?token=".$_GET['token']) ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
                   Buat Izin Kerja
                </a>

            </li>
            <li class="list-group-item">
                <?=$this->session->flashdata('pesan');?>

                <form method="get">
                    <input type="hidden" name="token" value="<?=$_GET['token'];?>">
                    <div class="row mb-3">
                        <div class="col-md-2 pt-2">Bulan</div>
                        <?php
                            $bulan   = date("m-Y");
                        ?>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= isset($_GET['bulan']) ? $_GET['bulan'] : $bulan ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><button class="btn btn-outline-primary btn-sm">Tampilkan</button></div>
                    </div>
                </form>

            </li>
            <li class="list-group-item mb-hide">
                <div class="table-responsive">
                    <table class="table table-striped" id="tableIzinKerja" width="100%" cellspacing="0">
    
                        <thead>
                            <tr>
                                <th>Dari Tanggal</th>
                                <th>Sampai Tanggal</th>
                                <th>Nama</th>
                                <th>Nama Unit Kerja</th>
                                <th>Jenis Izin</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </li>
            
            <li class="list-group-item mb-show">
                <div class="card-header row">
                    <h3>Permohonan Izin Kerja Menunggu Persetujuan</h3>
                </div>
                <div class="accordion mt-3" id="izinKerjaMenunggu" role="tablist">
                    
                <?php 
                    function getTanggal($tanggal, $tanggaldanwaktu=false){
                        $totime = strtotime($tanggal);
                        $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
                        $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        $tanggaldanwaktu = $tanggaldanwaktu ? " " . date('H:i', $totime)." WIB" : null;
                        return $hari[date("w", $totime)] . ", " . date('d', $totime)." " . $bulan[date('n', $totime)].date(' Y', $totime).$tanggaldanwaktu;
                    }
                    if(count($izinKerjaUnverified)>0):
                    foreach($izinKerjaUnverified as $ikv):
                        $skpd   = isset($skpds[$ikv['skpd_id']]) ? $skpds[$ikv['skpd_id']] : ['nama_skpd'=>'undefined'];
                        
                        $indexPegawai   = array_search($ikv['pegawai_id'], array_column($pegawais, 'user_id'));
                        $indexTks       = array_search($ikv['pegawai_id'], array_column($tkss, 'user_id'));
                        $indexAprover   = $ikv['aproved_by'] ? array_search($ikv['aproved_by'], array_column($pegawais, 'user_id')) : null;
            
                        $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
                        $indexTks       = $indexTks!==false ? $indexTks : "none"; 
                        $indexAprover   = $indexAprover!==false ? $indexAprover : "none"; 

            
                        $pegawai        = $ikv['jenis_pegawai']=='pegawai' ? 
                                          (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                                          (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
                        
                        $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];

                        $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
                        $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
            
                        $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
                        $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
                        $totimeDisetujuiPada    = strtotime($ikv['aproved_at']);
                        $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";

                
                ?>

                  <div class="card">
                    <div class="card-header" role="tab" id="headIzinMenunggu">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#dataIzinMenunggu_<?=$ikv['id'];?>" aria-expanded="false" aria-controls="dataIzinMenunggu_<?=$ikv['id'];?>">
                          <?=$gelarDepan.$pegawai['nama'].$gelarBelakang;?>
                                    <div style="font-size: 12px; margin-top: 8px">
                                        <span class="label btn-<?=$ikv['status']==null ? 'warning' : ($ikv['status']==1 ? 'success' : 'danger');?>" style="font-size: 12px;">
                                            <?=$ikv['status']==null ? 'Menunggu' : ($ikv['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>

                        </a>
                      </h6>
                    </div>
                    <div id="dataIzinMenunggu_<?=$ikv['id'];?>" class="collapse" role="tabpanel" aria-labelledby="dataIzinMenunggu_<?=$ikv['id'];?>" data-parent="#izinKerjaMenunggu">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Dikirim Pada</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['created_at'], true);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Unit Kerja</div>
                                    <div class="col-md-7"><?=$skpd['nama_skpd'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Dari Tanggal</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['tanggal_awal']);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Sampai Tanggal</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['tanggal_akhir']);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Jenis Izin</div>
                                    <div class="col-md-7"><?=$ikv['jenis_izin'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Lampiran</div>
                                    <div class="col-md-7"><a href="<?=base_url("resources/berkas/izin_kerja/".$ikv['file_izin']);?>">Lampiran</a></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Status</div>
                                    <div class="col-md-7">
                                        <span class="label btn-<?=$ikv['status']==null ? 'warning' : ($ikv['status']==1 ? 'success' : 'danger');?>">
                                            <?=$ikv['status']==null ? 'Menunggu' : ($ikv['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>

                    </div>
                  </div>
                  <?php 
                    endforeach;
                    else:
                        echo "<center>Tidak ada data!</center>";
                    endif;
                  ?>
                  
                </div>
            </li>
            <li class="list-group-item mb-show mt-3">
                <div class="row card-header">
                    <h3>Permohonan Izin Kerja Sudah Di Proses</h3>
                </div>
                <div class="accordion mt-3" id="izinKerja" role="tablist">
                    
                <?php 
                    if(count($izinKerjaVerified)>0):
                    foreach($izinKerjaVerified as $ikv):
                        $skpd   = isset($skpds[$ikv['skpd_id']]) ? $skpds[$ikv['skpd_id']] : ['nama_skpd'=>'undefined'];
                        
                        $indexPegawai   = array_search($ikv['pegawai_id'], array_column($pegawais, 'user_id'));
                        $indexTks       = array_search($ikv['pegawai_id'], array_column($tkss, 'user_id'));
                        $indexAprover   = $ikv['aproved_by'] ? array_search($ikv['aproved_by'], array_column($pegawais, 'user_id')) : null;

                        $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
                        $indexTks       = $indexTks!==false ? $indexTks : "none"; 
                        $indexAprover   = $indexAprover!==false ? $indexAprover : "none"; 

            
                        $pegawai        = $ikv['jenis_pegawai']=='pegawai' ? 
                                          (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                                          (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
                        
                        $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];

                        $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
                        $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
            
                        $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
                        $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
                        $totimeDisetujuiPada    = strtotime($ikv['aproved_at']);
                        $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";

                
                ?>

                  <div class="card">
                    <div class="card-header" role="tab" id="headIzin">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#dataIzin_<?=$ikv['id'];?>" aria-expanded="false" aria-controls="dataIzin_<?=$ikv['id'];?>">
                          <?=$gelarDepan.$pegawai['nama'].$gelarBelakang;?>
                                    <div style="font-size: 12px; margin-top: 8px">
                                        <span class="label btn-<?=$ikv['status']==null ? 'warning' : ($ikv['status']==1 ? 'success' : 'danger');?>" style="font-size: 12px;">
                                            <?=$ikv['jenis_izin'];?> <?=$ikv['status']==null ? 'Menunggu' : ($ikv['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>

                        </a>
                      </h6>
                    </div>
                    <div id="dataIzin_<?=$ikv['id'];?>" class="collapse" role="tabpanel" aria-labelledby="dataIzin_<?=$ikv['id'];?>" data-parent="#izinKerja">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Dikirim Pada</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['created_at'], true);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Unit Kerja</div>
                                    <div class="col-md-7"><?=$skpd['nama_skpd'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Dari Tanggal</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['tanggal_awal']);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Sampai Tanggal</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['tanggal_akhir']);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Jenis Izin</div>
                                    <div class="col-md-7"><?=$ikv['jenis_izin'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Lampiran</div>
                                    <div class="col-md-7"><a href="<?=base_url("resources/berkas/izin_kerja/".$ikv['file_izin']);?>">Lampiran</a></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Status</div>
                                    <div class="col-md-7">
                                        <span class="label btn-<?=$ikv['status']==null ? 'warning' : ($ikv['status']==1 ? 'success' : 'danger');?>">
                                            <?=$ikv['status']==null ? 'Menunggu' : ($ikv['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Diproses oleh</div>
                                    <div class="col-md-7"><?=$aproverGelarDepan.$aprover['nama'].$aproverGelarBelakang;?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Diproses pada</div>
                                    <div class="col-md-7"><?=getTanggal($ikv['aproved_at'], true);?></div>
                                </div>
                            </li>
                        </ul>

                    </div>
                  </div>
                  <?php 
                    endforeach;
                    else:
                        echo "<center>Tidak ada data!</center>";
                    endif;
                  ?>
                  
                </div>
        
            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        getFilter();

        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        function getFilter() {
            $('#tableIzinKerja').DataTable().destroy();
            $('#tableIzinKerja').DataTable({
                autoWidth : false,
                ordering : false,
                ajax : {
                    "url": "<?php echo site_url('izinkerja/getDataIzinKerja?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan" : "<?=isset($_GET['bulan']) ? $_GET['bulan'] : date("m-Y");?>",

                    }
                },
                "fnInitComplete": function(oSettings, json) {
                    $('#tableIzinKerja_wrapper .row .col-sm-12').addClass('table-responsive');
                },
                columnDefs: [
                    {orderable: false, targets: 0 }
                ]
            });
        }


    });
</script>


