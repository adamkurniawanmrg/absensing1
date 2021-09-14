<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group">
                        <label for="skpd_id">OPD</label>
                        <select id="skpd_id" name="skpd_id" class="form-control select2">
                            <option value="">Semua OPD</option>
                            <?php foreach ($skpds as $skpd) { 
                                $nama_skpd      = explode(" ", $skpd['nama_skpd']);
                                if(
                                    $skpd['nama_skpd']=='Satuan Polisi Pamong Praja' ||
                                    $nama_skpd[0]=='Dinas' ||
                                    $nama_skpd[0]=='Badan' ||
                                    $nama_skpd[0]=='Sekretariat' ||
                                    $nama_skpd[0]=='Kecamatan' ||
                                    $nama_skpd[0]=='Inspektorat'
                                ){}else{continue;}

                            ?>
                                <option value="<?= $skpd['id_skpd']."-".$skpd['nama_skpd']; ?>" <?=$skpd['id_skpd']==$kordinat['skpd_id'] ? "selected" : null;?>><?= $skpd['nama_skpd']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_kordinat">Nama Kordinat</label>
                        <input type="text" name="nama_kordinat" class="form-control" id="nama_kordinat" value="<?= set_value('nama_kordinat') ? set_value('nama_kordinat') : (isset($kordinat['nama_kordinat']) ? $kordinat['nama_kordinat'] : null); ?>" placeholder="Kordinat">
                        <?= form_error('latitude', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Kordinat</label>
                        <div class="input-group">
                            <input id="latitude" name="latitude" type="text" class="col-6 form-control" value="<?= set_value('latitude') ? set_value('latitude') : (isset($kordinat['latitude']) ? $kordinat['latitude'] : null); ?>" placeholder="Latitude">
                            <div class="input-group-append">
                                <span class="input-group-text">,</span>
                            </div>
                            <input id="longitude" name="longitude" type="text" class="col-6 form-control" value="<?= set_value('longitude') ? set_value('longitude') : (isset($kordinat['longitude']) ? $kordinat['longitude'] : null); ?>" placeholder="Longitude">
                        </div>
                        <?= form_error('latitude', '<small class="text-danger pl-2">', '</small>'); ?>
                        <?= form_error('longitude', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="radius">Radius</label>
                        <div class="input-group">
                            <input id="radius" class="form-control" type="number" name="radius" value="<?= set_value('radius') ? set_value('radius') : (isset($kordinat['radius']) ? $kordinat['radius'] : null); ?>" placeholder="Radius">
                            <div class="input-group-append">
                                <span class="input-group-text">Meter</span>
                            </div>
                        </div>
                        <?= form_error('radius', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pengaturan/kordinattambahan?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>
