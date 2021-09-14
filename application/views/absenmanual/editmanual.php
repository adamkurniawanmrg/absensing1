<!-- Begin Page Content -->
<div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
            </div>
            <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $manual['id']; ?>">
                        <div class="form-group">
                            <label for="opd_id">Nama OPD</label>
                            <select id="opd_id" name="opd_id" class="form-control select2" style="width: 100%;" required>
                                <option value="">-- Pilih OPD --</option>
                                <?php foreach ($opd as $o) : ?>
                                    <option value="<?= $o['id']; ?>" <?= $manual['opd_id'] == $o['id'] ? "selected" : null ?>><?= $o['nama_opd']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('opd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group">
                            <label for="pegawai_id">Nama Pegawai</label>
                            <select id="pegawai_id" name="pegawai_id" class="form-control select2" style="width: 100%;" required>
                                <option value="">-- Pilih Pegawai --</option>
                                <?php foreach ($pegawai as $p) { ?>
                                    <option value="<?= $p['id']; ?>" <?= $manual['pegawai_id'] == $p['id'] ? "selected" : null ?>><?= $p['nama']; ?></option>
                                <?php } ?>
                            </select>
                            <?= form_error('pegawai_id', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <?php if ($user['role_id'] == 1) : ?>
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input id="tanggal" name="tanggal" type="text" class="form-control from " autocomplete="OFF" value="<?= $manual['tanggal'] ?>" required />
                                <?= form_error('tanggal', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>

                        <?php else : ?>

                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input id="tanggal_opd" name="tanggal" type="text" class="form-control from" autocomplete="OFF" value="<?= $manual['tanggal'] ?>" required />
                                <?= form_error('tanggal', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                        <?php endif; ?>


                        <div class="form-group" style="padding:0">
                            <label for="jenis_absen">Jenis Absen</label>
                            <select name="jenis_absen" id="jenis_abesn" class="form-control" required>
                                <option value="">--Pilih Jenis Absen--</option>
                                <option value="AMP" <?= $manual['jenis_absen'] == "AMP" ? "selected" : null ?>>AMP</option>
                                <option value="AMS" <?= $manual['jenis_absen'] == "AMS" ? "selected" : null ?>>AMS</option>
                                <option value="AMP dan AMS" <?= $manual['jenis_absen'] == "AMP dan AMS" ? "selected" : null ?>>AMP dan AMS</option>
                            </select>

                            <?= form_error('jenis_absen', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group">
                            <label for="lampiran_amp">Lampiran AMP <small>Maks 500 KB</small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="lampiran_amp" name="lampiran_amp">
                                <label for="lampiran_amp" class="custom-file-label">
                                    Pilih File
                                </label>
                            </div>
                            <!-- Tombol Button -->
                            <?php if ($manual['jenis_absen'] == "AMP") : ?>
                                <a href=" <?= base_url('assets/img/berkas/absen_manual/') . $manual['lampiran_amp'] ?>" class="btn btn-secondary btn-sm mt-2 ml-2" target="_BLANK">Lihat Berkas</a>
                            <?php elseif ($manual['jenis_absen'] == "AMP dan AMS") : ?>
                                <a href="<?= base_url('assets/img/berkas/absen_manual/') . $manual['lampiran_amp'] ?>" class="btn btn-secondary btn-sm mt-2 ml-2" target="_BLANK">Lihat Berkas</a>
                            <?php else : ?>
                                Tidak ada Berkas
                            <?php endif; ?>

                            <?= form_error('lampiran_amp', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group">
                            <label for="lampiran_ams">Lampiran AMS <small>Maks 500 KB</small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="lampiran_ams" name="lampiran_ams">
                                <label for="lampiran_ams" class="custom-file-label">
                                    Pilih File
                                </label>
                            </div>

                            <!-- Tombol Button -->
                            <?php if ($manual['jenis_absen'] == "AMS") : ?>
                                <a href=" <?= base_url('assets/img/berkas/absen_manual/') . $manual['lampiran_amp'] ?>" class="btn btn-secondary btn-sm mt-2 ml-2" target="_BLANK">Lihat Berkas</a>
                            <?php elseif ($manual['jenis_absen'] == "AMP dan AMS") : ?>
                                <a href="<?= base_url('assets/img/berkas/absen_manual/') . $manual['lampiran_amp'] ?>" class="btn btn-secondary btn-sm mt-2 ml-2" target="_BLANK">Lihat Berkas</a>
                            <?php else : ?>
                                Tidak ada Berkas
                            <?php endif; ?>

                            <?= form_error('lampiran_ams', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary mb-3">Update Absen Manual</button>
                        <!-- <a href="<?= base_url('absensi/cetak') ?>" class="btn btn-info mb-3">Cetak</a> -->
                        <a href="<?= base_url('absensi/manual?token=' . $_GET['token']) ?>" class="btn btn-danger mb-3">Back</a>
                    </form>
            </div>
        </div>
</div>
<!-- /.container-fluid -->


<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        // var startDate = new Date();

        // var startDate = new Date();
        // var fechaFin = new Date();
        // var FromEndDate = new Date();
        // var ToEndDate = new Date();

        // $('.from').datepicker({
        //     autoclose: true,
        //     format: 'dd-mm-yyyy'
        // }).on('changeDate', function(selected) {
        //     startDate = new Date(selected.date.valueOf());
        //     startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
        //     $('.to').datepicker('setStartDate', startDate);
        //     $('#tanggal_opd').datepicker('setStartDate', '-7d');
        // });

        $('#tanggal_opd').datepicker({
            format: "yyyy-mm-dd",
            autoclose: false
        });
        $('#tanggal').datepicker({
            format: "yyyy-mm-dd",
            autoclose: false
        });

    });
</script>
<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
<script>
    $(document).ready(function() {
        changeJenisAbsen();
        $('#jenis_absen').change(function() {
            changeJenisAbsen();
        });


        $('#lampiran_amp').on("change keyup paste", function() {
            checkImage('amp');
        });

        $('#lampiran_ams').on("change keyup paste", function() {
            checkImage('ams');
        });

        function checkImage(click) {
            var jenis_absen = $('#jenis_absen').val();
            var amp = $('#lampiran_amp').val().split('.').pop().toLowerCase();
            var ams = $('#lampiran_ams').val().split('.').pop().toLowerCase();
            if (jenis_absen == "AMP dan AMS") {
                if ($('#lampiran_amp').val() == "" && $('#lampiran_ams').val() == "") {
                    $('#btnSubmit').attr('disabled', true);
                } else if ($('#lampiran_amp').val() != "" && $('#lampiran_ams').val() != "" && imageValidation(amp) == false && imageValidation(ams) == false) {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMP & AMS tidak valid, pastikan extensi file JPG, atau PDF");
                } else if ($('#lampiran_ams').val() == "" && imageValidation(amp) == false) {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMP tidak valid, pastikan extensi file JPG, atau PDF");
                } else if ($('#lampiran_amp').val() == "" && imageValidation(ams) == false) {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMS tidak valid, pastikan extensi file JPG, atau PDF");
                } else if ($('#lampiran_ams').val() != "" && imageValidation(amp) == false && click == "amp") {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMP tidak valid, pastikan extensi file JPG, atau PDF");
                } else if ($('#lampiran_amp').val() != "" && imageValidation(ams) == false && click == "ams") {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMS tidak valid, pastikan extensi file JPG, atau PDF");
                } else if (imageValidation(amp) == true && imageValidation(ams) == true) {
                    $('#btnSubmit').removeAttr('disabled');
                }
            } else if (jenis_absen == "AMP") {
                if ($('#lampiran_amp').val() == "") {
                    $('#btnSubmit').attr('disabled', true);
                } else if (imageValidation(amp) == false) {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMP tidak valid, pastikan extensi file JPG, atau PDF");
                } else {
                    $('#btnSubmit').removeAttr('disabled');
                }
            } else if (jenis_absen == "AMS") {
                if ($('#lampiran_ams').val() == "") {
                    $('#btnSubmit').attr('disabled', true);
                } else if (imageValidation(ams) == false) {
                    $('#btnSubmit').attr('disabled', true);
                    alert("Lampiran AMS tidak valid, pastikan extensi file JPG, atau PDF");
                } else {
                    $('#btnSubmit').removeAttr('disabled');
                }
            }

        }

        function imageValidation(ext) {
            if ($.inArray(ext, ['jpg', 'pdf']) == -1) {
                return false;
            } else {
                return true;
            }

        }

        function changeJenisAbsen() {
            var jenis_absen = $('#jenis_absen').val();
            var AMP = $('#amp-body');
            var AMS = $('#ams-body');
            if (jenis_absen == "AMP") {
                AMP.show();
                $('#lampiran_amp').prop('required', true);
                $('#lampiran_ams').prop('required', false);
                checkImage('amp');

                AMS.hide();
            } else if (jenis_absen == "AMS") {
                AMP.hide();
                $('#lampiran_amp').prop('required', false);
                $('#lampiran_ams').prop('required', true);
                checkImage('ams');

                AMS.show();
            } else if (jenis_absen == "AMP dan AMS") {
                AMP.show();
                AMS.show();
                checkImage('amp');
                checkImage('ams');

                $('#lampiran_amp').prop('required', true);
                $('#lampiran_ams').prop('required', true);
            } else {
                AMP.hide();
                AMS.hide();
            }
        }

        $("#opd_id").change(function() {
            $('#pegawai_id').find('option').remove().end();
            var opd_id = $("#opd_id").val();
            $.ajax({
                type: "post",
                url: "<?= base_url() . '/pegawai/selectpegawaibyopd?token=' . $_GET['token']; ?>",
                data: "opd_id=" + opd_id,
                success: function(data) {
                    $("#pegawai_id").html(data);
                }
            });
        });

    });
</script>
<!-- <script>
    $(document).ready(function() {
        $('#lampiran_amp').on("change keyup paste", function() {
            checkImage('amp');
        });

        $('#lampiran_ams').on("change keyup paste", function() {
            checkImage('ams');
        });

        function checkImage(mode) {
            if (mode == 'amp') {
                var ext = $('#lampiran_amp').val().split('.').pop().toLowerCase();
            } else {
                var ext = $('#lampiran_ams').val().split('.').pop().toLowerCase();
            }

            if ($.inArray(ext, ['jpg', 'pdf']) == -1) {
                alert('Format file harus JPG atau PDF !');
            }
        }

        $("#opd_id").change(function() {
            $('#pegawai_id').find('option').remove().end();
            var opd_id = $("#opd_id").val();
            $.ajax({
                type: "post",
                url: "<?= base_url() . '/pegawai/selectpegawaibyopd'; ?>",
                data: "opd_id=" + opd_id,
                success: function(data) {
                    $("#pegawai_id").html(data);
                }
            });
        });

    });
</script> -->