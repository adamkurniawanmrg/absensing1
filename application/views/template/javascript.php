
<!-- inject:js -->
<script src="assets/vendors/js/vendor.bundle.base.js"></script>
<script src="assets/js/off-canvas.js"></script>
<script src="assets/js/hoverable-collapse.js"></script>
<script src="assets/js/template.js"></script>
<!-- endinject -->


<!-- Core plugin JavaScript-->
<script src="<?= base_url('assets/'); ?>vendor/jquery-easing/jquery.easing.min.js"></script>
<!-- AutoNumeric -->
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<!-- Select2 -->
<script src="<?= base_url('assets/') ?>select2/js/select2.min.js"></script>
<!-- CDN Datepicker-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>


<!--<script src="https://code.jquery.com/jquery-3.5.1.js"></script>-->
<!--<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>-->
<!--Datatable Fixed Coloum-->




<script src="<?=base_url("assets/vendors/datatables.net/jquery.dataTables.js")?>"></script>
<script src="<?=base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js")?>"></script>
<script src="<?=base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js")?>"></script>

<script src="<?=base_url("assets/vendors/datatables.net/jquery.dataTables.js")?>"></script>


<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>-->

<script src="https://cdn.datatables.net/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js"></script>

<!--https://code.jquery.com/jquery-3.5.1.js-->
<!--https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js-->


  
  
<script>
    $(document).ready(function() {
        
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    })
</script>
  <!-- Plugin js for this page-->
  <?php 
    if(isset($javascript)){ 
      for($i=0; $i<count($javascript); $i++){
  ?>
    <script src="<?=$javascript[$i];?>"></script>
  <?php }} ?>

  <script>
    <?=isset($javascriptCode) ? $javascriptCode : null;?>
  </script>
  <!-- End custom js for this page-->
