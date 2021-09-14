<div class="content-wrapper">
    <div class="card">
          
        <div class="card-header">
              <h3><?= $title ?></h3>
        </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="<?=base_url('admin2/dataizin?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            
                <li class="list-group-item">
                
                <?= $this->session->flashdata('pesan'); ?>
                <div class="alert alert-danger alert-dismissible fade show" id="dataIzin" role="alert">
                    Data Izin <strong>Sudah ada</strong>, Silahkan Pilih Tanggal Lain.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="opd_id">Nama Opd</label>
                        <select id="opd_id" name="opd_id" class="form-control select2">
                            <option value="">Pilih Satu</option>
                            <?php foreach ($opd as $o) { ?>
                                <option value="<?= $o['id']; ?>"><?= $o['nama_opd']; ?></option>
                            <?php } ?>
                        </select>
                        <?= form_error('opd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="pegawai_id">Nama Pegawai</label>
                        <select id="pegawai_id" name="pegawai_id[]" class="select2 form-control" multiple="multiple">
                            <option value="">-- Tidak ada data --</option>
                        </select>
                        <?= form_error('pegawai_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="jenis_izin">Jenis Izin</label>
                        <select id="jenis_izin" name="jenis_izin" class="form-control">
                            <option value="">Pilih Satu</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Dinas Luar">Dinas Luar</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <?= form_error('jenis_izin', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                   

                    <div class="form-group" id="tanggalAwal-body">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <div class="input-group">
                            <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control from" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="addTanggal" href="javascript:;" class="input-group-text btn btn-outline-primary ">+</a>
                            </div>
                            <?= form_error('tanggal_awal', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                   


                    <div class="form-group" id="tanggalAkhir-body">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <div class="input-group">
                            <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control to" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="removeTanggal" type="button" href="javascript:;" class="input-group-text btn btn-outline-danger ">x</a>
                            </div>
                            <!-- <?= form_error('tanggal_akhir', '<small class="text-danger pl-2">', '</small>'); ?> -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="file_izin">Berkas <small>(Pdf/Jpg)</small></label><br>
                        <small> Max File 500 KB</small>
                        <div class="custom-file">
                            <input type="file" class=" custom-file-input" id="file_izin" name="file_izin" required>
                            <label for="file_izin" class="custom-file-label">
                                Pilih File
                            </label>
                        </div>
                        <?= form_error('file_izin', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                 
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                     </div>
                  </form>
                  
                </li>
              </ul>
            
    </div>
</div>

<?php $this->view('template/javascript') ?>

<script type="text/javascript">
    $(document).ready(function() {
        
        var startDate = new Date();
        var fechaFin = new Date();
        var FromEndDate = new Date();
        var ToEndDate = new Date();

        $('.from').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayHighlight: true,
        }).on('changeDate', function(selected) {
            startDate = new Date(selected.date.valueOf());
            startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
            $('.to').datepicker('setStartDate', startDate);
        });
        // $('#tanggal_awal').datepicker('setStartDate', '-7d');
       
        $('.to').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
            
        }).on('changeDate', function(selected) {
            FromEndDate = new Date(selected.date.valueOf());
            FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
            $('.from').datepicker('setEndDate', FromEndDate);
        });
        // $('#tanggal_akhir').datepicker('todayHighlight', true);
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
        // $('#pegawai_id').multipleSelect()
        $("#pegawai_id").select2({

            placeholder: 'Pilih Pegawai',
            allowClear: true
        });
        $("#pegawai_id").change(function() {
            var tanggal_awal = $('#tanggal_awal').val();
            var tanggal_akhir = $('#tanggal_akhir').val() == '' ? tanggal_awal : $('#tanggal_akhir').val();
            tanggal_akhir = $('#tanggal_akhir').is(':visible') ? $('#tanggal_akhir').val() : tanggal_awal;
            $("#btnSubmit").attr('disabled', true);

            if (tanggal_awal != '' && tanggal_akhir != '') {
                checkDataIzin();
            }
        });

        // $("#btnSubmit").attr('disabled', true);
        $("#tanggalAkhir-body").hide();
        $("#addTanggal").show();
        $("#addTanggal").click(function() {
            $("#tanggalAkhir-body").show();
            $("#tanggal_akhir").attr('required', true);
            $(this).hide();
            return;
        });
        $("#removeTanggal").click(function() {
            $("#tanggalAkhir-body").hide();
            $("#tanggal_akhir").attr('required', false);
            $("#addTanggal").show();
        });

        $('#file_izin').on("change keyup paste", function() {
            checkImage();
        });

        $('#tanggal_awal').on("change keyup paste", function() {
            checkDataIzin();
        });
        $('#tanggal_akhir').on("change keyup paste", function() {
            checkDataIzin();
        });

        $("#dataIzin").hide();



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

        function checkImage() {
            // alert($('#pegawai_id').val());

            var ext = $('#file_izin').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['jpg', 'pdf', 'png', 'jpeg']) == -1) {
                alert('Terjadi kesalahan, File tidak jelas atau Format kurang sesuai.');
                $("#btnSubmit").attr('disabled', true);
            } else {
                $("#btnSubmit").attr('disabled', false);
            }
        }

        function checkDataIzin() {
            var tanggal_awal = $('#tanggal_awal').val();
            var tanggal_akhir = $('#tanggal_akhir').val() == '' ? tanggal_awal : $('#tanggal_akhir').val();
            tanggal_akhir = $('#tanggal_akhir').is(':visible') ? $('#tanggal_akhir').val() : tanggal_awal;
            var pegawai_id = $('#pegawai_id').val();
            var opd_id = $('#opd_id').val();

            $.ajax({
                type: "POST",
                url: "<?= base_url() . '/pegawai/cekIzin?token=' . $_GET['token']; ?>",
                data: {
                    "tanggal_awal": tanggal_awal,
                    "tanggal_akhir": tanggal_akhir,
                    "pegawai_id": pegawai_id,
                    "opd_id": opd_id,
                },

                success: function(data) {
                    console.log(data);

                    if (data == "true") {
                        $("#dataIzin").fadeTo(10000, 500).slideUp(300);
                        $("#btnSubmit").attr('disabled', true);
                    } else {
                        $("#btnSubmit").removeAttr('disabled');
                    }
                },
            });
        }

    });
</script>