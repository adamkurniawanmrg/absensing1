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
        video {
            opacity:0;
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
            cursor:pointer;
        }
        #form-keterangan {
            margin:20px;
        }
        button#btn-absen {
            display:none;
            bottom:0;
            padding:15px;
            position:absolute;
            z-index:2;
            border:0;
            width:100%;
            left:0;
        }
        #btn-mulai {
            width:100%;
            border:0;
            padding:15px;
        }
        textarea {
            border:1px solid #eaeaea;
            width:100%;
            padding:10px;
            display:block;
            resize:none;
            font-family:arial;
        }
        </style>

        <div class="mobile-wrapper">
            <div id="form-keterangan">
                <br>
                <h4 align="center">Keterangan</h4>
                <textarea id="keterangan" placeholder="Keterangan disini..."></textarea>
                <button id="btn-mulai" onclick="mulai()">Mulai Pemindaian</button>    
            </div>
            <video autoplay="true" id="videoElement" autoplay></video>
            <div id="result">Memuat...</div>
            <button onclick="video.play()" id="btn-absen">Pindai Ulang</button>
        </div>
        <script type="text/javascript">
        var mobileWrapper = document.querySelector(".mobile-wrapper")
        var video = document.querySelector("#videoElement");
        var canvas = document.querySelector("#canvas");
        var keterangan = document.querySelector("#keterangan")
        const input2 = video
        var mytimeout, faceMatcher, detection2
        
        function mulai(){
            if(keterangan.value == "")
            {
                document.getElementById('btn-mulai').innerHTML = "Keterangan tidak boleh kosong"
                setTimeout(e => {
                    document.getElementById('btn-mulai').innerHTML = "Mulai Pemindaian"
                },3000)
                return
            }
        	if (navigator.mediaDevices.getUserMedia) {
        	    video.style.opacity = 1
        	  	navigator.mediaDevices.getUserMedia({ video: true })
        	    .then(function (stream) {
        	        document.getElementById('result').style.display = "block";
                    document.getElementById('result').innerHTML = "Memuat Kamera...";
        	    	video.srcObject = stream;
        	    	video.onplaying = async function(){
        	    	    document.getElementById('btn-absen').style.display = "none";
        	    	    document.getElementById('form-keterangan').style.display = "none";
        	    	    start()
        	    	}
        	    })
        	    .catch(function (err0r) {
        	    	console.log("Something went wrong!");
        	    });
        	}
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
		    formData.append('keterangan',keterangan.value)
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
            	    
		            
		    })
    	}
    	
    	function start()
        {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
    	        document.getElementById('result').innerHTML = "Sedang Memindai! Diharapkan untuk tidak bergerak selama proses pemindaian."
                setTimeout(e => {
                    document.getElementById('result').innerHTML = "Sedang Mengidentifikasi Wajah. Mohon tunggu..."
            	    detectingFace()
                },3000)
            }else{
                start()
            }
        }
        </script> 
    </body>
</html>