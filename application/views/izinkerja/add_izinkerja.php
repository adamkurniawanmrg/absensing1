<style>
video, canvas {
  height:100%;
  width:100%;
  object-fit:cover;
}
.mobile-wrapper {
    margin:auto;
    width:100%;
    max-width:500px;
    height:100vh;
    overflow:hidden;
    position:relative;
}
#result {
    display:none;
    text-align: center;
    z-index: 2;
    color: rgba(255,255,255,0.8);
    max-width: 450px;
    width:85%;
    background: rgba(0,0,0,0.5);
    padding: 10px;
    border-radius: 8px;
    position: absolute;
    bottom: 100px;
    left: 50%;
    transform: translate(-50%, 50%);
}
.modal-kamera {
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    background:rgba(0,0,0,0.9);
    display:none;   
    z-index:10000;
}
button#btn-ambil {
    position:absolute;
    z-index:2;
    bottom:54px;
    background-color:transparent;
    color:#FFF;
    font-weight:bold;
    border-radius:50%;
    padding:23px 20px;
    border:2px solid #FFF;
    left: 50%;
    transform: translate(-50%, 50%);
}

#btn-ganti {
    position:absolute;
    z-index:2;
    bottom:20px;
    background-color:transparent;
    color:#FFF;
    font-weight:bold;
    border-radius:50%;
    padding:23px 20px;
    border:2px solid #FFF;
    left: 10px;
}

