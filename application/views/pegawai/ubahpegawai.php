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
                            <?php 
                            foreach ($skpd as $o) : 
                            $o['skpd_id'] = isset($o['id_skpd']) ? $o['id_skpd'] : $o['skpd_id'];
                            ?>
                                <option value="<?= $o['skpd_id']; ?>" <?= $pegawai['skpd_id'] == $o['skpd_id'] ? "selected" : null ?>><?= $o['nama_skpd']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('opd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="opd_id">Organisasi Perangkat Daerah (OPD)</label>
                        <select id="opd_id" name="opd_id" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih OPD --</option>
                            <?php foreach ($skpd as $o) : ?>
                            <?php 
                                $nama_skpd = explode(" ", $o['nama_skpd']);
                                $o['skpd_id'] = isset($o['id_skpd']) ? $o['id_skpd'] : $o['skpd_id'];
                                if(
                                    $o['nama_skpd']=='Satuan Polisi Pamong Praja' || 
                                    $nama_skpd[0]=='Dinas' ||
                                    $nama_skpd[0]=='Badan' ||
                                    $nama_skpd[0]=='Sekretariat' ||
                                    $nama_skpd[0]=='Kecamatan' ||
                                    $nama_skpd[0]=='Inspektorat'
                                ){}else{continue;}
                            ?>
                                <option value="<?= $o['skpd_id']; ?>" <?= $pegawaiMeta['opd_id'] == $o['skpd_id'] ? "selected" : null ?>><?= $o['nama_skpd']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('opd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="jabatan_golongan">Pangkat/Golongan</label>
                        <select id="jabatan_golongan" name="jabatan_golongan" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih Satu --</option>
                            <?php foreach ($jabatangolongan as $jg) { ?>
                                <option value="<?= $jg['id']; ?>" <?= $pegawaiMeta['jabatan_golongan'] == $jg['id'] ? "selected" : null ?>><?= $jg['nama_golongan']; ?></option>
                            <?php } ?>
                        </select>
                        <?= form_error('jabatan_golongan', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>


                    <div class="form-group">
                        <label for="jabatan_opd">Jabatan</label>
                        <input id="jabatan_opd" class="form-control" type="text" name="jabatan_opd" value="<?= $pegawaiMeta['jabatan_opd']; ?>" placeholder="Masukkan Jabatan Pada OPD">
                        <?= form_error('jabatan_opd', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group  form-nonplt">
                        <label for="jabatan_perbub_tpp">Jabatan Defenitif</label>
                        <select id="jabatan_perbub_tpp" name="jabatan_perbub_tpp" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih Jabatan --</option>
                            <?php foreach ($jabatanpenghasilan as $jpt) { ?>
                                <option value="<?= $jpt['id']; ?>" <?= $pegawaiMeta['jabatan_perbub_tpp'] == $jpt['id'] ? "selected" : null ?>><?= $jpt['nama_jabatan']; ?></option>
                            <?php } ?>
                        </select>
                        <?= form_error('jabatan_perbub_tpp', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <!--<div class="alert alert-warning mb-4" role="alert">-->
                    <!--   Atasan langsung adalah pegawai yang akan memberikan persetujuan izin,sakit,dinas luar dan absen manual. -->
                    <!--</div>-->

                    <div class="form-group">
                        <label for="guru_sertifikasi">Apakah Pegawai ini guru bersertifikasi?</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="guruSertifikasiYa" type="radio" name="guru_sertifikasi" value="1" <?= $pegawaiMeta['guru_sertifikasi'] == "Ya" ? "checked" : null; ?>>
                                <label for="guruSertifikasiYa">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="guruSertifikasiTidak" type="radio" name="guru_sertifikasi" value="0" <?= $pegawaiMeta['guru_sertifikasi'] == null ? "checked" : null; ?>>
                                <label for="guruSertifikasiTidak">Tidak</label>
                            </div>
                        </div>

                        <?= form_error('cpns', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="cpns">Apakah Pegawai ini CPNS?</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="cpnsYa" class="cpnsYa" type="radio" name="cpns" value="1" <?= $pegawaiMeta['cpns'] == 1 ? "checked" : null; ?>>
                                <label for="cpnsYa">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="cpnsTidak" class="cpnsTidak" type="radio" name="cpns" value="0" <?= $pegawaiMeta['cpns'] == null ? "checked" : null; ?>>
                                <label for="cpnsTidak">Tidak</label>
                            </div>
                        </div>

                        <?= form_error('cpns', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
             

                    <div class="form-group">
                        <label>Apakah pegawai ini menduduki jabatan PLT?</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="pltYa" class="pltYa" type="radio" name="plt" value="1" <?= $pegawaiMeta['plt'] == 1 ? "checked" : null; ?>>
                                <label for="pltYa">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="pltTidak" class="pltTidak" type="radio" name="plt" value="0" <?= $pegawaiMeta['plt'] == null ? "checked" : null; ?>>
                                <label for="pltTidak">Tidak</label>
                            </div>
                        </div>
                        <?= form_error('plt', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group  form-plt">
                        <label for="jabatan_rangkap_perbub">Jabatan PLT sesuai Perbup No 10 Tahun 2020</label>
                        <select id="jabatan_rangkap_perbub" name="jabatan_rangkap_perbub" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih Satu --</option>
                            <?php foreach ($jabatanpenghasilan as $jpt) { ?>
                                <option value="<?= $jpt['id']; ?>" <?= $pegawaiMeta['jabatan_rangkap_perbub'] == $jpt['id'] ? "selected" : null ?>><?= $jpt['nama_jabatan']; ?></option>
                            <?php } ?>
                        </select>
                        <?= form_error('jabatan_rangkap_perbub', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <!--<div class="alert alert-warning mb-0" role="alert">-->
                    <!--    <h5 class="alert-heading"><em class="fa fa-exclamation-triangle"></em> Ketentuan PLT tertera di Pasal 37 Pada Perbup No 10 Tahun 2020, Yaitu:</h5>-->
                    <!--</div>-->
                    <!--<div class="alert alert-warning" role="alert">-->
                    <!--    <ol>-->
                    <!--        <li>Pelaksana Tugas diberikan TPP tambahan, yang menjabat dalam jangka waktu paling singkat 1 (satu) bulan kalender.</li>-->
                    <!--        <li>Ketentuan mengenai TPP tambahan sebagaimana dimaksud pada ayat (1) adalah sebagai berikut:</li>-->
                    <!--        <ol type="a">-->
                    <!--            <li>Pejabat atasan langsung atau atasan tidak langsung yang merangkap sebagai Pelaksana Tugas menerima TPP tambahan, ditambah sebesar 20% (dua puluh persen) dari TPP dalam Jabatan sebagai Pelaksana Tugas pada Jabatan yang dirangkapnya.</li>-->
                    <!--            <li>pejabat setingkat yang merangkap Pelaksana Tugas jabatan lain menerima TPP yang lebih tinggi, ditambah sebesar 50% (lima puluh persen) dari TPP jabatan yang dirangkapnya.</li>-->
                    <!--            <li>Pejabat satu tingkat di bawah pejabat definitif yang berhalangan tetap atau berhalangan sementara yang merangkap sebagai Pelaksana Tugas, hanya menerima TPP pada Jabatan TPP yang tertinggi.</li>-->
                    <!--        </ol>-->
                    <!--    </ol>-->
                    <!--</div>-->
                    <?php if($this->session->userdata('role_id')==1):?>
                    <div class="form-group mt-4">
                        <label>Absensi Dengan Kordinat Bebas ?</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_bebas_ya" class="kordinat_bebas_ya" type="radio" name="kordinat_bebas" value="Ya" <?= $pegawaiMeta['kordinat_bebas'] == 'Ya' ? "checked" : null; ?>>
                                <label for="kordinat_bebas_ya">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_bebas_tidak" class="kordinat_bebas_tidak" type="radio" name="kordinat_bebas" value="Tidak" <?= !$pegawaiMeta['kordinat_bebas'] || $pegawaiMeta['kordinat_bebas'] == 'Tidak' ? "checked" : null; ?>>
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
                                <input id="kordinat_khusus_ya" class="kordinat_khusus_ya" type="radio" name="kordinat_khusus" value="Ya" <?= $pegawaiMeta['kordinat_khusus'] == 'Ya' ||  set_value('kordinat_khusus')=="Ya" ? "checked" : null; ?>>
                                <label for="kordinat_khusus_ya">Ya</label>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 col-lg-2">
                                <input id="kordinat_khusus_tidak" class="kordinat_khusus_tidak" type="radio" name="kordinat_khusus" value="Tidak" <?= set_value('kordinat_khusus') && set_value('kordinat_khusus')=="Tidak" ? null : (!set_value('kordinat_khusus') && (!$pegawaiMeta['kordinat_khusus'] || $pegawaiMeta['kordinat_khusus'] == 'Tidak') ? "checked" : null); ?>>
                                <label for="kordinat_khusus_tidak">Tidak</label>
                            </div>
                        </div>
                        <div class="form-group" id="body_kordinat_khusus">
                            <select id="kordinats" name="kordinats[]" class="form-control select2Kordinats" multiple="multiple" style="width: 100%;">
                                <?php 
                                foreach ($kordinats as $k) : 
                                    $inKordinats = unserialize($pegawaiMeta['kordinats']);
                                ?>
                                    <option value="<?= $k['id']; ?>" <?= $inKordinats && in_array($k['id'], $inKordinats) ? "selected" : null ?>><?= $k['nama_kordinat']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('kordinats', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                        <?= form_error('kordinat_khusus', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <?php endif;?>

               
                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pegawai?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
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
        
        $('.pltYa').on('click', function() {
            $('.form-plt').show();
            $('.form-nonplt').show();
        });
        
        $('.pltTidak').on('click', function() {
            $('.form-plt').hide();
            $('.form-nonplt').show();
        });
        
        <?php if ($pegawaiMeta['plt'] == 1) { ?>
            $('.form-nonplt').show();
            $('.form-plt').show();
        <?php } else { ?>
            $('.form-nonplt').show();
            $('.form-plt').hide();
        <?php } ?>
    });
</script>