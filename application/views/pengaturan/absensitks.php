<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">

            
            <li class="list-group-item">
                 <table class="table table-striped" id="tablePengaturanAbsensi" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama OPD</th>
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
                            <th width="30">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($skpds as $skpd):?>
                        <?php
                            $nama_skpd = explode(" ", $skpd['nama_skpd']);
                            if(
                                $nama_skpd[0]=='Dinas' ||
                                $nama_skpd[0]=='Badan' ||
                                $nama_skpd[0]=='Sekretariat' ||
                                $nama_skpd[0]=='Kecamatan' ||
                                $nama_skpd[0]=='Inspektorat'
                            ){}else{continue;}
                            $pengaturan = $this->db->where('jenis_pegawai', 'tks')->where('opd_id', $skpd['id_skpd'])->get('tb_peraturan_absensi')->row_array();
                        ?>
                            <tr>
                                <td class="text-left"><?=$skpd['nama_skpd'];?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TMK']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TAU']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TDHE1']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TDHE2']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TM1']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TM2']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TM3']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['TM4']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['PLA1']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['PLA2']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['PLA3']."%" : null;?></td>
                                <td class="text-center"><?=$pengaturan ? $pengaturan['PLA4']."%" : null;?></td>
                                <td class="text-center">
                                    <a href="pengaturan/setpengaturanabsensitks/<?=$skpd['id_skpd'];?>?token=<?=$_GET['token'];?>" class="btn btn-primary"><em class="ti-settings"></em> SET</a>
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
        $('#tablePengaturanAbsensi').DataTable({
            "fnInitComplete": function(oSettings, json) {
                $('#tablePengaturanAbsensi_wrapper .row .col-sm-12').addClass('table-responsive');
            }
        });

    });
</script>

