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
                                <option value="">Pilih Unit Kerja</option>
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
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="pegawai">PNS</option>
                                <option value="tks">TKS</option>
                            </select>
                        </div>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm" disabled>Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <table class="table table-striped table-hover" id="tableLogAbsen" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="haritanggal">Nama Pegawai</th>
                            <th>
                                <div class="mb-show-flex">Detail</div>
                                <div class="row mb-hide-flex">
                                    <div class="col-md-3 text-center">Jam Masuk</div>
                                    <div class="col-md-3 text-center">Jam Keluar Istirahat</div>
                                    <div class="col-md-3 text-center">Jam Selesai Istirahat</div>
                                    <div class="col-md-3 text-center">Jam Pulang</div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>
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

        
        function validateForm(){
            var skpd_id          = $("#skpd_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();
            if(skpd_id=="" || jenis_pegawai==""){
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

            $('#tableLogAbsen').DataTable().destroy();
            $('#tableLogAbsen').DataTable({
                processing  : true,
                pageLength  : 100,
                ordering    : false,
                paging      : false,
                ajax: {
                    url     : "<?php echo base_url('absensi/getAbsensiHarianPegawai?token=' . $_GET['token']) ?>",
                    type    : "POST",
                    data    : {
                        tanggal         : tanggal,
                        skpd_id         : skpd_id,
                        jenis_pegawai   : jenis_pegawai
                    }

                },
            });
        }

    });
</script>