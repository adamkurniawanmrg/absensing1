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
                $akses = [1,2,3];
                if(in_array($this->session->userdata('role_id'), $akses)){
                ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">Pilih Unit Kerja</option>
                                <?php foreach ($skpdsOpt as $s) { ?>
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
                    <div class="col-md-2 pt-2">Nama Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="pegawai_id" name="pegawai_id" class="form-control select2">
                                <option value="0">Pilih Pegawai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm" <?=in_array($this->session->userdata('role_id'), $akses) ? 'disabled' :null;?>>Tampilkan</button></div>
                </div>

            </li>
            <form method="post">
                <input type="hidden" id="h_pegawai_id" name="pegawai_id">
                <input type="hidden" id="h_jenis_pegawai" name="jenis_pegawai">
                <li class="list-group-item">
                    <table class="table table-hover" id="tableLogAbsen" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="haritanggal">Hari/Tanggal</th>
                                <th class="text-center">
                                    Jam Kerja Yang Dipakai
                                </th>
                            </tr>
                        </thead>
                    </table>
                </li>
                <li class="list-group-item" id="body-btnsubmit" style="display:none;">
                    <button class="btn btn-sm btn-primary"><em class="ti-save"></em> Simpan</button>
                </li>
            </form>

        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>
<style>
.dataTable .select2-container--default .select2-selection--single .select2-selection__rendered {
    margin-top: -14px;
}
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });
    });
</script>

<script>
    $(document).ready(function() {
        var startDate = new Date();
        var fechaFin = new Date();
        var FromEndDate = new Date();
        var ToEndDate = new Date();

        $('.from').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
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

        $('#tgl_akhir').datepicker('setEndDate', '+0d');
    });
</script>
<script>
    $(document).ready(function() {
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

        function validForm(){
            var skpd_id          = $("#skpd_id").val();
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
            
            $('#body-btnsubmit').hide();

            $('#tableLogAbsen').DataTable().destroy();
            $('#tableLogAbsen').DataTable({
                "processing": true,
                "pageLength": 100,
                "ordering"  : false,
                'paging'    : false,
                "ajax": {
                    "url": "<?php echo base_url('pengaturan/getTableJamKerjaPegawai?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan": bulan,
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id,
                        "jenis_pegawai": jenis_pegawai
                    }

                },
                "initComplete":function( settings, json){
                    $('.jamPegawaiSelect2').select2();
                    $('#h_pegawai_id').val(pegawai_id);
                    $('#h_jenis_pegawai').val(jenis_pegawai);
                    
                    $('#body-btnsubmit').show();

                },
                "createdRow": function(row, data, dataIndex) {

                    if (parseInt(data[2])>6) {
                        $(row).find('td:eq(0)').addClass('text-danger');

                    }
                    if (data[3]!=null) {
                        if (data[4] == 'yes') {
                            $(row).find('td:eq(0)').addClass('text-info');
                        } else if (data[4] == null) {
                            $(row).find('td:eq(0)').addClass('text-warning');
                        }
                    }
                }
            });
        }

    });
</script>