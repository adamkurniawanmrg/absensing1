<!-- Begin Page Content -->
<div class="content-wrapper">

        <!-- Page Heading -->
        <div class="card">
             
        <div class="card-header">
              <h3><?= $title ?></h3>
        </div>
            <ul class="list-group list-group-flush">

                <form action="" method="post" enctype="multipart/form-data">
                    <li class="list-group-item">
                        <?= $this->session->flashdata('pesan'); ?>
                        <div class="alert alert-danger alert-dismissible" id="alertabsenmanual" role="alert">
                            Absen Manual <strong>Sudah ada</strong>, Silahkan Pilih Tanggal Lain.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <?php $akses = [1,2,3]; if(in_array($this->session->userdata('role_id'), $akses)): ?>
                        <div class="form-group mb-3">
                            <label for="skpd_id">Unit Kerja</label>
                            <select id="skpd_id" name="skpd_id" class="form-control select2" style="width: 100%;" required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                <?php foreach ($skpd as $s) { ?>
                                    <option value="<?= $s['id_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                            <?= form_error('skpd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="jenis_pegawai">Jenis Pegawai</label>
                            <select id="jenis_pegawai" name="jenis_pegawai" class="form-control select2">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="pegawai">Pegawai</option>
                                <option value="tks">TKS</option>
                            </select>
                            <?= form_error('jenis_pegawai', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>


                        <div class="form-group mb-3">
                            <label for="pegawai_id">Nama Pegawai</label>
                            <select id="pegawai_id" name="pegawai_id" class="form-control select2" style="width: 100%;" required>
                                <option value="">-- Tidak Ada Pegawai --</option>
                            </select>
                            <?= form_error('pegawai_id', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <?php endif;?>

                        <div class="form-group mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input id="tanggal" name="tanggal" type="text" class="form-control" autocomplete="OFF" placeholder="Tanggal" required />
                            <?= form_error('tanggal', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group mb-3" style="padding:0">
                            <label for="jenis_absen">Jenis Absen</label>
                            <select name="jenis_absen" id="jenis_absen" class="form-control select2" required>
                                <option value="">--Pilih Jenis Absen--</option>
                                <option value="AMP">AMP</option>
                                <option value="AMS">AMS</option>
                                <option value="AMP dan AMS">AMP dan AMS</option>
                            </select>

                            <?= form_error('jenis_absen', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>


                        <div class="form-group mb-3" id="amp-body">
                            <label for="lampiran_amp">Lampiran AMP <small>Maks 500 KB</small> </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="lampiran_amp" name="lampiran_amp">
                                <label for="lampiran_amp" class="custom-file-label">
                                    Pilih File
                                </label>
                            </div>
                            <?= form_error('lampiran_amp', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <div class="form-group mb-3" id="ams-body">
                            <label for="lampiran_ams">Lampiran AMS <small>Maks 500 KB</small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="lampiran_ams" name="lampiran_ams">
                                <label for="lampiran_ams" class="custom-file-label">
                                    Pilih File
                                </label>
                            </div>
                            <?= form_error('lampiran_ams', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <button type="submit" id="btnSubmit" class="btn btn-sm btn-primary" disabled><em class="ti-save"></em> Simpan</button>
                        <a href="<?= base_url('absenmanual?token=' . $_GET['token']) ?>" class="btn btn-danger btn-sm"><em class="ti-arrow-left"></em> Kembali</a>
                    </li>
                </form>
            </ul>
        </div>


</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>

<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>

<?php 
    $akses = [1,2,3]; 
    $akses2 = [1,2]; 
    $hak_akses = in_array($this->session->userdata('role_id'), $akses);
    $hak_akses2= in_array($this->session->userdata('role_id'), $akses2);
?>

<script type="text/javascript">
    $(document).ready(function() {
        var startDate = new Date();

        var startDate = new Date();
        var fechaFin = new Date();
        var FromEndDate = new Date();
        var ToEndDate = new Date();

        $('#tanggal').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayHighlight: true,
        <?php if(!$hak_akses2):?>
            startDate: '-7d',
            endDate: 'today'
        <?php endif;?>
        }).on('changeDate', function(selected) {
            checkAbsenManual();
            return;
        });



        $('#alertabsenmanual').hide();

        function checkAbsenManual() {
            var tanggal      = $('#tanggal').val();
            var jenis_absen  = $('#jenis_absen').val();
            
            <?php if($hak_akses):?>
            var pegawai_id   = $('#pegawai_id').val();
            var skpd_id      = $('#skpd_id').val();
            var jenis_pegawai= $('#jenis_pegawai').val();
            <?php endif;?> 
            
            if(jenis_absen==""){
                $("#btnSubmit").prop('disabled', true);
                return;
            }
            
            $.ajax({
                type: "POST",
                url: "<?= base_url() . 'absenmanual/cekAbsenManual?token=' . $_GET['token']; ?>",
                data: {
                    "tanggal"       : tanggal,
                    "jenis_absen"   : jenis_absen,
                    <?php if($hak_akses):?>
                    "pegawai_id"    : pegawai_id,
                    "skpd_id"       : skpd_id,
                    "jenis_pegawai" : jenis_pegawai
                    <?php endif;?>
                },

                success: function(data) {
                    data = $.parseJSON(data);
                    if (data) {
                        $("#btnSubmit").prop('disabled', true);
                        $('#alertabsenmanual').show();
                        return;
                    }
                    $('#alertabsenmanual').hide();
                    $("#btnSubmit").removeAttr('disabled');
                    return;
                }
            });
        }

        changeJenisAbsen();
        $('#jenis_absen').change(function() {
            changeJenisAbsen();
            checkAbsenManual();
            return;
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
            if ($.inArray(ext, ['jpg', 'pdf', 'jpeg', 'png']) == -1) {
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

        
        <?php if($hak_akses):?>

        $("#jenis_pegawai").change(function() {
            getPegawai();
        });
        
        $("#skpd_id").change(function() {
            if($("#jenis_pegawai").val()==""){
                return;
            }
            getPegawai();
        });
        
        function getPegawai(){
            $('#pegawai_id').find('option').remove().end();
            var skpd_id = $("#skpd_id").val();
            var jenis_pegawai = $("#jenis_pegawai").val();
            $.ajax({
                type: "post",
                url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'json/selectOptionPegawaiBySkpd?token=' . $_GET['token']; ?>" : "<?= base_url() . 'json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
                data: {
                    'skpd_id': skpd_id
                },
                success: function(data) {
                    $("#pegawai_id").html(data);
                }
            });

        }
        <?php endif;?>

    });
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

</script>