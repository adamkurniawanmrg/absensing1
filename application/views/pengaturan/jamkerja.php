<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">

            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="pt-2 pb-2">
                    <a href="pengaturan/setjamkerja?token=<?=$_GET['token'];?>" class="btn btn-sm btn-primary"><em class="ti-timer"></em> BUAT JAM KERJA BARU</a>
                </div>
            </li>
            
            <li class="list-group-item">
                 <table class="table table-striped" id="tableJamkerja" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Jam Kerja</th>
                            <th width="30">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($jam_kerjas as $jam_kerja):?>
                            <tr>
                                <td class="text-left"><?=$jam_kerja['nama_jam_kerja'];?><?= $jam_kerja['is_default'] == 1 ? '<br><small class="text-primary">Default</small>' : null;?></td>
                                <td class="text-center">
                                    <a href="pengaturan/setjamkerja/<?=$jam_kerja['id'];?>?token=<?=$_GET['token'];?>" class="btn btn-primary"><em class="ti-settings"></em> SET</a>
                                    <a href="pengaturan/hapusjamkerja/<?=$jam_kerja['id'];?>?token=<?=$_GET['token'];?>" onclick="if(!confirm('Apakah anda yakin untuk menghapus ?')){return false;}" class="btn btn-danger"><em class="ti-trash"></em> HAPUS</a>
                                </td>
                            </tr>
                        <?php endforeach;?>

                    </tbody>
                 </table>

            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>
<script>
    $(document).ready(function() {
        $('#tableJamkerja').DataTable({
            "fnInitComplete": function(oSettings, json) {
                $('#tableJamkerja_wrapper .row .col-sm-12').addClass('table-responsive');
            },

        });
    });
</script>

