<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
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
                                <th style="width:50px">AMP</th>
                                <th style="width:50px">AMS</th>
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

        getFilter();

        function getFilter() {
            var tgl_awal = $("#tgl_awal").val();
            var tgl_akhir = $("#tgl_akhir").val();
            var skpd_id = $("#skpd_id").val();
            var pegawai_id = $("#pegawai_id").val();
            $('#tableAbsensiManual').DataTable().destroy();
            $('#tableAbsensiManual').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('verifikasi/getDataAbsenManual?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "tgl_awal": tgl_awal,
                        "tgl_akhir": tgl_akhir,
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id
                    }
                },
                'columnDefs': [{
                            "width": "80",
                        "targets": [8]
                    },
                    {
                        "width": "200",
                        "targets": [1]
                    },
                    {
                        "className": "text-center",
                        "targets": [4, 5, 6]
                    },
                    {
                        "className": "text-right",
                        "targets": [7]
                    },
                ]

            });
        }
    
    });
</script>