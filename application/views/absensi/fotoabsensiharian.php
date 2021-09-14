<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="row mb-3">
                    <?php $tanggal   = date("d-m-Y");?>
                    <div class="col-md-2 pt-2">Tanggal</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="tanggal" name="tanggal" type="text" class="form-control" autocomplete="off" value="<?= $tanggal ?>" />
                        </div>
                    </div>
                </div>
                <?php
                $akses = [1,2];
                if(in_array($this->session->userdata('role_id'), $akses)):
                ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($skpd as $s) { ?>
                                    <option value="<?= $s['id_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Jenis Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="jenis_pegawai" name="jenis_pegawai" class="form-control select2">
                                <option value="">Semua Jenis Pegawai</option>
                                <option value="pegawai">PNS</option>
                                <option value="tks">TKS</option>
                            </select>
                        </div>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm">Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <div id="loading-animation" class="text-center p-3">
                    <img src="<?= base_url('assets/img/icon/loading.gif') ?>" width="100" />
                    <br>
                    <br>
                    <h3>Sedang memuat, tunggu sebentar!</h3>
                </div>
                <div class="row" id="body-foto">

                </div>
            </li>
        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('#loading-animation').hide();
        function validateForm(){
            var tanggal          = $("#tanggal").val();
            if(tanggal==""){
                $('#btnFilter').prop('disabled', true);
                return false;
            }

            $('#btnFilter').removeAttr('disabled');
            return true;
        }
        
        $('#jenis_pegawai').change(function(){
            validateForm();
        });
        $('#skpd_id').change(function(){
            validateForm();
        });

        $("#btnFilter").click(function() {
            filter();
        });

        function filter() {
            var tanggal          = $("#tanggal").val();
            var skpd_id          = $("#skpd_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();
            $('#loading-animation').show();
            $.ajax({
                url     : "<?php echo base_url('absensi/getFotoAbsensiHarianPegawai?token=' . $_GET['token']) ?>",
                type    : "POST",
                data    : {
                    tanggal         : tanggal,
                    skpd_id         : skpd_id,
                    jenis_pegawai   : jenis_pegawai
                },
                success: function(res){
                    $('#body-foto').html(res);
                    $('#loading-animation').hide();
                }
            });
        }

    });
</script>