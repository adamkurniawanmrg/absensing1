<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
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
                    <table class="table table-striped" id="tableAbsensiManual" cellpadding="8">
                        <thead>
                            <tr>
                                <th width="120">Jam</th>
                                <th>
                                    <div class="mb-show-flex">Detail</div>
                                    <div class="row mb-hide-flex">
                                        <div class="col-md-4 text-center">Nama</div>
                                        <div class="col-md-4 text-center">Keterangan</div>
                                        <div class="col-md-4 text-center">Status</div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </table>
            </li>

            <li class="list-group-item mb-show">
                <div class="card-header row">
                    <h3>Absen Manual Menunggu Persetujuan</h3>
                </div>
                <div class="accordion mt-3" id="absenManualMenunggu" role="tablist">
                    
                <?php 
                    function getTanggal($tanggal, $tanggaldanwaktu=false){
                        $totime = strtotime($tanggal);
                        $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
                        $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        $tanggaldanwaktu = $tanggaldanwaktu ? " " . date('H:i', $totime)." WIB" : null;
                        return $hari[date("w", $totime)] . ", " . date('d', $totime)." " . $bulan[date('n', $totime)].date(' Y', $totime).$tanggaldanwaktu;
                    }
                    if(count($absenManualMenunggu)>0):
                    foreach($absenManualMenunggu as $amm):
                        $skpd   = isset($skpds[$amm['skpd_id']]) ? $skpds[$amm['skpd_id']] : ['nama_skpd'=>'undefined'];
                        
                        $indexPegawai   = array_search($amm['pegawai_id'], array_column($pegawais, 'user_id'));
                        $indexTks       = array_search($amm['pegawai_id'], array_column($tkss, 'user_id'));
                        $indexAprover   = $amm['aproved_by'] ? array_search($amm['aproved_by'], array_column($pegawais, 'user_id')) : null;
            
                        $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
                        $indexTks       = $indexTks!==false ? $indexTks : "none"; 
                        $indexAprover   = $indexAprover!==false ? $indexAprover : "none"; 

            
                        $pegawai        = $amm['jenis_pegawai']=='pegawai' ? 
                                          (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                                          (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
                        
                        $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];

                        $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
                        $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
            
                        $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
                        $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
                        $totimeDisetujuiPada    = strtotime($amm['aproved_at']);
                        $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";

                
                ?>

                  <div class="card">
                    <div class="card-header" role="tab" id="headIzinMenunggu">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#dataIzinMenunggu_<?=$amm['id'];?>" aria-expanded="false" aria-controls="dataIzinMenunggu_<?=$amm['id'];?>">
                          <?=$gelarDepan.$pegawai['nama'].$gelarBelakang;?>
                                    <div style="font-size: 12px; margin-top: 8px">
                                        <span class="label btn-<?=$amm['status']==null ? 'warning' : ($amm['status']==1 ? 'success' : 'danger');?>" style="font-size: 12px;">
                                            <?=$amm['status']==null ? 'Menunggu' : ($amm['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>

                        </a>
                      </h6>
                    </div>
                    <div id="dataIzinMenunggu_<?=$amm['id'];?>" class="collapse" role="tabpanel" aria-labelledby="dataIzinMenunggu_<?=$amm['id'];?>" data-parent="#absenManualMenunggu">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Dikirim Pada</div>
                                    <div class="col-md-7"><?=getTanggal($amm['jam'], true);?></div>
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
                                    <div class="col-md-5">Jenis Absen</div>
                                    <div class="col-md-7"><?=$amm['jenis_absen'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Keterangan</div>
                                    <div class="col-md-7"><?=$amm['keterangan'];?></div>
                                </div>
                            </li>

                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Status</div>
                                    <div class="col-md-7">
                                        <span class="label btn-<?=$amm['status']==null ? 'warning' : ($amm['status']==1 ? 'success' : 'danger');?>">
                                            <?=$amm['status']==null ? 'Menunggu' : ($amm['status']==1 ? 'Disetujui' : 'Ditolak');?>
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
                    if(count($absenManual)>0):
                    foreach($absenManual as $amv):
                        $skpd   = isset($skpds[$amv['skpd_id']]) ? $skpds[$amv['skpd_id']] : ['nama_skpd'=>'undefined'];
                        
                        $indexPegawai   = array_search($amv['pegawai_id'], array_column($pegawais, 'user_id'));
                        $indexTks       = array_search($amv['pegawai_id'], array_column($tkss, 'user_id'));
                        $indexAprover   = $amv['aproved_by'] ? array_search($amv['aproved_by'], array_column($pegawais, 'user_id')) : null;

                        $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
                        $indexTks       = $indexTks!==false ? $indexTks : "none"; 
                        $indexAprover   = $indexAprover!==false ? $indexAprover : "none"; 

            
                        $pegawai        = $amv['jenis_pegawai']=='pegawai' ? 
                                          (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                                          (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
                        
                        $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];

                        $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
                        $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
            
                        $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
                        $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
                        $totimeDisetujuiPada    = strtotime($amv['aproved_at']);
                        $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";

                
                ?>

                  <div class="card">
                    <div class="card-header" role="tab" id="headIzin">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#dataIzin_<?=$amv['id'];?>" aria-expanded="false" aria-controls="dataIzin_<?=$amv['id'];?>">
                          <?=$gelarDepan.$pegawai['nama'].$gelarBelakang;?>
                                    <div style="font-size: 12px; margin-top: 8px">
                                        <span class="label btn-<?=$amv['status']==null ? 'warning' : ($amv['status']==1 ? 'success' : 'danger');?>" style="font-size: 12px;">
                                            <?=$amv['jenis_izin'];?> <?=$amv['status']==null ? 'Menunggu' : ($amv['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>

                        </a>
                      </h6>
                    </div>
                    <div id="dataIzin_<?=$amv['id'];?>" class="collapse" role="tabpanel" aria-labelledby="dataIzin_<?=$amv['id'];?>" data-parent="#izinKerja">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Dikirim Pada</div>
                                    <div class="col-md-7"><?=getTanggal($amv['jam'], true);?></div>
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
                                    <div class="col-md-5">Jenis Absen</div>
                                    <div class="col-md-7"><?=$amv['jenis_absen'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Keterangan</div>
                                    <div class="col-md-7"><?=$amv['keterangan'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Status</div>
                                    <div class="col-md-7">
                                        <span class="label btn-<?=$amv['status']==null ? 'warning' : ($amv['status']==1 ? 'success' : 'danger');?>">
                                            <?=$amv['status']==null ? 'Menunggu' : ($amv['status']==1 ? 'Disetujui' : 'Ditolak');?>
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
                                    <div class="col-md-7"><?=getTanggal($amv['aproved_at'], true);?></div>
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
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });


        // $("#jenis_pegawai").change(function() {
        //     getPegawai();
        // });
        // $("#skpd_id").change(function() {
        //     if($("#jenis_pegawai").val()==""){
        //         return;
        //     }
        //     getPegawai();
        // });
        // function getPegawai(){
        //     $('#pegawai_id').find('option').remove().end();
        //     var skpd_id = $("#skpd_id").val();
        //     var jenis_pegawai = $("#jenis_pegawai").val();
        //     $.ajax({
        //         type: "post",
        //         url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'json/selectOptionPegawaiBySkpd?token=' . $_GET['token']; ?>" : "<?= base_url() . 'json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
        //         data: {
        //             'skpd_id': skpd_id
        //         },
        //         success: function(data) {
        //             $("#pegawai_id").html(data);
        //         }
        //     });

        // }

        getFilter();

        function getFilter() {
            var bulan            = $("#bulan").val();
            // var jenis_pegawai    = $("#jenis_pegawai").val();
            // var skpd_id          = $("#skpd_id").val();
            // var pegawai_id       = $("#pegawai_id").val();
             
            $('#tableAbsensiManual').DataTable().destroy();
            $('#tableAbsensiManual').DataTable({
                "autoWidth": false,
                "ordering":false,
                "ajax": {
                    "url": "<?php echo site_url('absenmanual/getDataAbsenManual?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan"         : bulan,
                        // "jenis_pegawai" : jenis_pegawai,
                        // "skpd_id"       : skpd_id,
                        // "pegawai_id"    : pegawai_id
                    }
                },

            });
        }
    
    });
</script>