<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=base_url('golongan/setgolongan?token='.$_GET['token']);?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> TAMBAH GOLONGAN</a>
            </li>

            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                <?= $this->session->flashdata('pesan'); ?>
                     <table class="table table-striped" id="tableList" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Golongan</th>
                                <th>PPH</th>
                                <th width="30">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($golongans as $g):?>
                                <tr>
                                    <td><?=$g['nama_golongan'];?></td>
                                    <td><?=$g['pph'];?>%</td>
                                    <td class="text-center">
                                        <a href="golongan/setgolongan/<?=$g['id'];?>?token=<?=$_GET['token'];?>" class="btn btn-primary"><em class="ti-settings"></em></a>
                                        <a href="golongan/hapus/<?=$g['id'];?>?token=<?=$_GET['token'];?>" onclick="if(!confirm('Apakah ada yakin untuk menghapus ?')){return false;}" class="btn btn-danger"><em class="ti-trash"></em></a>
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
        $('#tableList').DataTable({
            "fnInitComplete": function(oSettings, json) {
                $('#tableList_wrapper .row .col-sm-12').addClass('table-responsive');
            },

        });
    });
</script>

