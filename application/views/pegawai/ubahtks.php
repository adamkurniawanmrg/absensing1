<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group">
                        <label for="nama">Nama Pegawai</label>
                        <input id="nama" class="form-control" type="text" name="nama" value="<?= $pegawai['nama']; ?>" disabled>
                        <?= form_error('nama', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="nip">NIP</label>
                        <input id="nip" class="form-control" type="text" name="nip" value="<?= $pegawai['username']; ?>" disabled>
                        <?= form_error('nip', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="skpd_id">Unit Kerja</label>
                        <select id="skpd_id" name="skpd" class="form-control select2" style="width: 100%;" disabled>
                            <option value="">-- Pilih SKPD --</option>
                            <?php foreach ($skpd as $o) : ?>
                                <option value="<?= $o['id_skpd']; ?>" <?= $pegawai['skpd_id'] == $o['id_skpd'] ? "selected" : null ?>><?= $o['nama_skpd']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('skpd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>


                    <div class="form-group">
                        <label for="opd_id">Organisasi Perangkat Daerah (OPD)</label>
                        <select id="opd_id" name="opd_id" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih OPD --</option>
                            <?php foreach ($skpd as $o) : ?>
                            <?php 
                                $nama_skpd = explode(" ", $o['nama_skpd']);
                                if(
                                    $o['nama_skpd']=='Satuan Polisi Pamong Praja' || 
                                    $nama_skpd[0]=='Dinas' ||
                                    $nama_skpd[0]=='Badan' ||
                                    $nama_skpd[0]=='Sekretariat' ||
                                    $nama_skpd[0]=='Kecamatan' ||
                                    $nama_skpd[0]=='Inspektorat'
                                ){}else{continue;}
                            ?>
                                <option value="<?= $o['id_skpd']; ?>" <?= $tksMeta['opd_id'] == $o['id_skpd'] ? "selected" : null ?>><?= $o['nama_skpd']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('opd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="gaji">Gaji</label>
                        <input id="gaji" class="form-control" type="number" name="gaji" value="<?= $tksMeta['gaji']; ?>" placeholder="Gaji">
                        <?= form_error('gaji', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                </li>
                <li class="list-group-item">
                    <?php if($this->session->userdata('role_id')==1):?>
                    <div class="form-group mt-4">
                        <label>Kordinat Bebas ?</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_bebas_ya" class="kordinat_bebas_ya" type="radio" name="kordinat_bebas" value="Ya" <?= $tksMeta['kordinat_bebas'] == 'Ya' ? "checked" : null; ?>>
                                <label for="kordinat_bebas_ya">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_bebas_tidak" class="kordinat_bebas_tidak" type="radio" name="kordinat_bebas" value="Tidak" <?= !$tksMeta['kordinat_bebas'] || $tksMeta['kordinat_bebas'] == 'Tidak' ? "checked" : null; ?>>
                                <label for="kordinat_bebas_tidak">Tidak</label>
                            </div>
                        </div>
                        <?= form_error('kordinat_bebas', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <?php endif;?>
                    <?php if($this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==3):?>
                    <div class="form-group mt-4">
                        <label>Absensi Dengan Kordinat Khusus ?</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_khusus_ya" class="kordinat_khusus_ya" type="radio" name="kordinat_khusus" value="Ya" <?= $tksMeta['kordinat_khusus'] == 'Ya' ||  set_value('kordinat_khusus')=="Ya" ? "checked" : null; ?>>
                                <label for="kordinat_khusus_ya">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_khusus_tidak" class="kordinat_khusus_tidak" type="radio" name="kordinat_khusus" value="Tidak" <?= set_value('kordinat_khusus') && set_value('kordinat_khusus')=="Tidak" ? null : (!set_value('kordinat_khusus') && (!$tksMeta['kordinat_khusus'] || $tksMeta['kordinat_khusus'] == 'Tidak') ? "checked" : null); ?>>
                                <label for="kordinat_khusus_tidak">Tidak</label>
                            </div>
                        </div>
                        <div class="form-group" id="body_kordinat_khusus">
                            <select id="kordinats" name="kordinats[]" class="form-control select2Kordinats" multiple="multiple" style="width: 100%;">
                                <?php 
                                foreach ($kordinats as $k) : 
                                    $inKordinats = unserialize($tksMeta['kordinats']);
                                ?>
                                    <option value="<?= $k['id']; ?>" <?= $inKordinats && in_array($k['id'], $inKordinats) ? "selected" : null ?>><?= $k['nama_kordinat']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('kordinats', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <?= form_error('kordinat_khusus', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <?php endif;?>
                </li>


                <li class="list-group-item">
                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pegawai/tks?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </ul>
        </form>
    </ul>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>
<script>
    $(document).ready(function() {
        cekKordinatKhusus()
        $(".select2Kordinats").select2({
            placeholder: "Pilih Kordinat",
            theme: 'bootstrap4'
        });
        $("input[type='radio'][name='kordinat_khusus']").click(function() {
            cekKordinatKhusus()
        });
        
        function cekKordinatKhusus(){
            if($("input[type='radio'][name='kordinat_khusus']:checked").val() == "Ya"){
                $('#body_kordinat_khusus').show()
            }else{
                $('#body_kordinat_khusus').hide()
            }
            
        }
    });
</script>