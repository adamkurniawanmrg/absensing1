<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
            </li>
            <li class="list-group-item">
                <?php
                    $tgl_skrg = date("d-m-Y");
                    $tgl_awal = date('01-m-Y', strtotime($tgl_skrg));
                    $tgl_akhir = date('Y-m-t', strtotime($tgl_skrg));
                ?>
                <?= $this->session->flashdata('pesan'); ?>

                <div class="row mb-3">
                    <div class="col-md-2">Tanggal</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="tgl_awal" name="tgl_awal" type="text" class="form-control from" value="<?= $tgl_awal ?>" autocomplete="OFF" />
                            <div class="input-group-append">
                                <span class="input-group-text">s/d</span>
                            </div>
                            <input id="tgl_akhir" name="tgl_akhir" type="text" class="form-control to" value="<?= $tgl_skrg ?>" autocomplete="OFF" />
                        </div>
                    </div>
                </div>
                
                <?php $akses = [1,2,3]; if(in_array($this->session->userdata('role_id'), $akses)): ?>
                <div class="row mb-3">
                    <div class="col-md-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="0">Pilih Unit Kerja</option>
                                <?php foreach ($skpd as $s) { ?>
                                    <option value="<?= $s['id_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                
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

                <div class="row mb-3">
                    <div class="col-md-2">Nama Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <select id="pegawai_id" name="pegawai_id" class="form-control select2">
                                <option value="">Tidak ada data</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <div class="row mb-3">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm">Filter</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <div class="table-responsive">
                    <table class="table table-striped" id="tableAbsensiManual" cellpadding="8">
                        <thead>
                            <tr>
                                <th style="width:20px">No</th>
                                <th>Tanggal</th>
                                <th>Nama Pegawai</th>
                                <th>Nama Unit Kerja</th>
                                <th>Jenis Absen</th>
                                <th>Waktu</th>
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

        $('.from').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
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

        $("#jenis_pegawai").change(function() {
            getPegawai();
        });
        $("#skpd_id").change(function() {
            if($("#jenis_pegawai").val()==""){
                return;
            }
            getPegawai();
        });
        function getPegawai(){
            $('#pegawai_id').find('option').remove().end();
            var skpd_id = $("#skpd_id").val();
            var jenis_pegawai = $("#jenis_pegawai").val();
            $.ajax({
                type: "post",
                url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'json/selectOptionPegawaiBySkpd?token=' . $_GET['token']; ?>" : "<?= base_url() . 'json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
                data: {
                    'skpd_id': skpd_id
                },
                success: function(data) {
                    $("#pegawai_id").html(data);
                }
            });

        }

        $("#btnFilter").click(function() {
            getFilter();
        });

        function getFilter() {
            var tgl_awal = $("#tgl_awal").val();
            var tgl_akhir = $("#tgl_akhir").val();
            var skpd_id = $("#skpd_id").val();
            var pegawai_id = $("#pegawai_id").val();
            $('#tableAbsensiManual').DataTable().destroy();
            $('#tableAbsensiManual').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('absen/getDataAbsen?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "tgl_awal": tgl_awal,
                        "tgl_akhir": tgl_akhir,
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id
                    }
                },
                'columnDefs': [
                    {
                        "width": "200",
                        "targets": [1]
                    },
                    {
                        "className": "text-center",
                        "targets": [4, 5]
                    },
                    {
                        "className": "text-right",
                        "targets": [6]
                    },
                ]

            });
        }
    
    });
</script>