<?php                
    $pegawaiMeta = $this->session->userdata('jenis_pegawai')=='pegawai' ? 
                                    $this->db->where('pegawai_id', $this->session->userdata('user_id'))->get('tb_pegawai_meta')->row():
                                    $this->db->where('tks_id', $this->session->userdata('user_id'))->get('tb_tks_meta')->row();

?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Absensi Wajah</title>
    </head>
    <body>
       <style type="text/css">
        body {
            margin:0;
            padding:0;
        }
        video, canvas {
          -webkit-transform: scaleX(-1);
          transform: scaleX(-1);
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
        button {
            display:none;
            bottom:0;
            padding:15px;
            position:absolute;
            z-index:2;
            border:0;
            width:100%;
            left:0;
        }
        </style>

        <!--<button id="start-btn" onclick="start()">Mulai Absen</button>-->
        <!--<br>-->
        <div class="mobile-wrapper">
            <!--<canvas id="canvas"></canvas>-->
            <video autoplay="true" id="videoElement" autoplay></video>
            <div id="result">Memuat...</div>
            <button onclick="video.play()" id="btn-absen">Pindai Ulang</button>
        </div>
        <script type="text/javascript">
        var mobileWrapper = document.querySelector(".mobile-wrapper")
        var video = document.querySelector("#videoElement");
        // var video = document.createElement("video");
        var canvas = document.querySelector("#canvas");
        // var canvasContext = canvas.getContext("2d");
        const input2 = video
        var mytimeout, faceMatcher, detection2

        function startMedia(coor)
        {
            <?php 
                if(isset($pegawaiMeta->kordinat_bebas) && $pegawaiMeta->kordinat_bebas=="Ya"){
                    echo "getMedia();";
                }else if(isset($pegawaiMeta->kordinat_khusus) && $pegawaiMeta->kordinat_khusus=="Ya"){
                    
                    if(count(unserialize($pegawaiMeta->kordinats))>0){
                        foreach(unserialize($pegawaiMeta->kordinats) as $kordinat_id){
                            $this->db->or_where('id', $kordinat_id);
                        }
                        $kordinats = $this->db->get('tb_kordinat_tambahan')->result();
                        if(count($kordinats)>0){
                            foreach($kordinats as $k){
                                ?>
                                    // -----------------------------------------------------------------
                                    var jarak = distanceBetweenEarthCoordinates(coor,<?=json_encode(['lat'=>$k->latitude,'lng'=>$k->longitude])?>)
                                    if(jarak <= <?=$k->radius?> && !isNaN(jarak))
                                    {
                                        Android.showToast("Anda sedang berada di lokasi absen <?=$k->nama_kordinat;?>")
                                        getMedia();
                                        return;
                                    }
                                    // -----------------------------------------------------------------
                                <?php
                            }
                        }
                    }
                ?>
                    if (typeof(Android) != "undefined")
                        Android.showToast("Anda sedang tidak berada di lokasi absen khusus!")
                    history.back(-1)
                    return

                <?php

                }else{
            ?>
            // -----------------------------------------------------------------
            var jarak = distanceBetweenEarthCoordinates(coor,<?=json_encode(['lat'=>$koordinat->latitude,'lng'=>$koordinat->longitude])?>)
            if(jarak > <?=$koordinat->radius?> || isNaN(jarak))
            {
                if (typeof(Android) != "undefined")
                    Android.showToast("Anda sedang tidak berada di lokasi absen. Jarak = "+jarak)
                history.back(-1)
                return
            }
            getMedia();
            // -----------------------------------------------------------------
            <?php } ?>
        }

        function getMedia(){
        	if (navigator.mediaDevices.getUserMedia) {
        	  	navigator.mediaDevices.getUserMedia({ video: true })
        	    .then(function (stream) {
        	        document.getElementById('result').style.display = "block";
                    document.getElementById('result').innerHTML = "Memuat Kamera...";
        	    	video.srcObject = stream;
        	    	video.onplaying = async function(){
        	    	    document.getElementById('btn-absen').style.display = "none";
        	    	    start()
        	    	}
        	    })
        	    .catch(function (err0r) {
        	    	console.log("Something went wrong!");
        	    });
        	}

        }
        
        function degreesToRadians(degrees) {
          return degrees * Math.PI / 180;
        }
        
        function distanceBetweenEarthCoordinates(coor1, coor2) {
          var earthRadius = 6371;
        
          var dLat = degreesToRadians(coor2.lat-coor1.lat);
          var dLon = degreesToRadians(coor2.lng-coor1.lng);
        
          lat1 = degreesToRadians(coor1.lat);
          lat2 = degreesToRadians(coor2.lat);
        
          var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(coor1.lat) * Math.cos(coor2.lat); 
          var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
          return earthRadius * 1000 * c;
        }
        
    	
    	async function detectingFace()
    	{
    	    video.pause()
    	    var canvas = document.createElement('canvas')
		    var context = canvas.getContext('2d')
		    canvas.height = video.videoHeight // offsetHeight
    	    canvas.width = video.videoWidth // offsetWidth
		    context.drawImage(video,0,0,canvas.width,canvas.height)
		    var formData = new FormData;
		    formData.append('jenis_absen','<?=$_GET['kategori']?>')
		    formData.append('keterangan','')
		    formData.append('file_absensi',canvas.toDataURL("image/jpeg",0.5))
		    
		    fetch('/absen/pushAbsen?token=<?=$_GET['token']?>',{
		        method:'POST',
		        body:formData
		    }).then(e => e.text())
		    .then(e => {
		        if(e == "")
		        {
		            document.getElementById('result').innerHTML = "Wajah anda tidak ditemukan. Disarankan agar menggunakan kamera yang bagus dengan pengambilan yang bagus";
            	    document.getElementById('btn-absen').style.display = "block";
            	    return
		        }
		        var e = JSON.parse(e)
	            var cocok = e.status == 'fail' ? "Tidak Cocok" : "Cocok"
	            document.getElementById('result').innerHTML = "Wajah "+cocok+". "+e.message
	            
	            if(e.status == 'success')
	            {
	                setTimeout(() => {
    		            location.href='/absen/absen_berhasil?token=<?=$_GET['token']?>&id='+e.id
	                },1000)
	            }
	            else
	                document.getElementById('btn-absen').style.display = "block";
            	    
		            
		    }).catch(e => {
		        console.log(e)
		        alert(e)
		    })
    	}
        
        function start()
        {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
    	        document.getElementById('result').innerHTML = "Sedang Memindai! Diharapkan untuk tidak bergerak selama proses pemindaian."
                setTimeout(e => {
                    document.getElementById('result').innerHTML = "Sedang Mengidentifikasi Wajah. Mohon tunggu..."
            	    detectingFace()
                },5000)
            }else{
                start()
            }
        }
        
        <?php /*
        <?php if($_SESSION['username'] == '199511062020121005'): ?>
        if (navigator.mediaDevices.getUserMedia) {
    	  	navigator.mediaDevices.getUserMedia({ video: true })
    	    .then(function (stream) {
    	        document.getElementById('result').style.display = "block";
                document.getElementById('result').innerHTML = "Memuat Kamera...";
    	    	video.srcObject = stream;
    	    	video.onplaying = async function(){
    	    	    document.getElementById('btn-absen').style.display = "none";
    	    	    start()
    	    	}
    	    })
    	    .catch(function (err0r) {
    	    	console.log("Something went wrong!");
    	    });
    	}
        <?php endif ?>
        */ ?>
        </script> 
    </body>
</html>