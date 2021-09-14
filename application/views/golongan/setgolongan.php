<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group">
                        <label for="nama_golongan">Nama Golongan</label>
                        <input type="text" name="nama_golongan" class="form-control" id="nama_golongan" value="<?= set_value('nama_golongan') ? set_value('nama_golongan') : (isset($golongan['nama_golongan']) ? $golongan['nama_golongan'] : null); ?>" placeholder="Nama Golongan">
                        <?= form_error('nama_golongan', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="pph">PPH %</label>
                        <input type="number" name="pph" class="form-control" id="pph" value="<?= set_value('pph') ? set_value('pph') : (isset($golongan['pph']) ? $golongan['pph'] : null); ?>" placeholder="PPH %">
                        <?= form_error('pph', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>


                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('golongan?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>
