<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?= base_url("izinkerja/addizin?token=".$_GET['token']) ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
                   Tambah Izin Kerja
                </a>
            </li>
            <li class="list-group-item">
                 <?= $this->session->flashdata('pesan'); ?>
                <?php
                $tgl_skrg = date("d-m-Y");
                $tgl_awal = date('01-m-Y', strtotime($tgl_skrg));
                $tgl_akhir = date('Y-m-t', strtotime($tgl_skrg));

                ?>

                <div class="row mb-3">
                    <div class="col-md-2 pt-2">
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
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-row align-items-center" style="margin-left: 0;margin-right: 0">
                            <div class="input-group">
                                <select id="skpd_id" name="skpd_id" class="form-control select2">
                                    <option value="">Pilih Unit Kerja</option>
                                    <?php foreach ($skpd as $s) { ?>
                                        <option value="<?= $s['id_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Jenis Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="jenis_pegawai" name="jenis_pegawai" class="form-control select2">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="pegawai">Pegawai</option>
                                <option value="tks">TKS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2 pt-2">
                        Pegawai
                    </div>
                    <div class="col-md-10">
                        <div class="form-row align-items-center" style="margin-left: 0;margin-right: 0">
                            <div class="input-group">
                                <select id="pegawai_id" name="pegawai_id" class="col select2">
                                    <option value="">Pilih Pegawai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <button id="btnFilter" class="btn btn-outline-primary btn-sm">Filter</button>
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
                                <th>Nama Unit Kerja</th>
                                <th>Jenis Izin</th>
                                <th>Berkas Izin</th>
                                <th>Status</th>
                                <th>Aksi</th>
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

        $("#jenis_pegawai").change(function() {
            getPegawai();
        });

        function getPegawai(){

            $('#pegawai_id').find('option').remove().end();
            var skpd_id          = $("#skpd_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();
            $.ajax({
                type: "post",
                url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'json/selectOptionPegawaiBySkpd?token=' . $_GET['token']; ?>" : "<?= base_url() . 'json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
                data: {
                    "skpd_id"       : skpd_id,
                },
                    
                success: function(data) {
                    $("#pegawai_id").html(data).change();
                }
            });

        }

        $("#btnFilter").click(function() {
            getFilter();
        });

        function getFilter() {
            var tgl_awal = $("#tgl_awal").val();
            var tgl_akhir = $("#tgl_akhir").val();
            var pegawai_id = $("#pegawai_id").val();
            var skpd_id = $("#skpd_id").val();
            var jenis_pegawai = $("#jenis_pegawai").val();
            $('#tableIzinKerja').DataTable().destroy();
            $('#tableIzinKerja').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('izinkerja/getDataIzinKerja?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "tgl_awal": tgl_awal,
                        "tgl_akhir": tgl_akhir,
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id,
                        "jenis_pegawai": jenis_pegawai
                    }
                },
            });
        }

        //Init
        // getFilter();

    });
</script>


