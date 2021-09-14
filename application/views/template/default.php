<!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?=base_url();?>" />
  <meta charset="utf-8">
  <meta name="theme-color" content="#149532">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?=isset($title) ? $title : SITENAME();?></title>

  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/css/style.css">
  
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />

    <link href=" <?php echo base_url('assets/') ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!--CDN - DatePicker-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
    <!--<link href=" <?php echo base_url('assets/') ?>vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">-->

    <!-- Select 2-->
    <link rel="stylesheet" href="<?= base_url('assets/') ?>select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>select2-bootstrap4-theme/select2-bootstrap4.min.css">
    
    <!--Fixed Cloum DataTable-->
    <link href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/3.3.2/css/fixedColumns.dataTables.min.css" rel="stylesheet">
    
    

    
    
    <!-- Multiple Select -->
    <link rel="stylesheet" href="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css">

  <!-- Plugin css for this page -->
  <?php 
    if(isset($css)){ 
      for($i=0; $i<count($css); $i++){
  ?>
    <link rel="stylesheet" href="<?=$css[$i];?>">
  <?php }} ?>
  <!-- End plugin css for this page -->

<style>
<?=isset($cssCode) ? $cssCode : null;?>
.dataTables_wrapper .dataTable .btn, .dataTables_wrapper .dataTable .fc button, .fc .dataTables_wrapper .dataTable button, .dataTables_wrapper .dataTable .ajax-upload-dragdrop .ajax-file-upload, .ajax-upload-dragdrop .dataTables_wrapper .dataTable .ajax-file-upload, .dataTables_wrapper .dataTable .swal2-modal .swal2-buttonswrapper .swal2-styled, .swal2-modal .swal2-buttonswrapper .dataTables_wrapper .dataTable .swal2-styled, .dataTables_wrapper .dataTable .wizard > .actions a, .wizard > .actions .dataTables_wrapper .dataTable a {
  padding: 7px;
  vertical-align: top;
}
.datepicker.datepicker-dropdown .datepicker-days table.table-condensed tbody td.day,
.datepicker.datepicker-inline .datepicker-days table.table-condensed tbody td.day {
  font-size: 0.9375rem;
  padding: 0.5rem 0;
  color: #000;
}
.list-group-hitung .list-group-item {
    border-right-width: 0;
    border-left-width: 0;
    border-radius: 0;
    border-left: 2px solid#71c016; 
    padding: 8px 12px;
}
.list-group-hitung .list-group-item:hover{
    background: #effbef;
}
.horizontal-menu .bottom-navbar .page-navigation > .nav-item.active > .nav-link .menu-title, .horizontal-menu .bottom-navbar .page-navigation > .nav-item.active > .nav-link .menu-arrow {
    color: #7b7575;
}
.horizontal-menu .bottom-navbar .page-navigation > .nav-item .submenu ul li a.active {
    color: #7b7575;
}
.datepicker .datepicker-days table.table-condensed tbody td.disabled.day {
  color: #aaa;
}
.accordion .card {
    margin-bottom: 0;
}
.accordion .card .card-header {
    padding: 1.8rem;
}
.accordion .card .card-header a{
    font-weight: 600;
    letter-spacing 2px;
}
.accordion .collapse .list-group .list-group-item{
    border-bottom: 1px solid #eaeaea;
    text-decoration:none;
    font-weight: 500;
    color: #222;
}
.accordion .collapse .list-group a{
    text-decoration: none;
}
.accordion .card .card-body {
    padding: 0 2rem 1rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    box-sizing: border-box;
    display: inline-block;
    min-width: 1.5em;
    padding: 0;
    margin-top: 0;
    text-align: center;
    text-decoration: none !important;
    cursor: pointer;
    *cursor: hand;
    color: #333 !important;
    border: 0px solid transparent;
    border-top: 1px solid transparent;
    border-radius: 2px;
}
.input-group-append .input-group-text, .input-group-prepend .input-group-text {
    border-color: #C9CCD5;
    padding: 0.575rem 0.75rem;
    color: #484848;
}
select.form-control, select.asColorPicker-input, .dataTables_wrapper select, .jsgrid .jsgrid-table .jsgrid-filter-row select, .select2-container--default select.select2-selection--single, .select2-container--default .select2-selection--single select.select2-search__field, select.typeahead, select.tt-query, select.tt-hint {
    padding: .4375rem .75rem;
    border: 0;
    outline: none; 
    border: 1px solid #c9ccd7;
    color: #202529;
}
.form-control, select.form-control{
    height: 2.375rem;
    border-radius: 4px;
}
.console_tarikdata li{
    margin-left: 20px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    margin-top:5px;
    margin-bottom:-3px;
    padding: 5px 5px;
    font-size: 14px;
}
.label {
    font-size: 10px;
    padding: 2px 4px;
    border-radius: 2px;
    color: white;
}

