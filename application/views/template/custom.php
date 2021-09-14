<!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?=base_url();?>" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?=isset($title) ? $title : "Absensi Fingerprint";?></title>

  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/css/style.css">
  

  <!-- Plugin css for this page -->
  <?php 
    if(isset($css)){ 
      for($i=0; $i<count($css); $i++){
  ?>
    <link rel="stylesheet" href="<?=$css[$i];?>">
  <?php }} ?>
  <!-- End plugin css for this page -->

</head>

<body>
  <div class="container-scroller">
      
    <div class="horizontal-menu">
      <nav class="navbar top-navbar col-lg-12 col-12 p-0">
        <div class="container">
          <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo" href="">
              <img src="assets/images/logolabura.png" alt="logo"/>
              <span><strong>ABSENSI</strong></span>
            </a>
            <a class="navbar-brand brand-logo-mini" href="">
              <img src="assets/images/logolabura.png" alt="logo"/>
            </a>
          </div>
        </div>
      </nav>
    </div>
    
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <div class="main-panel" style="padding-top: 60px">

        <?php $this->load->view($page);?>

        <footer class="container footer">
          <div class="w-100 clearfix">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © <?=date("Y");?> <a href="https://diskominfo.labura.go.id" target="_blank">DISKOMINFO</a>. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>
          </div>
        </footer>

        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->




</body>

</html>
