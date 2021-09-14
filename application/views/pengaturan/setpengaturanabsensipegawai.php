<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?><span id="subtitle"></span></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>TMK</label>
                            <div class="input-group">
                                <input name="TMK" type="text" class="form-control" value="<?=set_value('TMK') ? set_value('TMK') : $pengaturanabsensi['TMK'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TMK', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>TAU</label>
                            <div class="input-group">
                                <input name="TAU" type="text" class="form-control" value="<?=set_value('TAU') ? set_value('TAU') : $pengaturanabsensi['TAU'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TAU', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>TDHE1</label>
                            <div class="input-group">
                                <input name="TDHE1" type="text" class="form-control" value="<?=set_value('TDHE1') ? set_value('TDHE1') : $pengaturanabsensi['TDHE1'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TDHE1', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <label>TDHE2</label>
                            <div class="input-group">
                                <input name="TDHE2" type="text" class="form-control" value="<?=set_value('TDHE2') ? set_value('TDHE2') : $pengaturanabsensi['TDHE2'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TDHE2', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-3">
                            <label>TM1</label>
                            <div class="input-group">
                                <input name="TM1" type="text" class="form-control" value="<?=set_value('TM1') ? set_value('TM1') : $pengaturanabsensi['TM1'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM1', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-3">
                            <label>TM2</label>
                            <div class="input-group">
                                <input name="TM2" type="text" class="form-control" value="<?=set_value('TM2') ? set_value('TM2') : $pengaturanabsensi['TM2'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM2', '<small class="text-danger pl-2">', '</small>'); ?>

                        </div>
                        <div class="form-group col-md-3">
                            <label>TM3</label>
                            <div class="input-group">
                                <input name="TM3" type="text" class="form-control" value="<?=set_value('TM3') ? set_value('TM3') : $pengaturanabsensi['TM3'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM3', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>TM4</label>
                            <div class="input-group">
                                <input name="TM4" type="text" class="form-control" value="<?=set_value('TM4') ? set_value('TM4') : $pengaturanabsensi['TM4'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('TM4', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>PLA1</label>
                            <div class="input-group">
                                <input name="PLA1" type="text" class="form-control" value="<?=set_value('PLA1') ? set_value('PLA1') : $pengaturanabsensi['PLA1'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA1', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>PLA2</label>
                            <div class="input-group">
                                <input name="PLA2" type="text" class="form-control" value="<?=set_value('PLA2') ? set_value('PLA2') : $pengaturanabsensi['PLA2'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA2', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>PLA3</label>
                            <div class="input-group">
                                <input name="PLA3" type="text" class="form-control" value="<?=set_value('PLA3') ? set_value('PLA3') : $pengaturanabsensi['PLA3'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA3', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label>PLA4</label>
                            <div class="input-group">
                                <input name="PLA4" type="text" class="form-control" value="<?=set_value('PLA4') ? set_value('PLA4') : $pengaturanabsensi['PLA4'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <?= form_error('PLA4', '<small class="text-danger pl-2">', '</small>'); ?>
                        </div>

                    </div>

                </li>
                <li class="list-group-item">
                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pengaturan/absensipegawai?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>
