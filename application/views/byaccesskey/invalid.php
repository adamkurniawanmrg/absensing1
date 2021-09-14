<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <center>
                <li class="list-group-item">
                    <img src="/assets/images/close.png" width="150px"><br>
                    <b>INVALID!</b>
                    <br>
                    <button onclick="window.close();" class="btn btn-warning">Tutup</a>
                </li>    
            </center>
        </ul>
    </div>
</div>

<?php $this->view('template/javascript'); ?>
