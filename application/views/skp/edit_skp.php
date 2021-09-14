<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">

                    <?php
                    $akses = [1,2];
                    if(in_array($this->session->userdata('role_id'), $akses)):
                    ?>
                    <div class="row">
                        <div class="col-md-2 pt-2">Unit Kerja</div>
                        <div class="col-md-10">
                            <div class="form-group mb-2">
                                <select id="skpd_id" name="skpd_id" class="form-control select2" disabled>
                                    <option value="">Pilih Unit Kerja</option>
                                    <?php foreach ($skpds as $skpd) { ?>
                                        <option value="<?= $skpd['id_skpd']; ?>" <?=$skpd['id_skpd']==$skp['skpd_id'] ? "selected" : null;?>><?= $skpd['nama_skpd']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-2 pt-2">Bulan/Tahun</div>
                        <?php
                            $bulan   = date("m-Y", strtotime($skp['bulan']));
                        ?>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= $bulan ?>" style="padding-left: 8px;" disabled />
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
                    <button type="submit" class="btn btn-primary">Add Skp</button>
                    <a href="<?= base_url('skp?token=' . $_GET['token']) ?>" class="btn btn-danger">Back</a>
                </li>
            </form>
        </ul>
    </div>
</div>


<?php $this->view('template/javascript'); ?>

<script>
    $(document).ready(function() {
        $('#tablePegawai').DataTable().destroy();
        $('#tablePegawai').DataTable({
            ajax: {
                type : "post",
                url: "<?= base_url('skp/getskppegawaibyskpd?token='.$_GET['token']);?>",
                data:{
                    skpd_id: <?=$skp['skpd_id'];?>,
                    skp_id: <?=$skp['id'];?>,
                    bulan:'<?=$skp['bulan'];?>'
                }
            },
            autoWidth: false,
            paging: false
        });
    });
</script>