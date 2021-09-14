<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row" style="padding">
                    <div class="col-md-3">Unit Kerja</div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <?php if($this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==2){?> 
                                        <option value="">-- Pilih Unit Kerja --</option>
                                <?php } ?>
                                <?php foreach ($skpd as $s) { 
                                    if($this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==2){
                                        $s['skpd_id'] = $s['id_skpd'];
                                    }
                                ?>
                                    <option value="<?= $s['skpd_id']; ?>" <?=$this->session->userdata('role_id')==3 && $this->session->userdata('skpd_id')==$s['skpd_id'] ? "selected" :null;?>><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="padding">
                    <div class="col-md-12">
                        <button id="filter" class="btn btn-primary btn-sm" <?=count($skpd)==0? "disabled":null;?>>Filter</button>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="mb-2" id="faceSaved"></div>
                <div class="mb-2" id="faceUnsaved"></div>
            </li>
            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                <?= $this->session->flashdata('pesan'); ?>

                     <table class="table table-striped" id="tablePegawai" cellspacing="0">
                        <thead>
                            <tr>
                                <!--<th>No</th>-->
                                <th>Nama</th>
                                <th>NIP</th>
                                <!--<th>Pangkat/Golongan</th>-->
                                <th>OPD/Unit Kerja</th>
                                <!--<th>Jabatan Pada OPD</th>-->
                                <!--<th>Jabatan Pada Perbub TPP</th>-->
                                <!--<th>Jabatan PLT</th>-->
                                <th>Aksi</th>
                            </tr>
                        </thead>
                     </table>
                </div>
              </div>
            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>
<script>
    $(document).ready(function() {
        $("#filter").click(function() {
            getFilter();
        });
        
        getFilter();

        function getFilter() {
            var pegawai_id = $("#pegawai_id").val();
            var skpd_id = $("#skpd_id").val();
            $('#tablePegawai').DataTable().destroy();
            $('#tablePegawai').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('json/getDataPegawai?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "skpd_id": skpd_id,
                    }
                },
                "pageLength" :25,
                "initComplete": function( settings, json ) {
                    $('#faceSaved').html(null)
                    $('#faceUnsaved').html(null)
                    if(json.totalSaved && json.totalSaved!=0) {
                        $('#faceSaved').html("<strong>"+json.totalSaved+"</strong> Data pegawai yang sudah terekam.")
                    }
                    if(json.totalUnsaved && json.totalUnsaved!=0){
                        $('#faceUnsaved').html("<strong>"+json.totalUnsaved+"</strong> Data pegawai yang belum terekam.");
                    }
                    $('#tablePegawai_wrapper .row .col-sm-12').addClass('table-responsive');

                }
            });
        }

    });
</script>