.bg-tr-danger {
    background: #ffc7ab;
    color: #343434;

}

.bg-tr-danger:hover {
    background: #efb395;
    color: #fff;
}

.bg-tr-warning {
    background: #fccd7f;
    color: #222;
    
}

.bg-tr-warning:hover {
    background: #f2b654;
    color: #222;
}

.bg-tr-info div {
    color: #fff;
}
.bg-tr-info {
    background: #39bd7f;
    color: #fff;
}

.bg-tr-info:hover {
    background: #33a36e;
    color: #fff;
}

.bg-tr-success {
    background: #3cba38;
    color: #fff;
}

.bg-tr-success:hover {
    background: #36a333;
    color: #fff;
}

.tb-wrap{
    white-space: break-spaces;
}
.labura-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 1%;
    padding-bottom: 1%;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.4);
}

.labura-modal-content {
    margin: auto;
    width: 80%;
}

.labura-modal-close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}



.card-modal {
    height: 700px;
}

.rajoe-progress-bar {
    background: #bbbbbb;
    border-radius: 3px;
}

.rajoe-progress-bar .child {
    background: #98EC98;
    height: 5px;
}

.mb-show{
    display: none;
}
.mb-hide{
    display: block;
}
.mb-show-flex{
    display: none;
}
.mb-hide-flex{
    display: flex;
}
.haritanggal{
    width: 250px;
}

@media (max-width: 1320px) {
    .labura-modal-content {
        margin: auto;
        width: 90%;
    }

    .card-modal {
        height: 650px;
    }

}

@media (max-width: 1000px) {
    .labura-modal-content {
        margin: auto;
        width: 90%;
    }

    .card-modal {
        height: 600px;
    }

}

@media (max-width: 780px) {
    .mb-show{
        display: block;
    }
    .mb-hide{
        display: none;
    }
    .mb-show-flex{
        display: flex;
    }
    .mb-hide-flex{
        display: none;
    }
    .haritanggal{
        width: 125px;
    }
    .main-panel {
        background: #ffffff;
    }
}
@media (max-width: 700px) {
    .labura-modal-content {
        margin: auto;
        width: 98%;
    }

    .card-modal {
        height: 600px;
    }

}
</style>

</head>

