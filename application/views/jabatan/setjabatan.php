<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group">
                        <label for="nama_jabatan">Nama Jabatan</label>
                        <input type="text" name="nama_jabatan" class="form-control" id="nama_jabatan" value="<?= set_value('nama_jabatan') ? set_value('nama_jabatan') : (isset($jabatan['nama_jabatan']) ? $jabatan['nama_jabatan'] : null); ?>" placeholder="Nama Jabatan">
                        <?= form_error('nama_jabatan', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="penghasilan">Penghasilan</label>
                        <input type="number" name="penghasilan" class="form-control" id="penghasilan" value="<?= set_value('penghasilan') ? set_value('penghasilan') : (isset($jabatan['total']) ? $jabatan['total'] : null); ?>" placeholder="Penghasilan">
                        <?= form_error('penghasilan', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="skp">SKP</label>
                        <input id="skp" class="form-control autonumeric" value="0" type="text" name="skp" data-a-sign="Rp. " data-a-dec="," data-a-sep="." readonly>

                    </div>
                    <div class="form-group">
                        <label for="pkp">PKP</label>
                        <input id="pkp" class="form-control autonumeric" value="0" type="text" name="pkp" data-a-sign="Rp. " data-a-dec="," data-a-sep="." readonly>

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
<script>
    $(document).ready(function() {
        $('#penghasilan').on("change keyup paste", function() {
            generatepkpskp();
        });
        generatepkpskp();

        function generatepkpskp() {
            var skp = $('#penghasilan').val() * (60 / 100);
            var pkp = $('#penghasilan').val() * (40 / 100);
            $('#skp').val(skp);
            $('#pkp').val(pkp);
        }

    });
</script>