<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <div class="col-3 mt-3">
            
        <a href="<?= base_url("admin2/addizin?token=".$_GET['token']) ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
           Tambah Izin Kerja
        </a>
    
        </div>
        <hr>
        
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                 <?= $this->session->flashdata('pesan'); ?>
                <?php
                $tgl_skrg = date("d-m-Y");
                $tgl_awal = date('01-m-Y', strtotime($tgl_skrg));
                $tgl_akhir = date('Y-m-t', strtotime($tgl_skrg));

                ?>

                <div class="row mb-3">
                    <div class="col-md-2">
                        Tanggal
                    </div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <div class="input-group">
                                <input id="tgl_awal" name="tgl_awal" type="text" class="col-md-6 form-control tgl_awal" autocomplete="OFF" value="<?= $tgl_awal ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text">s/d</span>
                                </div>
                                <input id="tgl_akhir" name="tgl_akhir" type="text" class="col-md-6 form-control tgl_akhir" value="<?= date('d-m-Y') ?>" autocomplete="OFF" value="<?= $tgl_skrg ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">Nama OPD</div>
                    <div class="col-md-10">
                        <div class="form-row align-items-center" style="margin-left: 0;margin-right: 0">
                            <div class="input-group">
                                <select id="opd_id" name="opd_id" class="form-control select2">
                                    <option value="">-- Pilih OPD --</option>
                                    <?php foreach ($opd as $o) { ?>
                                        <option value="<?= $o['id']; ?>"><?= $o['nama_opd']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Pegawai
                    </div>
                    <div class="col-md-10">
                        <div class="form-row align-items-center" style="margin-left: 0;margin-right: 0">
                            <div class="input-group">
                                <select id="pegawai_id" name="pegawai_id" class="col select2">
                                    <option value="">-- Tidak ada data --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                            <button id="filter" class="btn btn-outline-primary btn-sm mb-3">Filter</button>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="table-responsive">
                    <table class="table table-striped" id="tableIzinKerja" width="100%" cellspacing="0">
    
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Dari Tanggal</th>
                                <th>Sampai Tanggal</th>
                                <th>Nama Pegawai</th>
                                <th>Nama OPD</th>
                                <th>Jenis Izin</th>
                                <!--<th>Berkas Izin</th>-->
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        // $('#tgl_awal').datepicker({
        //     format: "dd-mm-yyyy",
        //     autoclose: true
        // });
        
        // $('.tgl_awal').datepicker({
        //     format: 'dd/mm/yyyy',
        //     autoclose: true
        // });
        // $('.tgl_akhir').datepicker({
        //     format: 'dd-mm-yyyy',
        //     autoclose: true
        // });
        // $('#tgl_akhir').datepicker({
        //     format: "dd-mm-yyyy",
        //     autoclose: true
        // });
         var startDate = new Date();
                var fechaFin = new Date();
                var FromEndDate = new Date();
                var ToEndDate = new Date();

                $('.tgl_awal').datepicker({
                    autoclose: true,
                    format: 'dd-mm-yyyy'
                }).on('changeDate', function(selected) {
                    startDate = new Date(selected.date.valueOf());
                    startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
                    $('.tgl_akhir').datepicker('setStartDate', startDate);
                });
                $('.tgl_akhir').datepicker({
                    autoclose: true,
                    format: 'dd-mm-yyyy'
                }).on('changeDate', function(selected) {
                    FromEndDate = new Date(selected.date.valueOf());
                    FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
                    $('.tgl_awal').datepicker('setEndDate', FromEndDate);
                });
    });
</script>
<script>
    $(document).ready(function() {
        $("#filter").click(function() {
            getFilter();
        });

        $("#opd_id").change(function() {
            $('#pegawai_id').find('option').remove().end();
            var opd_id = $("#opd_id").val();
            $.ajax({
                type: "post",
                url: "<?= base_url() . '/pegawai/selectpegawaibyopd?token=' . $_GET['token']; ?>",
                data: "opd_id=" + opd_id,
                success: function(data) {
                    $("#pegawai_id").html(data);
                }
            });
        });

        function getFilter() {
            var tgl_awal = $("#tgl_awal").val();
            var tgl_akhir = $("#tgl_akhir").val();
            var pegawai_id = $("#pegawai_id").val();
            var opd_id = $("#opd_id").val();
            $('#tableIzinKerja').DataTable().destroy();
            $('#tableIzinKerja').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('admin2/get_data_izin_kerja?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "tgl_awal": tgl_awal,
                        "tgl_akhir": tgl_akhir,
                        "opdId": opd_id,
                        "pegawaiId": pegawai_id
                    }
                },
            });
        }

        //Init
        // getFilter();

    });
</script>


