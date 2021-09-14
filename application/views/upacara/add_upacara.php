<div class="content-wrapper">
    <div class="card">
          
        <div class="card-header">
              <h3><?=$title ?></h3>
        </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="<?=base_url('upacara/upacara?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            
                <li class="list-group-item">
                
                  <form class="forms-sample" action="" method="post">
                       <div class="form-group">
                        <label for="nama_hari">Nama Hari</label>
                        <input id="nama_hari" class="form-control" type="text" name="nama_hari">
                        <?= form_error('nama_hari', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input id="tanggal" name="tanggal" type="text" class="col-12 form-control from" autocomplete="OFF" />
                        <?= form_error('tanggal', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    
                    <!--<div id="datepicker-popup" class="input-group date datepicker">-->
                    <!--    <input type="text" class="form-control">-->
                    <!--    <span class="input-group-addon input-group-append border-left">-->
                    <!--      <span class="ti-calendar input-group-text"></span>-->
                    <!--    </span>-->
                    <!--  </div>-->

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <select name="kategori" id="kategori" class="col-12 form-control">
                            <option value="">Pilih Kategori</option>
                            <option value="1">Upacara</option>
                            <option value="2">Libur</option>
                        </select>
                    </div>
                    <div class="form-group form-upacara-libur">
                        <label for="upacara_libur">Upacara Dihari Libur</label><br />
                        <input type="checkbox" name="upacara_libur" id="upacara_libur" value="yes"> Ya
                    </div>
                    
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                     </div>
                  </form>
                  
                </li>
              </ul>
            
    </div>
</div>

<?php $this->view('template/javascript'); ?>
<script type="text/javascript">
    $(document).ready(function() {
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
        $('.to').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(selected) {
            FromEndDate = new Date(selected.date.valueOf());
            FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
            $('.from').datepicker('setEndDate', FromEndDate);
        });
    });
</script>

<!-- <script type="text/javascript">
    $(document).ready(function() {
        $('#tanggal').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: false
        });

    });
</script> -->
<script>
    $(document).ready(function() {
        changeFormUpacaraLibur();
        //init(), Form Tambah upacara libur, Ubah upacara libur
        $('#kategori').change(function() {
            changeFormUpacaraLibur();
        });
        // Form Tambah upacara libur, Ubah upacara libur
        function changeFormUpacaraLibur() {
            if ($('#kategori').val() == 1) {
                $('.form-upacara-libur').show();
            } else if ($('#kategori').val() == 2) {
                $('.form-upacara-libur').hide();
            } else {
                $('.form-upacara-libur').hide();
            }
        }
        changeForm();
        changeFormUpacaraLibur();
    });
</script>
            
      