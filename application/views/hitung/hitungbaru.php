<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <!-- Hitung Progress -->
            <li class="list-group-item bodyHitung" style="display: none">
                <h4 class="mb-3">(<span id="textJumlahHitungPegawai">0</span>) Terhitung - Dinas Komunikasi dan Informatika</h4>
                
                <div class="row pt-3 pb-3" id="bodyEndHitung" style="display: none">
                    <div class="col-md-6">
                        <span class="text-primary" id="textStatusEndHitung"></span>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-sm btn-danger" id="btnCloseHitung"><em class="ti ti-close"></em> Tutup</button>
                    </div>
                </div>
                <div class="row pt-3 pb-3" id="bodyLoadingHitung">
                    <div class="col-md-6">
                        <img src="<?=base_url('assets/img/icon/loading.gif');?>" width="40"> Sedang menghitung . . .
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-sm btn-danger" id="btnBatalkan"><em class="ti ti-close"></em> Batalkan</button>
                    </div>
                </div>
                <div class="card">
                    <ul class="list-group list-group-hitung" style="overflow:auto; max-height: 300px" id="bodyListResultHitungPegawai">
                    </ul>
                </div>
            </li>
            <!-- End Hitung Progress -->

            <!-- Prepare Hitung -->
            <li class="list-group-item bodyPrepareHitung">
                <?= $this->session->flashdata('pesan'); ?>
    
                <div class="alert alert-success" id="finish-alert">
                    <button type="button" class="close" data-dismiss="alert">x</button>
                    <strong id="alert_judul"></strong> <span id="alert_deskripsi"></span>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Bulan</div>
                    <?php
                        $bulan   = date("m-Y");
                    ?>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= $bulan ?>" />
                        </div>
                    </div>
                </div>

                <a href="<?= base_url('hitung/?token=' . $_GET['token']); ?>" class="btn btn-danger btn-sm"><em class="ti-arrow-left"></em> Kembali</a>
                <button id="btnGet" class="btn btn-info btn-sm"><em class="ti-reload"></em> Refresh</button>
                <img id="loading-animation" src="<?= base_url('assets/img/icon/loading.gif') ?>" width="20" />
            </li>
            <!-- End Hitung Prepare -->

            <!-- Datatable OPD -->
            <li class="list-group-item">
                <table class="table table-striped table-hover" id="tableOpd" width="100%">
                    <thead class="text-center">
                        <th>#</th>
                        <th>Nama OPD</th>
                        <th>Jumlah PNS</th>
                        <th>Status</th>
                        <th>Opsi</th>
                    </thead>
                </table>
            </li>
            <!-- End Datatable OPD -->

        </ul>
    </div>
</div>
<div id="pegawaiModal" class="labura-modal">
    <div class="labura-modal-content">
        <p id="pegawaiList"></p>
    </div>
</div>

