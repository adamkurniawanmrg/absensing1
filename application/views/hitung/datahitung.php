<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?= site_url('hitung/hitungbaru?token=' . $_GET['token']) ?>" class="btn btn-sm btn-primary">Hitung Baru</a>
            </li>
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="alert alert-success mb-3" id="finish-alert">
                    <button type="button" class="close" data-dismiss="alert">x</button>
                    <strong id="alert_judul"></strong> <span id="alert_deskripsi"></span>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">OPD</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="opd_id" name="opd_id" class="form-control select2">
                                <option value="">Pilih OPD</option>
                                <?php foreach ($skpds as $opd) { 
                                        $nama_skpd      = explode(" ", $opd['nama_skpd']);
                                        if(
                                            $opd['nama_skpd']=='Satuan Polisi Pamong Praja' ||
                                            $nama_skpd[0]=='Dinas' ||
                                            $nama_skpd[0]=='Badan' ||
                                            $nama_skpd[0]=='Sekretariat' ||
                                            $nama_skpd[0]=='Kecamatan' ||
                                            $nama_skpd[0]=='Inspektorat'
                                        ){}else{continue;}

                                ?>
                                    <option value="<?= $opd['id_skpd']; ?>"><?= $opd['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Jenis Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="kategori_pegawai" name="kategori_pegawai" class="form-control select2">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="pegawai">PNS</option>
                                <option value="tks">TKS</option>
    
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Rekapitulasi</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="rekapitulasi" name="rekapitulasi" class="form-control select2">
                                <option value="">Pilih Rekapitulasi</option>
                                <option value="rekap">Rekap Absensi</option>
                                <option value="tpp">TPP/Honor</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Bulan</div>
                    <?php
                        $bulan   = date("m-Y");
                    ?>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="bulan" name="bulan" id="bulan" type="text" class="form-control" autocomplete="off" value="<?= $bulan ?>" />
                        </div>
                    </div>
                </div>
                
                <button id="btnFilter" class="btn btn-primary btn-sm">Filter</button>
            </li>
            <li class="list-group-item">
                <button id="link-cetak" class="float-right mb-5 btn btn-sm btn-success"><em class="fa fa-print"></em> Cetak</button><br>
                <div class="col-md-12" id="stageTppRekap"></div>
            </li>
        </ul>
    </div>
</div>


<?php $this->view('template/javascript'); ?>
<script>
    $(document).ready(function() {
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        $('#link-cetak').attr("disabled", true);

        $("#finish-alert").hide();
        $("#stageTppRekap").hide();

        var link_cetak = "";
        $('#link-cetak').click(function() {
            window.open(link_cetak);
        });

        $('#btnFilter').click(function() {
            var opd_id = $("#opd_id").val();
            var kategori_pegawai = $("#kategori_pegawai").val();
            var mode = $("#rekapitulasi").val();

            if (opd_id != "" && kategori_pegawai != "" && mode != "") {
                if (mode == 'rekap') {
                    link_cetak = "<?= base_url('hitung/cetakRekap/');?>" + opd_id + "/" + $('#bulan').val() + "/" + $('#kategori_pegawai').val() + "?token=<?=$_GET['token'];?>";
                    getHitungRekap(opd_id, kategori_pegawai);
                    $('#link-cetak').removeAttr("disabled");
                } else if (mode == 'tpp') {
                    link_cetak = "<?= base_url('hitung/cetakTpp/');?>" + opd_id + "/" + $('#bulan').val() + "/" + $('#kategori_pegawai').val() + "?token=<?=$_GET['token'];?>";
                    getHitungTpp(opd_id, kategori_pegawai);
                    $('#link-cetak').removeAttr("disabled");
                }
            } else {
                $("#alert_judul").html("GAGAL !");
                $("#alert_deskripsi").html("Cek Kembali inputan filter !");
                $("#finish-alert").removeClass("alert-success");
                $("#finish-alert").removeClass("alert-danger");
                $("#finish-alert").addClass("alert-danger");

                $("#finish-alert").fadeTo(5000, 500).slideUp(500, function() {
                    $("#finish-alert").slideUp(500);
                });
            }
        });

        function getHitungRekap(opd_id, kategori_pegawai) {
            var bulan = $("#bulan").val();
            
            $("#loading-animation").show();

            $('#btnFilter').html('Tunggu sebentar . . .');
            $("#btnFilter").prop("disabled", true);

            $.ajax({
                type: "POST",
                url: "<?= base_url('hitung/getRekapAbsen?token=' . $_GET['token']); ?>",
                data: {
                    'kategori_pegawai': kategori_pegawai,
                    'opd_id': opd_id,
                    'bulan': bulan,
                },
                success: function(response) {
                    $('#stageTppRekap').html(response);
                    $("#stageTppRekap").fadeIn(200);

                    $('#stageDatatable').DataTable({
                        "pageLength": 50,
                        'columnDefs': [{
                            "width": "40%",
                            "targets": [1]
                        }],
                        "fnInitComplete": function(oSettings, json) {
                            $('#stageDatatable_wrapper .row .col-sm-12').addClass('table-responsive');
                        }

                    });

                    $('#btnFilter').html('Filter');
                    $("#btnFilter").removeAttr("disabled");

                },
            });
        }

        function getHitungTpp(opd_id, kategori_pegawai) {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();

            $("#loading-animation").show();

            $('#btnFilter').html('Tunggu sebentar . . .');
            $("#btnFilter").prop("disabled", true);

            $.ajax({
                type: "POST",
                url: "<?= base_url('hitung/getTpp?token=' . $_GET['token']); ?>",
                data: {
                    'opd_id': opd_id,
                    'kategori_pegawai': kategori_pegawai,
                    'bulan': bulan,
                    'tahun': tahun
                },
                success: function(response) {
                    $('#stageTppRekap').html(response);
                    $("#stageTppRekap").fadeIn(200);

                    $('#stageDatatable').DataTable({
                        "pageLength": 50,
                        'columnDefs': [{
                                "width": "350",
                                "targets": [1,2,3]
                            },
                        ],
                        "fnInitComplete": function(oSettings, json) {
                            $('#stageDatatable_wrapper .row .col-sm-12').addClass('table-responsive');
                        }


                    });

                    $('#btnFilter').html('Filter');
                    $("#btnFilter").removeAttr("disabled");

                }
            });
        }
    });
</script>