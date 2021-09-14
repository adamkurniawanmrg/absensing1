<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=base_url('jabatan/setjabatan?token='.$_GET['token']);?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> TAMBAH JABATAN</a>
            </li>

            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                <?= $this->session->flashdata('pesan'); ?>
                     <table class="table table-striped" id="tableList" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Jabatan</th>
                                <th>PKP</th>
                                <th>SKP</th>
                                <th>Total</th>
                                <th width="30">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($jabatans as $j):?>
                                <tr>
                                    <td><?=$j['nama_jabatan'];?></td>
                                    <td><?="Rp. ".number_format($j['pkp']);?></td>
                                    <td><?="Rp. ".number_format($j['skp']);?></td>
                                    <td><?="Rp. ".number_format($j['total']);?></td>
                                    <td class="text-center">
                                        <a href="jabatan/setjabatan/<?=$j['id'];?>?token=<?=$_GET['token'];?>" class="btn btn-primary"><em class="ti-settings"></em></a>
                                        <a href="jabatan/hapus/<?=$j['id'];?>?token=<?=$_GET['token'];?>" onclick="if(!confirm('Apakah ada yakin untuk menghapus ?')){return false;}" class="btn btn-danger"><em class="ti-trash"></em></a>
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

