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
                <div class="table-responsive">
                    <table class="table table-striped" id="tableAbsensiManual" cellpadding="8">
                        <thead>
                            <tr>
                                <th>Tanggal/Waktu</th>
                                <th>Nama Pegawai</th>
                                <th>Nama Unit Kerja</th>
                                <th>Jenis Absen</th>
                                <th>Keterangan</th>
                                <th>Lampiran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </li>
            <li class="list-group-item mb-show">

                <div class="accordion mt-3" id="absenmanualMenunggu" role="tablist">
                    
                <?php 
                    function getTanggal($tanggal, $tanggaldanwaktu=false){
                        $totime = strtotime($tanggal);
                        $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
                        $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        $tanggaldanwaktu = $tanggaldanwaktu ? " " . date('H:i', $totime)." WIB" : null;
                        return $hari[date("w", $totime)] . ", " . date('d', $totime)." " . $bulan[date('n', $totime)].date(' Y', $totime).$tanggaldanwaktu;
                    }
                    if(count($absenmanual)>0):
                    foreach($absenmanual as $am):
                        $skpd   = isset($skpds[$am['skpd_id']]) ? $skpds[$am['skpd_id']] : ['nama_skpd'=>'undefined'];
                        
                        $indexPegawai   = array_search($am['pegawai_id'], array_column($pegawais, 'user_id'));
                        $indexTks       = array_search($am['pegawai_id'], array_column($tkss, 'user_id'));
                        $indexAprover   = $am['approved_by'] ? array_search($am['approved_by'], array_column($pegawais, 'user_id')) : null;
                        
            
                        $pegawai        = $am['jenis_pegawai']=='pegawai' ? 
                                          (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']) : 
                                          (isset($tkss[$indexTks])          ? $tkss[$indexTks]         : ['nama'=>'undefined']);
                        
                        $aprover        = isset($pegawais[$indexAprover]) ? $pegawais[$indexAprover] : ['nama'=>'undefined'];

                        $gelarDepan      = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
                        $gelarBelakang   = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
            
                        $aproverGelarDepan      = isset($aprover['gelar_depan']) && $aprover['gelar_depan'] && $aprover['gelar_depan']!=="" ? $aprover['gelar_depan']."." : null;
                        $aproverGelarBelakang   = isset($aprover['gelar_belakang']) && $aprover['gelar_belakang'] && $aprover['gelar_belakang']!="" ? " ".$aprover['gelar_belakang'] : null;
                        $totimeDisetujuiPada    = strtotime($am['approved_at']);
                        $disetujuiPada          = $this->hari[date("w", $totimeDisetujuiPada)] . ", " . date('d', $totimeDisetujuiPada)." " . $this->bulan[date('n', $totimeDisetujuiPada)]." " . date('Y - H:i', $totimeDisetujuiPada)." WIB";

                
                ?>

                  <div class="card">
                    <div class="card-header" role="tab" id="headAbsenmanualMenunggu">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#dataAbsenmanualMenunggu_<?=$am['id'];?>" aria-expanded="false" aria-controls="dataAbsenmanualMenunggu_<?=$am['id'];?>">
                          <?=$gelarDepan.$pegawai['nama'].$gelarBelakang;?>
                            <div style="font-size: 12px; margin-top: 8px">
                                <span class="label btn-<?=$am['status']==null ? 'warning' : ($am['status']==1 ? 'success' : 'danger');?>" style="font-size: 12px;">
                                    <?=$am['status']==null ? 'Menunggu' : ($am['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                </span>
                            </div>

                        </a>
                      </h6>
                    </div>
                    <div id="dataAbsenmanualMenunggu_<?=$am['id'];?>" class="collapse" role="tabpanel" aria-labelledby="dataAbsenmanualMenunggu_<?=$am['id'];?>" data-parent="#absenmanualMenunggu">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Unit Kerja</div>
                                    <div class="col-md-7"><?=$skpd['nama_skpd'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Jam</div>
                                    <div class="col-md-7"><?=getTanggal($am['jam'], true);?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Jenis Absen</div>
                                    <div class="col-md-7"><?=$am['jenis_absen'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Keterangan</div>
                                    <div class="col-md-7"><?=$am['keterangan'];?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Berkas</div>
                                    <div class="col-md-7"><a href="<?=base_url($am['file_absensi']);?>">Berkas</a></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-5">Status</div>
                                    <div class="col-md-7">
                                        <span class="label btn-<?=$am['status']==null ? 'warning' : ($am['status']==1 ? 'success' : 'danger');?>">
                                            <?=$am['status']==null ? 'Menunggu' : ($am['status']==1 ? 'Disetujui' : 'Ditolak');?>
                                        </span>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <a href="<?=site_url('verifikasi/prosesabsenmanual/tolak/' . $am['absensi_id'].'?token=' . $_GET['token']);?>" onclick="return confirm('Yakin tolak Absen Manual ?')"  class="btn btn-danger btn-sm" style="padding: 5px 15px" title="Tolak izin kerja"><i class="ti-close"></i> Tolak</a>
                                <a href="<?=site_url('verifikasi/prosesabsenmanual/setuju/' . $am['absensi_id'].'?token=' . $_GET['token']);?>" onclick="return confirm('Yakin setujui Absen Manual ?')"  class="btn btn-success btn-sm" style="padding: 5px 15px" title="Setuju izin kerja"><i class="ti-check"></i> Setuju</a>
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

        getFilter();

        function getFilter() {
            var bulan = $("#bulan").val();
            $('#tableAbsensiManual').DataTable().destroy();
            $('#tableAbsensiManual').DataTable({
                "autoWidth": false,
                "ordering":false,
                "ajax": {
                    "url": "<?php echo site_url('verifikasi/getDataAbsenManual?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan": bulan,
                    }
                },
                'columnDefs': [{
                        "width": "100",
                        "targets": [7]
                    },
                    {
                        "width": "200",
                        "targets": [1]
                    },
                    {
                        "className": "text-center",
                        "targets": [4, 5]
                    },
                    {
                        "className": "text-right",
                        "targets": [7]
                    },
                ]

            });
        }
    
    });
</script>