<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Tanggal</div>
                    <?php
                    $tgl_skrg = date("d-m-Y");
                    $tgl_awal = date('01-m-Y', strtotime($tgl_skrg));
                    $tgl_akhir = date('Y-m-t', strtotime($tgl_skrg));
    
                    ?>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="tgl_awal" name="tgl_awal" type="text" class="form-control from" autocomplete="off" value="<?= $tgl_awal ?>" />
                            <div class="input-group-append">
                                <span class="input-group-text">s/d</span>
                            </div>
                            <input id="tgl_akhir" name="tgl_akhir" type="text" class="form-control to" autocomplete="off" value="<?= $tgl_skrg ?>" />
                        </div>
                    </div>
                </div>
                <?php
                $akses = [1,2,3];
                if(in_array($this->session->userdata('role_id'), $akses)){
                ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">Pilih Unit Kerja</option>
                                <?php foreach ($skpd as $s) { ?>
                                    <option value="<?= $s['id_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Jenis Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="jenis_pegawai" name="jenis_pegawai" class="form-control select2">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="pegawai">Pegawai</option>
                                <option value="tks">TKS</option>
                            </select>
                        </div>
                    </div>
                </div>
    
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Nama Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="pegawai_id" name="pegawai_id" class="form-control select2">
                                <option value="0">Pilih Pegawai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm" <?=in_array($this->session->userdata('role_id'), $akses) ? 'disabled' :null;?>>Filter</button></div>
                </div>

            </li>
            <li class="list-group-item table-responsive">
                <table class="table table-hover table-striped" id="tableLogAbsen" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pegawai</th>
                            <th>Hari / Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                        </tr>
                    </thead>
                </table>
            </li>
        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tgl_awal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('#tgl_akhir').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        var startDate = new Date();
        var fechaFin = new Date();
        var FromEndDate = new Date();
        var ToEndDate = new Date();

        $('.from').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
        }).on('changeDate', function(selected) {
            startDate = new Date(selected.date.valueOf());
            startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
            $('.to').datepicker('setStartDate', startDate);
        });

        $('.to').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(selected) {
            FromEndDate = new Date(selected.date.valueOf());
            FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
            $('.from').datepicker('setEndDate', FromEndDate);
        });

        $('#tgl_akhir').datepicker('setEndDate', '+0d');
    });
</script>
<script>
    $(document).ready(function() {
        $("#jenis_pegawai").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                alert('Pastikan field SKPD dan Jenis Pegawai sudah terpilih!');
                return;
            }
            validForm();
            getPegawai();
        });
        $("#skpd_id").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                return;
            }
            validForm();
            getPegawai();
        });
        
        $('#pegawai_id').change(function(){
            validForm();
        });
        
        function getPegawai(){

            $('#pegawai_id').find('option').remove().end();
            var skpd_id          = $("#skpd_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();
            $.ajax({
                type: "post",
                url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'json/selectOptionPegawaiBySkpd?token=' . $_GET['token']; ?>" : "<?= base_url() . 'json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
                data: {
                    "skpd_id"       : skpd_id,
                },
                    
                success: function(data) {
                    $("#pegawai_id").html(data).change();
                }
            });

        }

        function validForm(){
            var skpd_id          = $("#skpd_id").val();
            var pegawai_id       = $("#pegawai_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();

            if(skpd_id=="" || pegawai_id=="" || jenis_pegawai==""){
                $('#btnFilter').prop("disabled", true);
            }else{
                $('#btnFilter').removeAttr("disabled");
            }
            
        }

        $("#btnFilter").click(function() {
            filter();
        });

        function filter() {
            var tgl_awal = $("#tgl_awal").val();
            var tgl_akhir = $("#tgl_akhir").val();

            var skpd_id = $("#skpd_id").val();
            var pegawai_id = $("#pegawai_id").val();

            var jenis_pegawai    = $("#jenis_pegawai").val();

            $('#tableLogAbsen').DataTable().destroy();
            $('#tableLogAbsen').DataTable({
                "processing": true,
                "pageLength": 100,
                "ajax": {
                    "url": "<?php echo base_url('logabsen/getLogAbsen?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "tanggalAwal": tgl_awal,
                        "tanggalAkhir": tgl_akhir,
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id,
                        "jenis_pegawai": jenis_pegawai
                    }

                },
                "createdRow": function(row, data, dataIndex) {
                    if (data[5] == "6" || data[5] == "0") {
                        $(row).find('td:eq(2)').addClass('bg-tr-danger');
                    }
                    if (data[6] >= 1) {
                        if (data[8] == 'yes') {
                            $(row).find('td:eq(2)').addClass('bg-tr-info');
                        } else if (data[8] == 'no') {
                            $(row).find('td:eq(2)').addClass('bg-tr-success');
                        } else {
                            $(row).find('td:eq(2)').addClass('bg-tr-warning');
                            $(row).find('td:eq(3)').html('<center>Libur</center>');
                            $(row).find('td:eq(4)').html('<center>Libur</center>');
                        }
                        var data2 = $(row).find('td:eq(2)').html();
                        $(row).find('td:eq(2)').html(data2 + "<div class='text-success'>" + data[7] + "</div>");
                    }
                }
            });
        }

    });
</script>