#btn-tutup {
    border:0px;
    position:absolute;
    z-index:2;
    background-color:transparent;
    color:#FFF;
    font-weight:bold;
    padding:23px 20px;
    left: 10px;
}
</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"><div class="content-wrapper">
    <div class="card">
          
        <div class="card-header">
              <h3><?= $title ?></h3>
        </div>
            <ul class="list-group list-group-flush">
                <form id="formIzinKerja" method="post" enctype="multipart/form-data">
                <li class="list-group-item">
                
                    <?= $this->session->flashdata('pesan'); ?>
                    <div class="alert alert-danger alert-dismissible fade hide" id="alertCekIzin" role="alert">
                        <span id="textAlert"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="form-group mb-3">
                        <label for="jenis_izin">Jenis Izin</label>
                        <select id="jenis_izin" name="jenis_izin" class="form-control select2">
                            <option value="">Pilih Satu</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Urusan Keluarga">Urusan Keluarga</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <?= form_error('jenis_izin', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    
                 
                        <div class="form-group mb-3" id="tanggalAwal-body">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <div class="input-group">
                                <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control from" autocomplete="OFF" />
                                <div class="input-group-append">
                                    <a id="addTanggal" href="javascript:;" class="input-group-text btn btn-outline-primary ">+</a>
                                </div>
                                <?= form_error('tanggal_awal', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                        </div>
               

                    <div class="form-group mb-3" id="tanggalAkhir-body">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <div class="input-group">
                            <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control to" autocomplete="OFF" />
                            <div class="input-group-append">
                                <a id="removeTanggal" type="button" href="javascript:;" class="input-group-text btn btn-outline-danger ">x</a>
                            </div>
                            <!-- <?= form_error('tanggal_akhir', '<small class="text-danger pl-2">', '</small>'); ?> -->
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="file_izin">Berkas</label>
                        <div id="bodyAmbilFoto">
                            <input type="hidden" id="file_izin" name="file_izin">
                            <div class="row image-response"></div>
                            <button class="btn btn-success btn-sm btn-ambil-foto" id="btnAmbilFoto" type="button" onclick="bukaKamera()"><i class="fa fa-camera fa-fw"></i> Ambil Foto</button>
                            <button class="btn btn-primary btn-sm btn-ambil-foto" id="btnUploadBerkas" type="button"><i class="fa fa-upload fa-fw"></i> Upload Berkas</button>
                        </div>
                        <div id="bodyUploadBerkas" style="display:none">
                            <div id="cover_input_file_lampiran" class="input-group mb-2" style="">
                                <div class="custom-file">
                                    <input type="file" class="lampiran custom-file-input" id="lampiran" name="lampiran">
                                    <label class="col-md-5 custom-file-label" for="lampiran">Pilih File Lampiran</label><br>
                                </div>
                            </div>
                            <button class="btn btn-danger btn-sm btn-ambil-foto" id="btnCloseUploadBerkas" type="button"><i class="fa fa-close fa-fw"></i> Batal</button>
                        </div>
                        <?= form_error('file_izin', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <div id="load-anim" class="mt-5" style="display:none;">
                        <img src="<?=base_url('assets/img/icon/loading.gif');?>" width="30"> Mengajukan permohonan izin kerja. Tunggu sebentar . . .
                    </div>

                </li>
                <li class="list-group-item">
                    <button type="submit" id="btnSubmit" class="btn btn-sm btn-primary" disabled><em class="ti-save"></em> Simpan</button>
                    <a href="<?=base_url('izinkerja/dataizinkerja?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
                </form>
            </ul>
            
    </div>
</div>
<div class="modal-kamera">
    <div class="mobile-wrapper">
        <button onclick="tutupKamera()" id="btn-tutup"><i class="fa fa-times fa-lg"></i></button>
        <video autoplay="true" id="videoElement" autoplay></video>
        <button id="btn-ambil" onclick="ambil()"><i class="fa fa-camera fa-lg fa-fw"></i></button>
    </div>
</div>

<?php $this->view('template/javascript') ?>
<script>
var mobileWrapper = document.querySelector(".mobile-wrapper")
var imageResponse = document.querySelector('.image-response')
var btnSubmit = document.querySelector('.btn-submit')
var video = document.querySelector("#videoElement");
var index = 1
var mystream
var defaultStream = "environment"
var currentDeviceId = ""
var availableDevice = []

$('#btnUploadBerkas').click(function(){
    $('#bodyAmbilFoto').hide();
    $('#bodyUploadBerkas').show();
    $('#file_izin').val("");
    $('.image-response').html("");
});
$('#btnCloseUploadBerkas').click(function(){
    $('#bodyAmbilFoto').show();
    $('#bodyUploadBerkas').hide();
    $('#lampiran').val("");
    let fileName = $('.custom-file-input').val().split('\\').pop();
    $('.custom-file-input').next('.custom-file-label').addClass("selected").html(fileName);

});
 $("#formIzinKerja").on("submit", function(){
     $('#load-anim').show();
     $('#btnSubmit').prop("disabled", true);
 })
function bukaKamera()
{
    window.scrollTo(0, 0);
    document.querySelector(".modal-kamera").style.display="block";
    if (navigator.mediaDevices.getUserMedia) {
        videoConstraints = {}
        videoConstraints.facingMode = defaultStream;
	  	navigator.mediaDevices.getUserMedia({ video: videoConstraints })
	    .then(function (stream) {
	        mystream = stream
	    	video.srcObject = stream;
	    	video.play()
	    })
	    .catch(function (err0r) {
	    	console.log("Something went wrong!");
	    });
	}
}

function tutupKamera()
{
    window.scrollTo(0, 0);
    mystream.getTracks().forEach(track => {
        track.stop();
    });
    currentDeviceId = ""
    document.querySelector(".modal-kamera").style.display="none";
}

function ambil()
{
    var canvas = document.createElement('canvas')
    var context = canvas.getContext('2d')
    canvas.height = video.videoHeight // offsetHeight
    canvas.width = video.videoWidth // offsetWidth
    context.drawImage(video,0,0,canvas.width, canvas.height)
    imageResponse.innerHTML = "<div class='col-12 col-sm-3' style='margin-bottom:10px'><img id='img"+index+"' class='img-thumbnail' src="+canvas.toDataURL("image/jpeg",0.5)+"></div>"
    document.getElementById('file_izin').value = canvas.toDataURL("image/jpeg",1)
    mystream.getTracks().forEach(track => {
        track.stop();
    });
    currentDeviceId = ""
    document.querySelector(".modal-kamera").style.display="none"
    document.querySelector(".btn-ambil-foto").innerHTML="<i class='fa fa-camera fa-fw'></i> Ambil Ulang"
}
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
$('#tanggal_awal_opd').datepicker('setStartDate', '-7d');

$('.to').datepicker({
    autoclose: true,
    format: 'dd-mm-yyyy'
}).on('changeDate', function(selected) {
    FromEndDate = new Date(selected.date.valueOf());
    FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
    $('.from').datepicker('setEndDate', FromEndDate);
});

$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
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


$('#tanggal_awal').on("change keyup paste", function() {
    checkDataIzin();
    return;
});
$('#tanggal_akhir').on("change keyup paste", function() {
    checkDataIzin();
    return;
});

$("#alertCekIzin").hide();



$("#skpd_id").change(function() {
    $('#pegawai_id').find('option').remove().end();
    var skpd_id = $("#skpd_id").val();
    $.ajax({
        type: "post",
        url: "<?= base_url() . '/json/selectOptionPegawaiBySkpd?token=' . $_GET['token']; ?>",
        data: "skpd_id=" + skpd_id,
        success: function(data) {
            $("#pegawai_id").html(data);
        }
    });
    $.ajax({
        type: "post",
        url: "<?= base_url() . '/json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
        data: "skpd_id=" + skpd_id,
        success: function(data) {
            $("#tks_id").html(data);
        }
    });
});

$('#lampiran').on("change keyup paste", function() {
    checkImage();
});

function checkImage() {

    var ext = $('#lampiran').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['jpg', 'pdf', 'png', 'jpeg']) == -1) {
        alert('Terjadi kesalahan, File tidak jelas atau Format kurang sesuai.');
        $("#btnSubmit").attr('disabled', true);
    } else {
        $("#btnSubmit").attr('disabled', false);
    }
}

function checkDataIzin() {
    var tanggal_awal    = $('#tanggal_awal').val();
    var tanggal_akhir   = $('#tanggal_akhir').val() == '' ? tanggal_awal : $('#tanggal_akhir').val();
    tanggal_akhir       = $('#tanggal_akhir').is(':visible') ? $('#tanggal_akhir').val() : tanggal_awal;

    $.ajax({
        type: "POST",
        url: "<?= base_url('/izinkerja/cekIzin?token='.$_GET['token']); ?>",
        data: {
            "tanggal_awal"  : tanggal_awal,
            "tanggal_akhir" : tanggal_akhir,
        },

        success: function(data) {
            data = $.parseJSON(data);
            if (data[0]) {
                $("#alertCekIzin").fadeTo(30000, 500).slideUp(300);
                $('#textAlert').html('Data Izin <strong>Sudah ada</strong> :<br>'+data[1]+"<br><strong>Silahkan Pilih Tanggal Lain.<strong>");
                $("#btnSubmit").attr('disabled', true);
                return;
            }
            $("#btnSubmit").removeAttr('disabled');
            return;
        },
    });
}
</script>