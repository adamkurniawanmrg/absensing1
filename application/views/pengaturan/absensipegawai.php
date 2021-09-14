<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">

            
            <li class="list-group-item">
                 <table class="table table-striped table-responsive" id="tablePengaturanAbsensi" cellspacing="0">
                    <thead>
                        <tr>
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
                        <tr>
                            <td class="text-center"><?=isset($pengaturanabsensi['TMK']) ? $pengaturanabsensi['TMK']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TAU']) ? $pengaturanabsensi['TAU']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TDHE1']) ? $pengaturanabsensi['TDHE1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TDHE2']) ? $pengaturanabsensi['TDHE2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM1']) ? $pengaturanabsensi['TM1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM2']) ? $pengaturanabsensi['TM2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM3']) ? $pengaturanabsensi['TM3']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['TM4']) ? $pengaturanabsensi['TM4']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA1']) ? $pengaturanabsensi['PLA1']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA2']) ? $pengaturanabsensi['PLA2']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA3']) ? $pengaturanabsensi['PLA3']."%" : null;?></td>
                            <td class="text-center"><?=isset($pengaturanabsensi['PLA4']) ? $pengaturanabsensi['PLA4']."%" : null;?></td>
                            <td class="text-center">
                                <a href="pengaturan/setpengaturanabsensipegawai?token=<?=$_GET['token'];?>" class="btn btn-sm btn-primary"><em class="ti-settings"></em> SET</a>
                            </td>
                        </tr>

                    </tbody>
                 </table>

            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>