<body>
  <div class="container-scroller">
    <div class="horizontal-menu">
      <nav class="navbar top-navbar col-lg-12 col-12 p-0">
        <div class="container">
          <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo" href="">
              <img src="<?=$this->session->userdata('websiteLogo') ? $this->session->userdata('websiteLogo') : "assets/images/logolabura.png";?>" alt="logo"/>
              <span><strong><?=strtoupper(SITENAME());?></strong></span>
            </a>
            <a class="navbar-brand brand-logo-mini" href="">
              <img src="<?=$this->session->userdata('websiteLogo') ? $this->session->userdata('websiteLogo') : "assets/images/logolabura.png";?>" alt="logo"/>
              <span><strong><?=strtoupper(SITENAME());?></strong></span>
            </a>
          </div>
          <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav mr-lg-2">
            </ul>
            <ul class="navbar-nav navbar-nav-right">
              <!--<li class="nav-item dropdown mr-1">-->
              <!--  <a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center" id="messageDropdown" href="#" data-toggle="dropdown">-->
              <!--    <i class="ti-email mx-0"></i>-->
              <!--  </a>-->
              <!--  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">-->
              <!--    <p class="mb-0 font-weight-normal float-left dropdown-header">Messages</p>-->
              <!--    <a class="dropdown-item preview-item">-->
              <!--      <div class="preview-thumbnail">-->
              <!--          <img src="https://via.placeholder.com/36x36" alt="image" class="profile-pic">-->
              <!--      </div>-->
              <!--      <div class="preview-item-content flex-grow">-->
              <!--        <h6 class="preview-subject ellipsis font-weight-normal">David Grey-->
              <!--        </h6>-->
              <!--        <p class="font-weight-light small-text text-muted mb-0">-->
              <!--          The meeting is cancelled-->
              <!--        </p>-->
              <!--      </div>-->
              <!--    </a>-->
              <!--    <a class="dropdown-item preview-item">-->
              <!--      <div class="preview-thumbnail">-->
              <!--          <img src="https://via.placeholder.com/36x36" alt="image" class="profile-pic">-->
              <!--      </div>-->
              <!--      <div class="preview-item-content flex-grow">-->
              <!--        <h6 class="preview-subject ellipsis font-weight-normal">Tim Cook-->
              <!--        </h6>-->
              <!--        <p class="font-weight-light small-text text-muted mb-0">-->
              <!--          New product launch-->
              <!--        </p>-->
              <!--      </div>-->
              <!--    </a>-->
              <!--    <a class="dropdown-item preview-item">-->
              <!--      <div class="preview-thumbnail">-->
              <!--          <img src="https://via.placeholder.com/36x36" alt="image" class="profile-pic">-->
              <!--      </div>-->
              <!--      <div class="preview-item-content flex-grow">-->
              <!--        <h6 class="preview-subject ellipsis font-weight-normal"> Johnson-->
              <!--        </h6>-->
              <!--        <p class="font-weight-light small-text text-muted mb-0">-->
              <!--          Upcoming board meeting-->
              <!--        </p>-->
              <!--      </div>-->
              <!--    </a>-->
              <!--  </div>-->
              <!--</li>-->
              <!--<li class="nav-item dropdown">-->
              <!--  <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">-->
              <!--    <i class="ti-bell mx-0"></i>-->
              <!--    <span class="count"></span>-->
              <!--  </a>-->
              <!--  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">-->
              <!--    <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>-->
              <!--    <a class="dropdown-item preview-item">-->
              <!--      <div class="preview-thumbnail">-->
              <!--        <div class="preview-icon bg-success">-->
              <!--          <i class="ti-info-alt mx-0"></i>-->
              <!--        </div>-->
              <!--      </div>-->
              <!--      <div class="preview-item-content">-->
              <!--        <h6 class="preview-subject font-weight-normal">Application Error</h6>-->
              <!--        <p class="font-weight-light small-text mb-0 text-muted">-->
              <!--          Just now-->
              <!--        </p>-->
              <!--      </div>-->
              <!--    </a>-->
              <!--    <a class="dropdown-item preview-item">-->
              <!--      <div class="preview-thumbnail">-->
              <!--        <div class="preview-icon bg-warning">-->
              <!--          <i class="ti-settings mx-0"></i>-->
              <!--        </div>-->
              <!--      </div>-->
              <!--      <div class="preview-item-content">-->
              <!--        <h6 class="preview-subject font-weight-normal">Settings</h6>-->
              <!--        <p class="font-weight-light small-text mb-0 text-muted">-->
              <!--          Private message-->
              <!--        </p>-->
              <!--      </div>-->
              <!--    </a>-->
              <!--    <a class="dropdown-item preview-item">-->
              <!--      <div class="preview-thumbnail">-->
              <!--        <div class="preview-icon bg-info">-->
              <!--          <i class="ti-user mx-0"></i>-->
              <!--        </div>-->
              <!--      </div>-->
              <!--      <div class="preview-item-content">-->
              <!--        <h6 class="preview-subject font-weight-normal">New user registration</h6>-->
              <!--        <p class="font-weight-light small-text mb-0 text-muted">-->
              <!--          2 days ago-->
              <!--        </p>-->
              <!--      </div>-->
              <!--    </a>-->
              <!--  </div>-->
              <!--</li>-->
              <li class="nav-item nav-profile dropdown">
                <a class="nav-link" href="#" data-toggle="dropdown" id="profileDropdown">
                    <h3 class="ti-user" style="margin-top: 5px; font-size: 25px;"></h3>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                  <a class="dropdown-item" href="javascript:;" style="background: #eaeaea;border-top: 5px solid #558B2F; cursor: default">
                    <i class="ti-user text-primary" style="color: #fff; font-weight: 700"></i>
                    <h4><?=$this->session->userdata('nama');?><br>
                    <small><?=$this->session->userdata('nama_opd');?></small></h4>
                  </a>
                  <a class="dropdown-item" href="<?=base_url('auth/logout');?>">
                    <i class="ti-shift-left text-primary"></i>
                    Kembali Ke Portal Layanan
                  </a>
                  <a class="dropdown-item" href="<?=base_url('auth/logoutaplikasi');?>">
                    <i class="ti-power-off text-primary"></i>
                   Logout
                  </a>
                </div>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
              <span class="ti-menu"></span>
            </button>
          </div>
        </div>
      </nav>
      <nav class="bottom-navbar">
        <div class="container">
            <?=getmenu();?>
        </div>
      </nav>
    </div>

    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <div class="main-panel">

        <?php $this->load->view($page);?>

        <footer class="container footer">
          <div class="w-100 clearfix">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © <?=date("Y");?> <a href="https://diskominfo.labura.go.id" target="_blank">DISKOMINFO</a>.</span>
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
