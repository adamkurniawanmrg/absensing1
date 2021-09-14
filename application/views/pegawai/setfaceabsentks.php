<style>
video, canvas {
  -webkit-transform: scaleX(-1);
  transform: scaleX(-1);
  height:100%;
  width:100%;
  object-fit:cover;
}
.img-thumbnail {
    -webkit-transform: scaleX(-1);
  transform: scaleX(-1);
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
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?></h3>
          <div style="position: absolute;right: 8px;top: 6px;"><a href="<?=base_url('pegawai/tks?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a></div>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row image-response">
                    <?php
                        foreach ($all_files as $key => $file)
                        {
							$i = $key+1;
                            $image_name = $file->name();
                            $supported_format = array('gif','jpg','jpeg','png');
                            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                            if (in_array($ext, $supported_format)){
                                echo "<div class='col-md-3 gambar-faceabsen-".$i."' style='position:relative'>";
                                echo '<img src="https://storage.googleapis.com/file-absensi/'.($image_name) .'" width="100%" alt="'.$image_name.'" class="img-thumbnail" />';
                                echo '<button class="btn btn-sm btn-danger btn-gambar-faceabsen" data-index="'.$i.'" style="border-radius:50%; padding: 15px;top:10px;right:20px; position: absolute"><em class="ti-close"></em></button>';
                                echo "</div>";
                            }else{
                                continue;
                            }
                        }
                    ?>

                </div>
            </li>
            <li class="list-group-item">
                <div class="row mb-2" id="bodyUpload" style="display:none">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Pilih File : </label>
                            <input type="file" name="foto" id="foto" multiple style="height:auto;" onchange="loadImage(this)">
                        </div>

                    </div>
                </div>
                <button class="btn btn-outline-primary" id="btnUploadFoto" onclick="uploadfoto()"><em class="ti ti-upload"></em> Upload</button>
                <button class="btn btn-outline-success" onclick="bukaKamera()"><em class="ti ti-camera"></em> Ambil Foto</button>
            </li>
            <li class="list-group-item">
                <button class="btn btn-primary btn-submit" id="btnSubmit" <?= iterator_count($all_files) ? '' : 'style="display:none"' ?> onclick="train()">Simpan dan Perbaharui</button>
            </li>
        </ul>

    </div>
</div>
<div class="modal-kamera">
    <div class="mobile-wrapper">
        <button onclick="tutupKamera()" id="btn-tutup"><i class="fa fa-times fa-lg"></i></button>
        <video autoplay="true" id="videoElement" autoplay></video>
        <button onclick="gantiKamera()" id="btn-ganti"><i class="fa fa-refresh fa-lg fa-fw"></i></button>
        <button id="btn-ambil" onclick="ambil()"><i class="fa fa-camera fa-lg fa-fw"></i></button>
    </div>
</div>
<!-- /.container-fluid -->

<?php $this->view('template/javascript'); ?>
<script src="/js/face-api/face-api.min.js"></script>
<script>
var mobileWrapper = document.querySelector(".mobile-wrapper")
var imageResponse = document.querySelector('.image-response')
var btnSubmit = document.querySelector('.btn-submit')
var video = document.querySelector("#videoElement");
var allImages = []
var index = 1
var mystream
var defaultStream = "user"
var currentDeviceId = ""
var availableDevice = []

$('.btn-gambar-faceabsen').each(function(i,v){
    $(this).click(function(){
        if(!confirm('Apakah anda yakin untuk menghapus?')) return;
        var index = $(this).data('index')
        $('.gambar-faceabsen-'+index).remove();
    });    
});


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

function gantiKamera()
{
    mystream.getTracks().forEach(track => {
        track.stop();
    });
    
    defaultStream = defaultStream == 'user' ? 'environment' : 'user';
    if(defaultStream == 'environment')
        video.style.transform = 'scaleX(1)'
    else
        video.style.transform = 'scaleX(-1)'
    
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

function uploadfoto(){
    if(document.querySelector("#bodyUpload").style.display=="none"){
        $("#bodyUpload").show();
        $("#btnUploadFoto").removeClass("btn-outline-primary");
        $("#btnUploadFoto").addClass("btn-danger");
        $("#btnUploadFoto").html('<em class="ti ti-close"></em> Batal Upload');
    }else{
        $("#bodyUpload").hide();
        $("#btnUploadFoto").removeClass("btn-danger");
        $("#btnUploadFoto").addClass("btn-outline-primary");
        $("#btnUploadFoto").html('<em class="ti ti-upload"></em> Upload');
    }
}

function ambil()
{
    var canvas = document.createElement('canvas')
    var context = canvas.getContext('2d')
    if(defaultStream == 'environment')
        canvas.style.transform = 'scaleX(1)'
    else
        canvas.style.transform = 'scaleX(-1)'
    canvas.height = video.videoHeight // offsetHeight
    canvas.width = video.videoWidth // offsetWidth
    context.drawImage(video,0,0,canvas.width, canvas.height)
    pushImage(canvas.toDataURL("image/jpeg",1))
    mystream.getTracks().forEach(track => {
        track.stop();
    });
    currentDeviceId = ""
    document.querySelector(".modal-kamera").style.display="none"
}

function pushImage(imageSrc)
{
    allImages.push(imageSrc)
    imageResponse.innerHTML += "<div class='col-12 col-sm-3 gambar-faceabsen' style='margin-bottom:10px'><img id='img"+index+"' class='img-thumbnail' src="+imageSrc+"></div>"
    index++
    if(index > 1) btnSubmit.style.display = "inline-block"
}

function loadImage(el)
{
    var files = el.files
    Array.from(files).forEach((file,index) => {
        var reader = new FileReader();
    
        reader.onload = function(e) {
            imageResponse.innerHTML += "<div class='col-12 col-sm-3' style='margin-bottom:10px'><img id='img"+index+"' class='img-thumbnail' src="+e.target.result+"></div>"
            //  $('#blah').attr('src', e.target.result);
        }
            
        reader.readAsDataURL(file); // convert to base64 string
    })
    btnSubmit.style.display = "block"
    el.value = null;
    uploadfoto();
}

async function train()
{
    $('#btnSubmit').prop('disabled', true);
    $('#btnSubmit').html('Sedang memproses. .');
    var imgThumbnail = document.querySelectorAll('.img-thumbnail')
    
    var formData = new FormData;
    for(i=0;i<imgThumbnail.length;i++)
    {
        var canvas = document.createElement('canvas')
	    var context = canvas.getContext('2d')
	    canvas.height = imgThumbnail[i].offsetHeight // offsetHeight
	    canvas.width = imgThumbnail[i].offsetWidth // offsetWidth
	    context.drawImage(imgThumbnail[i],0,0,canvas.width,canvas.height)
        formData.append('file[]',canvas.toDataURL("image/jpeg",1))
    }
    
	var request = await fetch('/pegawai/savefaceabsen/<?=$pegawai['username']?>?token=<?=$_GET['token']?>',{
		method:'POST',
		body:formData
	})
	var response = await request.json()
	if(response.status == 'success'){
		alert('Berhasil')
	    btnSubmit.style.display = "none"
        $('#btnSubmit').removeAttr('disabled');
        $('#btnSubmit').html('Simpan dan Perbaharui');
	}else{
	    alert('Gagal')
        $('#btnSubmit').removeAttr('disabled');
        $('#btnSubmit').html('Simpan dan Perbaharui');

	}
}
</script>
