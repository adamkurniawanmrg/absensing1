<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <center>
                <li class="list-group-item">
                    <img src="/assets/images/check.png" width="150px"><br>
                    <b>Absensi Berhasil</b>
                    <br>
                    Pada <?=date('j F Y, H:i', strtotime($absensi->created_at)) ?> WIB<br>
                    <a href="<?=base_url('/home?token='.$_GET['token'])?>" class="btn btn-warning">Kembali</a>
                </li>    
            </center>
        </ul>
    </div>
</div>

<?php $this->view('template/javascript'); ?>


<?php

    // $dt[4]  = '<td>'.(isset($datametanohp->no_hp) ? $datametanohp->no_hp : null).'</td>';

?>