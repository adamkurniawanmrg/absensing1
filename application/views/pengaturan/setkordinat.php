<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group">
                        <label for="latitude">Kordinat</label>
                        <div class="input-group">
                            <input id="latitude" name="latitude" type="text" class="col-6 form-control" value="<?= isset($kordinat['latitude']) ? $kordinat['latitude'] : null; ?>" placeholder="Latitude">
                            <div class="input-group-append">
                                <span class="input-group-text">,</span>
                            </div>
                            <input id="longitude" name="longitude" type="text" class="col-6 form-control" value="<?= isset($kordinat['longitude']) ? $kordinat['longitude'] : null; ?>" placeholder="Longitude">
                        </div>
                        <?= form_error('latitude', '<small class="text-danger pl-2">', '</small>'); ?>
                        <?= form_error('longitude', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="radius">Radius</label>
                        <div class="input-group">
                            <input id="radius" class="form-control" type="number" name="radius" value="<?= isset($kordinat['radius']) ? $kordinat['radius'] : null; ?>" placeholder="Radius">
                            <div class="input-group-append">
                                <span class="input-group-text">Meter</span>
                            </div>
                        </div>
                        <?= form_error('radius', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pengaturan/kordinat?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>
