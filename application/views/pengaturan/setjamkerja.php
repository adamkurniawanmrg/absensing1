<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?><span id="subtitle"></span></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post">
                <li class="list-group-item">
                    <div class="form-group mb-0">
                        <label>Nama Jam Kerja</label>
                        <input name="nama_jam_kerja" type="text" class="form-control" value="<?=isset($jamkerja['nama_jam_kerja']) ? $jamkerja['nama_jam_kerja'] : null;?>" placeholder="Jam Kerja" />
                        <?= form_error('nama_jam_kerja', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                </li>
                <li class="list-group-item">
                    <div id="formJam">
                        <?php $no=1;foreach($jamkerjaMetas as $jkm):?>
                        <fieldset class="fieldsetJam border p-3" style="border-style: dashed;">
                            <legend class="legendTitleJam w-auto px-2"><?=$no; $no++;?></legend>
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari[]" class="form-control select2">
                                    <option value="0">Seluruh Hari</option>
                                    <option value="1" <?=$jkm['hari']==1 ? "selected" : null;?>>Senin</option>
                                    <option value="2" <?=$jkm['hari']==2 ? "selected" : null;?>>Selasa</option>
                                    <option value="3" <?=$jkm['hari']==3 ? "selected" : null;?>>Rabu</option>
                                    <option value="4" <?=$jkm['hari']==4 ? "selected" : null;?>>Kamis</option>
                                    <option value="5" <?=$jkm['hari']==5 ? "selected" : null;?>>Jumat</option>
                                    <option value="6" <?=$jkm['hari']==6 ? "selected" : null;?>>Sabtu</option>
                                </select>
                            </div>
    
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Masuk</label>
                                    <input name="jam_awal_masuk[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_awal_masuk'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Masuk</label>
                                    <input name="jam_akhir_masuk[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_akhir_masuk'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Pulang</label>
                                    <input name="jam_awal_pulang[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_awal_pulang'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Pulang</label>
                                    <input name="jam_akhir_pulang[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_akhir_pulang'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Istirathat</label>
                                    <input name="jam_awal_istirahat[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_awal_istirahat'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Istirahat</label>
                                    <input name="jam_akhir_istirahat[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_akhir_istirahat'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Selesai Istirahat</label>
                                    <input name="jam_awal_selesai_istirahat[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_awal_selesai_istirahat'];?>" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Selesai Istirahat</label>
                                    <input name="jam_akhir_selesai_istirahat[]" type="time" class="form-control" autocomplete="off" value="<?=$jkm['jam_akhir_selesai_istirahat'];?>" />
                                </div>
                            </div>
                        </fieldset>
                        <?php endforeach;?>
                        <?php if(count($jamkerjaMetas)<0 || !$jamkerjaMetas):?> 
                        <fieldset class="fieldsetJam border p-3" style="border-style: dashed;">
                            <legend class="legendTitleJam w-auto px-2"><?=count($jamkerjaMetas)+1;?></legend>
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari[]" class="form-control select2">
                                    <option value="0">Seluruh Hari</option>
                                    <option value="1">Senin</option>
                                    <option value="2">Selasa</option>
                                    <option value="3">Rabu</option>
                                    <option value="4">Kamis</option>
                                    <option value="5">Jumat</option>
                                    <option value="6">Sabtu</option>
                                </select>
                            </div>
    
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Masuk</label>
                                    <input name="jam_awal_masuk[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Masuk</label>
                                    <input name="jam_akhir_masuk[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Pulang</label>
                                    <input name="jam_awal_pulang[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Pulang</label>
                                    <input name="jam_akhir_pulang[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Istirathat</label>
                                    <input name="jam_awal_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Istirahat</label>
                                    <input name="jam_akhir_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Awal Selesai Istirahat</label>
                                    <input name="jam_awal_selesai_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Jam Akhir Selesai Istirahat</label>
                                    <input name="jam_akhir_selesai_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />
                                </div>
                            </div>
                        </fieldset>
                        <?php endif;?>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" id="btnKurangField" class="btn btn-sm btn-danger"><em class="ti-minus"></em></button>
                            <button type="button" id="btnTambahField" class="btn btn-sm btn-success"><em class="ti-plus"></em></button>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <button type="submit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                    <a href="<?=base_url('pengaturan/jamkerja?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>

<script>
    $(document).ready(function(){
    
    var start   = $('.fieldsetJam').length+1; 
    buttonVisibility();

    $('#btnTambahField').click(function(){
        var formNow = $('#formJam').html();
        $('#formJam').append(elementForm(start));
        start = start+1;
        buttonVisibility();        
    });
    $('#btnKurangField').click(function(){
        var formNow = $('#formJam').html();
        $('#formJam .fieldsetJam:last').remove();
        start = start-1;
        buttonVisibility();
    });
    
    $('#skpd_id').change(function(){
        $('#subtitle').html(" Unit Kerja "+($("#skpd_id option:selected").text()));
    });

    function buttonVisibility(){
        if(start>6){
            $('#btnTambahField').prop('disabled', true);
        }else{
            $('#btnTambahField').removeAttr('disabled');
        }
        if(start<=2){                        <?= form_error('latitude', '<small class="text-danger pl-2">', '</small>'); ?>
            $('#btnKurangField').prop('disabled', true);
        }else{
            $('#btnKurangField').removeAttr('disabled');
        }
    }

    function elementForm(no){
        return      '<fieldset class="fieldsetJam border p-3" style="border-style: dashed;">'
                    +'    <legend class="legendTitleJam w-auto px-2">'+no+'</legend>'
                    +'    <div class="form-group">'
                    +'        <label>Hari</label>'
                    +'        <select name="hari[]" class="form-control select2">'
                    +'            <option value="0">Seluruh Hari</option>'
                    +'            <option value="1">Senin</option>'
                    +'            <option value="2">Selasa</option>'
                    +'            <option value="3">Rabu</option>'
                    +'            <option value="4">Kamis</option>'
                    +'            <option value="5">Jumat</option>'
                    +'            <option value="6">Sabtu</option>'
                    +'        </select>'
                    +'    </div>'
                    +'    <div class="row">'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Awal Masuk</label>'
                    +'            <input name="jam_awal_masuk[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Akhir Masuk</label>'
                    +'            <input name="jam_akhir_masuk[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Awal Pulang</label>'
                    +'            <input name="jam_awal_pulang[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Akhir Pulang</label>'
                    +'            <input name="jam_akhir_pulang[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Awal Istirathat</label>'
                    +'            <input name="jam_awal_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Akhir Istirahat</label>'
                    +'            <input name="jam_akhir_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Awal Selesai Istirahat</label>'
                    +'            <input name="jam_awal_selesai_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'        <div class="form-group col-md-3">'
                    +'            <label>Jam Akhir Selesai Istirahat</label>'
                    +'            <input name="jam_akhir_selesai_istirahat[]" type="time" class="form-control" autocomplete="off" value="" />'
                    +'        </div>'
                    +'    </div>'
                    +'</fieldset>';
    }


    });
</script>