<?php $this->view('template/javascript'); ?>
<script>
    var is_stop = false;
        
    $("#loading-animation").hide();
    $("#finish-alert").hide();

    $('#bulan').datepicker({
        format: "mm-yyyy",
        viewMode: "months", 
        minViewMode: "months",
        autoclose: true
    }).on('changeDate', function(selected) {
        getOpd();
    });


    $("#btnGet").click(function() {
        getOpd();
    });


    function getOpd() {
        var bulan = $("#bulan").val();

        $("#loading-animation").show();

        $('#btnGet').html('Tunggu sebentar . . .');
        $("#btnGet").prop("disabled", true);


        $('#tableOpd').DataTable().destroy();
        $('#tableOpd').DataTable({
            "autoWidth": false,
            "pageLength": 50,
            "ajax": {
                "type": "POST",
                "url": "<?= base_url() . 'hitung/getOpd?token=' . $_GET['token']; ?>",
                "data": {
                    "bulan": bulan,
                },
                error: function (xhr, error, thrown) {
                    $("#loading-animation").fadeOut(1000);
                    $("#btnGet").html("Refresh");
                    $("#btnGet").removeAttr("disabled");

                    $("#finish-alert").fadeTo(5000, 500).slideUp(500, function() {
                        $("#finish-alert").slideUp(500);
                    });


                    $("#alert_judul").html("GAGAL!");
                    $("#alert_deskripsi").html("Silahkan coba lagi !");
                    if ($("#finish-alert").removeClass("alert-success")) {
                        $("#finish-alert").addClass("alert-danger");
                    }

                },
            },
            "initComplete": function(setting, data) {
                $("#loading-animation").fadeOut(1000);
                $("#btnGet").html("Refresh");
                $("#btnGet").removeAttr("disabled");


                if (data == 'false') {
                    $("#finish-alert").fadeTo(5000, 500).slideUp(500, function() {
                        $("#finish-alert").slideUp(500);
                    });


                    $("#alert_judul").html("GAGAL!");
                    $("#alert_deskripsi").html("Silahkan coba lagi !");
                    if ($("#finish-alert").removeClass("alert-success")) {
                        $("#finish-alert").addClass("alert-danger");
                    }
                }

            },
            'columnDefs': [{
                    "targets": [0, 2, 3], // your case first column
                    "className": "text-center",
                },
                {
                    "targets": 4,
                    "className": "text-right",
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                var disabled = data[5] > 0 ? 'disabled' : null;
                var btn = data[5] > 0 ? 'btn-success' : 'btn-outline-primary';
                var text = data[5] > 0 ? 'Sudah Disetujui' : 'Pilih Pegawai';
                $(row).find('td:eq(4)').html("<button onclick='getPegawai(" + data[4] + ", "+data[4]+")' class='btnPilihPegawai btn btn-sm " + btn + "' " + disabled + ">" + text + "</button>");
            }



        });
    }

    function getPegawai(opd_id, nama_opd) {

        var loading = '<center style="margin-top: 150px;"><img style="top:100px; margin: 20px" id="loading-animation" src="<?= base_url(); ?>assets/img/icon/loading.gif" width="80" /> <h2 style="color:white">Sedang Memuat</h2><center>';
        $("#pegawaiModal").fadeIn(200);
        $('#pegawaiList').html(loading);

        $.ajax({
            type: "POST",
            url: "<?= base_url() . 'hitung/getPegawai?token=' . $_GET['token']; ?>",
            data: {
                'opd_id': opd_id,
                'bulan': $("#bulan").val(),
            },
            success: function(response) {
                
                $('#pegawaiList').html(response);
                var nama_opd = $('#view_nama_opd').val();
                $('#tablelistpegawai').DataTable({
                    paging: false
                });
                $("#pegawaiModal").fadeIn(200);
                $('#btnSelesaiPegawai').removeAttr('disabled');
                $('#rekap_semua').click(function() {
                    $(':checkbox.rekap_pegawai').prop('checked', this.checked);
                    $(':checkbox.rekap_tks').prop('checked', this.checked);
                });
                $('#btnSelesaiPegawai').click(function() {
                    $("#bodyListResultHitungPegawai").html(null);
                    // $("#pegawaiModal").slideUp(200);
                    var data = [];
                    $(":checkbox.rekap_pegawai").each(function() {
                        if ($(this).prop('checked') == true) {
                            data.push($(this).val());
                        }
                    });
                    $(":checkbox.rekap_tks").each(function() {
                        if ($(this).prop('checked') == true) {
                            data.push($(this).val());
                        }
                    });
                    

                    $("#btnSelesaiPegawai").attr('disabled', true);
                    $('.bodyHitung').show();
                    $('.bodyPrepareHitung').hide();
                    $("#pegawaiModal").fadeOut(500);
                    finishAlert("alert-success", "Berhasil !", "Hitung selesai !");
                    $("html, body").animate().scrollTop(0);
                    
                    is_stop         = false; 
                    var start       = 0;
                    hitung(opd_id,data[0], start, data.length, data);
                    $('#btnBatalkan').click(function(){
                        is_stop         = true;
                    });
                    return;

                    // var loading = '<center style="margin-top: 80px;"><img style="margin-top:-15px;" id="loading-animation" src="<?= base_url(); ?>assets/img/icon/loading.gif" width="80" /> <h1>Sedang Memproses</h1><center>';
                    // $('#pegawaiList').html(loading);
                });

                $("#closeLaburaModal").click(function() {
                    $("#pegawaiModal").fadeOut(500);
                    $('#pegawaiList').html(null);

                });

                $(document).on('keydown', function(event) {
                    if (event.key == "Escape") {
                        $("#pegawaiModal").fadeOut(500);
                        $('#pegawaiList').html(null);
                    }
                });

            }
        });

    }
    
    function elementResultHitungPegawai(no, nama, status, unit_kerja, rekap, tpp){
        return '<li class="list-group-item">'+
                '<div class="row" style="width: 100%">'+
                '<div class="col-md-5">'+
                        '<strong>'+(Number(no+1))+'. '+nama+'</strong>'+
                        // '<br>&nbsp;&nbsp;&nbsp;'+
                        // (status ? '<small class="text-primary">'+status+'</small>' : null)+
                    '</div>'+
                    '<div class="col-md-3">'+
                        '<small>'+unit_kerja+'</small>'+
                    '</div>'+
                    '<div class="col-md-2 text-right text-primary"><small>'+rekap+'</small></div>'+
                    '<div class="col-md-2 text-right text-danger"><small>'+tpp+'</small></div>'+
                '</div>'+
            '</li>';
    }
    
    function elementResultHitungPegawaiError(xhr, error){
        return '<li class="list-group-item">'+
                '<div class="row" style="width: 100%">'+
                '<div class="col-md-12 text-danger">'+error+'</div>'+
                '</div>'+
            '</li>';
    }

    function hitung(opd_id, pegawai, no, end, data) {
        if(is_stop){
            $('#textStatusEndHitung').html('Dibatalkan');
            $('#bodyEndHitung').show();
            $('#bodyLoadingHitung').hide();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "<?= base_url() . 'hitung/hitungPerpegawai?token=' . $_GET['token']; ?>",
            data: {
                'bulan'     : $("#bulan").val(),
                'opd_id'    : opd_id,
                'pegawai'   : pegawai,
            },
            error: function(xhr, error){
                console.log(error);
                elementResultHitungPegawaiError(xhr, error);
                var index   = Number(no+1);
                $('#textJumlahHitungPegawai').html(index);
                $("#bodyListResultHitungPegawai").animate().scrollTop(300);
                
                if(index < data.length){
                    hitung(opd_id, data[index], index, data.length, data);
                }else{
                    $('.bodyPrepareHitung').show();
                    $('#bodyLoadingHitung').hide();
                    $('#textStatusEndHitung').html('Selesai');
                    $('#bodyEndHitung').show();
                }
            },
            success: function(response) {
                var res     = $.parseJSON(response);
                var hit     = $("#bodyListResultHitungPegawai");
                var ele     = elementResultHitungPegawai(no, res.nama_pegawai, status, res.nama_skpd, res.rekap, res.tpp);
                var index   = Number(no+1);
                hit.html(hit.html()+ele);
                $('#textJumlahHitungPegawai').html(index);
                $("#bodyListResultHitungPegawai").animate().scrollTop(300);
                
                if(index < data.length){
                    hitung(opd_id, data[index], index, data.length, data);
                }else{
                    $('.bodyPrepareHitung').show();
                    $('#bodyLoadingHitung').hide();
                    $('#textStatusEndHitung').html('Selesai');
                    $('#bodyEndHitung').show();

                }

            }

        });
    }
    
    $('#btnCloseHitung').click(function(){
        $('.bodyHitung').hide();
        $('.bodyPrepareHitung').show();
    })

    function finishAlert(bg, title, desc) {
        $("#finish-alert").fadeTo(5000, 500).slideUp(500, function() {
            $("#finish-alert").slideUp(500);
        });
        $("#alert_judul").html(title);
        $("#alert_deskripsi").html(desc);
        $("#finish-alert").removeClass("alert-danger");
        $("#finish-alert").removeClass("alert-success");
        $("#finish-alert").addClass(bg);
    }

    
</script>