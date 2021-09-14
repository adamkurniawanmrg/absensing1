<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=base_url('pengaturan/setkordinattambahan?token='.$_GET['token']);?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> TAMBAH KORDINAT</a>
            </li>

            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                <?= $this->session->flashdata('pesan'); ?>
                     <table class="table table-striped" id="tableKordinatTambahan" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Kordinat</th>
                                <th>Nama OPD</th>
                                <th>Koordinat</th>
                                <th>Radius</th>
                                <th width="30">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($kordinat_tambahan as $kt):?>
                                <tr>
                                    <td><?=$kt['nama_kordinat'];?></td>
                                    <td><?=$kt['nama_skpd'];?></td>
                                    <td class="text-center"><?=$kt['latitude'];?>, <?=$kt['longitude'];?></td>
                                    <td class="text-center"><?=$kt['radius'];?></td>
                                    <td class="text-center">
                                        <a href="pengaturan/setkordinattambahan/<?=$kt['id'];?>?token=<?=$_GET['token'];?>" class="btn btn-primary"><em class="ti-settings"></em></a>
                                        <a href="pengaturan/hapuskordinattambahan/<?=$kt['id'];?>?token=<?=$_GET['token'];?>" onclick="if(!confirm('Apakah ada yakin untuk menghapus ?')){return false;}" class="btn btn-danger"><em class="ti-trash"></em></a>
                                    </td>
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
        $('#tableKordinatTambahan').DataTable({
            "fnInitComplete": function(oSettings, json) {
                $('#tableKordinatTambahan_wrapper .row .col-sm-12').addClass('table-responsive');
            },

        });
    });
</script>

