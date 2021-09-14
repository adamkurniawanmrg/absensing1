<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">

            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                <?= $this->session->flashdata('pesan'); ?>
                     <table class="table table-striped" id="tableKordinat" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Unit Kerja</th>
                                <th>Koordinat</th>
                                <th>Radius</th>
                                <th width="30">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($skpds as $skpd):?>
                            <?php
                                $indexKordinat  = array_search($skpd['id_skpd'], array_column($kordinats, 'skpd_id'));
                                $kordinat       = isset($kordinats[($indexKordinat!==false ? $indexKordinat : "none")]) ? $kordinats[$indexKordinat] : array();
                            ?>
                                <tr>
                                    <td><?=$skpd['nama_skpd'];?></td>
                                    <td class="text-center"><?=isset($kordinat['latitude']) ? $kordinat['latitude'] : "none";?>, <?=isset($kordinat['longitude']) ? $kordinat['longitude'] : "none";?></td>
                                    <td class="text-center"><?=isset($kordinat['radius']) ? $kordinat['radius']." Meter" : "none";?></td>
                                    <td class="text-center"><a href="pengaturan/setkordinat/<?=$skpd['id_skpd'];?>?token=<?=$_GET['token'];?>" class="btn btn-primary"><em class="ti-settings"></em> SET</a></td>
                                </tr>
                            <?php endforeach;?>

                        </tbody>
                     </table>

                </div>
              </div>
            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>
<script>
    $(document).ready(function() {
        $('#tableKordinat').DataTable({
                "fnInitComplete": function(oSettings, json) {
                    $('#tableKordinat_wrapper .row .col-sm-12').addClass('table-responsive');
                },

        });
    });
</script>

