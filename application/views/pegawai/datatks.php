<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row" style="padding">
                    <div class="col-md-2">Nama Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">-- Pilih Unit Kerja --</option>
                                <?php foreach ($skpd as $s) { 
                                    $s['id_skpd'] = isset($s['skpd_id']) ? $s['skpd_id'] : $s['id_skpd'];
                                ?>
                                    <option value="<?= $s['id_skpd']; ?>" <?=$this->session->userdata('role_id')==3 && $this->session->userdata('skpd_id')==$s['id_skpd'] ? "selected" :null;?>><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="padding">
                    <div class="col-md-12">
                        <button id="filter" class="btn btn-primary btn-sm">Filter</button>
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
                                <!--<th width="20">No</th>-->
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>OPD</th>
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
        
        // $("#skpd_id").change(function() {
        //     getPegawai();
        // });

        // function getPegawai(){
        //     $('#pegawai_id').find('option').remove().end();
        //     var skpd_id = $("#skpd_id").val();
        //     $.ajax({
        //         type: "post",
        //         url: "<?= base_url() . '/json/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
        //         data: "skpd_id=" + skpd_id,
        //         success: function(data) {
        //             $("#pegawai_id").html(data);
        //         }
        //     });

        // }
        function getFilter() {
            var pegawai_id = 0;
            var skpd_id = $("#skpd_id").val();
            $('#tablePegawai').DataTable().destroy();
            $('#tablePegawai').DataTable({
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('json/getDataTks?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "skpd_id": skpd_id,
                        "pegawai_id": pegawai_id
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

