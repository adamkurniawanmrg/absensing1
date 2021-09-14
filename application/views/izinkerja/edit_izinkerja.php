<div class="content-wrapper">
    <div class="card">
          
        <div class="card-header">
              <h3><?= $title ?></h3>
        </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="<?=base_url('izinkerja/dataizinkerja?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
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
                    <input type="hidden" name="id" value="<?= $izin['id']; ?>">
                    <input type="hidden" id="label_pegawai_id" value="<?= $izin['pegawai_id']; ?>">
                    <div class="form-group">
                        <label for="opd_id">OPD</label>
                        <select id="opd_id" name="opd_id" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih OPD --</option>
                            <?php foreach ($opd as $o) : ?>
                                <option value="<?= $o['id']; ?>" <?= $izin['opd_id'] == $o['id'] ? "selected" : null ?>><?= $o['nama_opd']; ?></option>
                            <?php endforeach; ?>
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
                            <option value="Izin" <?= $izin['jenis_izin'] == "Izin" ? "selected" : null ?>>Izin</option>
                            <option value="Sakit" <?= $izin['jenis_izin'] == "Sakit" ? "selected" : null ?>>Sakit</option>
                            <option value="Dinas Luar" <?= $izin['jenis_izin'] == "Dinas Luar" ? "selected" : null ?>>Dinas Luar</option>
                            <option value="Lainnya" <?= $izin['jenis_izin'] == "Lainnya" ? "selected" : null ?>>Lainnya</option>
                        </select>
                        <?= form_error('jenis_izin', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    
                      <?php if ($this->session->role_id == 1) : ?>
                        <div class="form-group" id="tanggalAwal-body">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <div class="input-group">
                            <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control from" value="<?= date("d-m-Y", strtotime($izin['tanggal_awal'])) ?>" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="addTanggal" href="javascript:;" class="input-group-text btn btn-outline-primary ">+</a>
                            </div>
                            <?= form_error('tanggal_awal', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                    <?php elseif ($this->session->role_id == 2) : ?>
                           <div class="form-group" id="tanggalAwal-body">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <div class="input-group">
                            <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control from" value="<?= date("d-m-Y", strtotime($izin['tanggal_awal'])) ?>" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="addTanggal" href="javascript:;" class="input-group-text btn btn-outline-primary ">+</a>
                            </div>
                            <?= form_error('tanggal_awal', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>

                    <?php else : ?>
                       
                        
                    <div class="form-group" id="tanggalAwal-body">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <div class="input-group">
                            <input id="tanggal_awal_opd" name="tanggal_awal" type="text" class="form-control from" value="<?= date("d-m-Y", strtotime($izin['tanggal_awal'])) ?>" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="addTanggal" href="javascript:;" class="input-group-text btn btn-outline-primary ">+</a>
                            </div>
                            <?= form_error('tanggal_awal', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                    </div>
                    

                    <?php endif; ?>



                    <div class="form-group" id="tanggalAkhir-body">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <div class="input-group">
                            <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control to" value="<?= date("d-m-Y", strtotime($izin['tanggal_akhir'])) ?>" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="removeTanggal" href="javascript:;" class="input-group-text btn btn-outline-danger ">x</a>
                            </div>
                            <!-- <?= form_error('tanggal_akhir', '<small class="text-danger pl-2">', '</small>'); ?> -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="file_izin">Berkas <small>(Pdf/Jpg)</small></label><br>
                        <small> Max File 500 KB</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file_izin" name="file_izin">
                            <label for="file_izin" class="custom-file-label">
                                Pilih File
                            </label>
                        </div>


                        <a href=" <?= base_url('assets/img/berkas/izin_kerja/') . $izin['file_izin'] ?>" class="btn btn-secondary btn-sm mt-2 ml-2" target="_BLANK">Lihat Berkas</a>

                        <!-- <?= form_error('file_izin', '<small class="text-danger pl-2">', '</small>'); ?> -->
                    </div>

               
                    
                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-sm btn-primary mr-2">Simpan</button>
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
            format: 'dd-mm-yyyy'
            // todayHighlight: true,
        }).on('changeDate', function(selected) {
            startDate = new Date(selected.date.valueOf());
            startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
            $('.to').datepicker('setStartDate', startDate);
        });  
        $('#tanggal_awal_opd').datepicker('setStartDate', '-7d');
       
        $('.to').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(selected) {
            FromEndDate = new Date(selected.date.valueOf());
            FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
            $('.from').datepicker('setEndDate', FromEndDate);
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
        $("#pegawai_id").select2({
            maximumSelectionLength: 5,
            placeholder: 'Pilih Pegawai',
            allowClear: true
        });

        changing();
        $("#addTanggal").show();
        $("#addTanggal").click(function() {
            $("#tanggalAkhir-body").show();
            $("#tanggal_akhir").attr('required', true);
            $(this).hide();
        });
        if ($("#tanggal_awal").val() == $("#tanggal_akhir").val()) {
            removeTanggal();
        }
        $("#removeTanggal").click(function() {
            removeTanggal();
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

        $("#opd_id").change(function() {
            changing();
        });

        $("#dataIzin").hide();

        function removeTanggal() {
            $("#tanggalAkhir-body").hide();
            $("#tanggal_akhir").attr('required', false);
            $("#tanggal_akhir").val("");
            $("#addTanggal").show();
        }

        function checkImage() {
            var ext = $('#file_izin').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['jpg', 'pdf']) == -1) {
                alert('Terjadi kesalahan, File tidak jelas atau Format kurang sesuai. Format file harus JPG atau PDF !');
                $("#btnSubmit").attr('disabled', true);
            } else {
                $("#btnSubmit").removeAttr('disabled');
            }
        }



        function changing() {
            $('#pegawai_id').find('option').remove().end();
            var opd_id = $("#opd_id").val();
            $.ajax({
                type: "post",
                url: "<?= base_url() . '/izinkerja/selectpegawaibyopdeditizin?token=' . $_GET['token']; ?>",
                data: {
                    'opd_id': opd_id,
                    'last_pegawai': '<?= $last_pegawai; ?>'
                },
                success: function(data) {
                    $("#pegawai_id").html(data);
                }
            });
        }

        $("#pegawai_id").change(function() {
            var tanggal_awal = $('#tanggal_awal').val();
            var tanggal_akhir = $('#tanggal_akhir').val() == '' ? tanggal_awal : $('#tanggal_akhir').val();
            tanggal_akhir = $('#tanggal_akhir').is(':visible') ? $('#tanggal_akhir').val() : tanggal_awal;
            $("#btnSubmit").attr('disabled', true);

            if (tanggal_awal != '' && tanggal_akhir != '') {
                checkDataIzin();
            }
        });



        function checkDataIzin() {
            var tanggal_awal = $('#tanggal_awal').val();
            var tanggal_akhir = $('#tanggal_akhir').val() == '' ? tanggal_awal : $('#tanggal_akhir').val();
            tanggal_akhir = $('#tanggal_akhir').is(':visible') ? $('#tanggal_akhir').val() : tanggal_awal;
            var pegawai_id = $('#pegawai_id').val();
            var opd_id = $('#opd_id').val();

            $.ajax({
                type: "POST",
                url: "<?= base_url() . '/izinkerja/cekIzin?token=' . $_GET['token']; ?>",
                data: {
                    "tanggal_awal": tanggal_awal,
                    "tanggal_akhir": tanggal_akhir,
                    "pegawai_id": pegawai_id,
                    "opd_id": opd_id,
                },

                success: function(data) {
                    console.log(data);
                    if (data == "true") {
                        $("#dataIzin").fadeTo(3000, 500).slideUp(300);
                        $("#btnSubmit").attr('disabled', true);
                    } else {
                        $("#btnSubmit").removeAttr('disabled');
                    }
                },
            });
        }
    });
</script>