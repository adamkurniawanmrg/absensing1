<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="alert alert-success" id="finish-alert">
                        <button type="button" class="close" data-dismiss="alert">x</button>
                        <strong id="alert_judul"></strong> <span id="alert_deskripsi"></span>
                    </div>
                    <?php
                    $akses = [1,2];
                    if(in_array($this->session->userdata('role_id'), $akses)):
                    ?>
                    <div class="row">
                        <div class="col-md-2 pt-2">Unit Kerja</div>
                        <div class="col-md-10">
                            <div class="form-group mb-2">
                                <select id="skpd_id" name="skpd_id" class="form-control select2">
                                    <option value="">Pilih Unit Kerja</option>
                                    <?php foreach ($skpds as $skpd) { ?>
                                        <option value="<?= $skpd['id_skpd']; ?>"><?= $skpd['nama_skpd']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-2 pt-2">Bulan/Tahun</div>
                        <?php
                            $bulan   = date("m-Y");
                        ?>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= $bulan ?>" />
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <table class="table table-bordered table-hover" id="tablePegawai" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="70">No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th width="200">Nilai <small>(%)</small></th>
    
                            </tr>
                        </thead>
                    </table>
                </li>
                <li class="list-group-item">
                    <button type="submit" id="btnSubmit" class="btn btn-primary mb-3" disabled>Add Skp</button>
                    <a href="<?= site_url('skp?token=' . $_GET['token']) ?>" class="btn btn-danger mb-3">Back</a>
                </li>
            </form>
        </ul>
    </div>
</div>


<?php $this->view('template/javascript'); ?>

<script>
    $("#finish-alert").hide();
    $(document).ready(function() {
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        getPegawai();

        $('#skpd_id').change(function() {
            getPegawai();
        });
        
        function getPegawai(){
            var skpd_id = <?=in_array($this->session->userdata('role_id'), $akses) ? '$("#skpd_id").val()' : $this->session->userdata('skpd_id');?>;
            var bulan = $("#bulan").val();
            if(!skpd_id || skpd_id=="") return;
            $('#tablePegawai').DataTable().destroy();
            $('#tablePegawai').DataTable({
                ajax: {
                    type : "post",
                    url: "<?= base_url('skp/getskppegawaibyskpd?token='.$_GET['token']);?>",
                    data:{
                        skpd_id: skpd_id,
                        skp_id: null,
                        bulan:bulan
                    }
                },
                autoWidth: false,
                paging: false
            });
            checkIfExists();
        }
        
        $('#bulan').change(function() {
            checkIfExists();
        });

        function checkIfExists() {
            var bulan = $('#bulan').val();
            var skpd_id = <?=in_array($this->session->userdata('role_id'), $akses) ? '$("#skpd_id").val()' : $this->session->userdata('skpd_id');?>;
            $.ajax({
                type: "POST",
                url: "<?= base_url() . 'skp/cekskpexists?token=' . $_GET['token']; ?>",
                data: {
                    "bulan": bulan,
                    "skpd_id":skpd_id
                },
                success: function(data) {
                    console.log(data);
                    data = $.parseJSON(data);
                    if (data==true) {
                        $("#btnSubmit").prop('disabled', true);
                        finishAlert("alert-danger", "Gagal !", "SKP sudah terinput sebelumnya, silahkan cek kembali !");
                    } else {
                        $("#btnSubmit").removeAttr('disabled');
                    }
                }
            });
            return false;

        }
        function finishAlert(bg, title, desc) {
            $("#finish-alert").fadeTo(5000, 500).slideUp(500, function() {
                $("#finish-alert").slideUp(500);
            });
            $("#alert_judul").html(title);
            $("#alert_deskripsi").html(desc);
            $("#finish-alert").removeClass("alert-danger");
            $("#finish-alert").removeClass("alert-success");
            $("#finish-alert").addClass(bg);
        }

    });
    



</script>