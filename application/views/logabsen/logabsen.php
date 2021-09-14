<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
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
                $akses = [1];
                $akses2 = [1,3];
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
                <?php   if(in_array($this->session->userdata('role_id'), $akses2)): ?>
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
                    <div class="col-md-2 pt-2">Nama Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="pegawai_id" name="pegawai_id" class="form-control select2">
                                <option value="0">Pilih Pegawai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm" <?=in_array($this->session->userdata('role_id'), $akses) ? 'disabled' :null;?>>Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <table class="table table-hover" id="tableLogAbsen" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="haritanggal">Hari/Tanggal</th>
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
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        $("#jenis_pegawai").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                alert('Pastikan field SKPD dan Jenis Pegawai sudah terpilih!');
                return;
            }
            validForm();
            getPegawai();
        });
        $("#skpd_id").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                return;
            }
            validForm();
            getPegawai();
        });
        
        $('#pegawai_id').change(function(){
            validForm();
        });
        
        function getPegawai(){

            $('#pegawai_id').find('option').remove().end();
            var skpd_id          = <?= !in_array($this->session->userdata('role_id'), $akses) ? $this->session->userdata('skpd_id') : '$("#skpd_id").val()'?>;
            var jenis_pegawai    = $("#jenis_pegawai").val();
            $.ajax({
                type: "post",
                url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'json/selectOptionPegawaiBySkpd?is_single=true&token=' . $_GET['token']; ?>" : "<?= base_url() . 'json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
                data: {
                    "skpd_id"       : skpd_id,
                },
                    
                success: function(data) {
                    $("#pegawai_id").html(data).change();
                }
            });

        }

        function validForm(){
            var skpd_id          = <?= !in_array($this->session->userdata('role_id'), $akses) ? $this->session->userdata('skpd_id') : '$("#skpd_id").val()'?>;
            var pegawai_id       = $("#pegawai_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();

            if(skpd_id=="" || pegawai_id=="" || jenis_pegawai==""){
                $('#btnFilter').prop("disabled", true);
            }else{
                $('#btnFilter').removeAttr("disabled");
            }
            
        }

        $("#btnFilter").click(function() {
            filter();
        });

        function filter() {
            var bulan            = $("#bulan").val();
            var skpd_id          = $("#skpd_id").val();
            var pegawai_id       = $("#pegawai_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();

            $('#tableLogAbsen').DataTable().destroy();
            $('#tableLogAbsen').DataTable({
                "processing": true,
                "pageLength": 100,
                "ordering"  : false,
                'paging'    : false,
                "ajax": {
                    "url": "<?php echo base_url('logabsen/getLogAbsen2?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan": bulan,
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id,
                        "jenis_pegawai": jenis_pegawai
                    }

                },
                "createdRow": function(row, data, dataIndex) {
                    if (parseInt(data[2])>=6) {
                        $(row).find('td:eq(0)').addClass('bg-tr-danger');
                        $(row).find('td:eq(1)').html('<center>Libur</center>');

                    }
                    if (data[3]!=null) {
                        if (data[4] == 'yes') {
                            $(row).find('td:eq(0)').addClass('bg-tr-info');
                        } else if (data[4] == null) {
                            $(row).find('td:eq(0)').addClass('bg-tr-warning');
                            $(row).find('td:eq(1)').html('<center>Libur</center>');
                        }
                    }
                }
            });
        }

    });
</script>