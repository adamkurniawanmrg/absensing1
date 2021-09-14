<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?= site_url('skp/addskp?token=' . $_GET['token']) ?>" class="btn btn-sm btn-primary mb-3">Buat SKP</a>
            </li>
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Bulan</div>
                    <?php
                        $bulan   = date("m-Y");
                    ?>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= $bulan ?>" />
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
                                <?php foreach ($skpds as $skpd) { ?>
                                    <option value="<?= $skpd['id_skpd']; ?>"><?= $skpd['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif;?>

                <div class="row">
                    <div class="col-md-12">
                        <button id="btnFilter" class="btn btn-outline-primary btn-sm">Tampilkan</button>
                    </div>
                </div>

            </li>
            <li class="list-group-item">
                <table class="table table-striped table-hover" id="tableSKP" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th style="width: 30px">Bulan</th>
                            <th style="width: 200px">Nama SKPD</th>
                            <th style="width: 50px">Sudah di Input</th>
                            <th style="width: 30px">Action</th>
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
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        $("#btnFilter").click(function() {
            getFilter();
        });

        function getFilter() {
            var bulan = $("#bulan").val();
            var skpd_id = <?=in_array($this->session->userdata('role_id'), $akses) ? '$("#skpd_id").val()' : $this->session->userdata('skpd_id');?>;
            $('#tableSKP').DataTable().destroy();
            $('#tableSKP').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('skp/get_data_skp?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan": bulan,
                        "skpd_id": skpd_id,
                    }
                },
            });
        }
    });

</script>