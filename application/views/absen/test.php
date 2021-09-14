<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Absensi Wajah</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
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
          max-width:500px;
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
        #btn-absen {
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
        </style>
        
        <div class="mobile-wrapper">
            <video autoplay="true" id="videoElement" autoplay></video>
            <div id="result">Memuat...</div>
            <button onclick="ambil()" id="btn-absen"><i class="fa fa-camera fa-lg"></i></button>
            <button onclick="gantiKamera()" id="btn-ganti"><i class="fa fa-refresh fa-lg"></i></button>
        </div>
        <canvas id="canvas"></canvas>
        
        <script type="text/javascript">
        var mobileWrapper = document.querySelector(".mobile-wrapper")
        var video = document.querySelector("#videoElement");
        // var video = document.createElement("video");
        var canvas = document.querySelector("#canvas");
        var canvasContext = canvas.getContext("2d");
        const input2 = video
        var mytimeout, faceMatcher, detection2
        
    	if (navigator.mediaDevices.getUserMedia) {
    	  	navigator.mediaDevices.getUserMedia({ video: true })
    	    .then(function (stream) {
    	    	video.srcObject = stream;
    	    	video.onplaying = e => {
    	    	    canvas.height = video.videoHeight // offsetHeight
    	    	    canvas.width = video.videoWidth // offsetWidth
    	    	}
    	    })
    	    .catch(function (err0r) {
    	    	console.log("Something went wrong!");
    	    });
    	}
    	
    	function ambil()
    	{
    	    canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height)
    	}
    	
    	
        </script> 
    </body>
</html>