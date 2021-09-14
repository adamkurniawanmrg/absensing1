<style>
a:hover { 
    text-decoration:none; 
}
</style>
<!-- The Modal --> 
<div class="modal fade" id="modalAbsensi">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Pilih Kategori Absensi</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
            <div class="accordion mt-3" id="menuPegawai" role="tablist">
              <?php foreach($kategori as $cat): ?>
              <div class="card">
                <div class="card-header" role="tab" id="headIzin">
                  <h6 class="mb-0">
                    <a href="<?=base_url('absen/wajah?token='.$_GET['token'].'&kategori='.$cat.'&is_manual=false');?>" target="_blank">
                      <?=$cat?>
                    </a>
                  </h6>
                </div>
              </div>
              <?php endforeach ?>
            </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="modalAbsensiManual">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Pilih Kategori Absensi</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
            <div class="accordion mt-3" id="menuPegawai" role="tablist">
              <?php foreach($kategori as $cat): ?>
              <div class="card">
                <div class="card-header" role="tab" id="headIzin">
                  <h6 class="mb-0">
                    <a href="<?=base_url('absen/wajah?token='.$_GET['token'].'&kategori='.$cat.'&is_manual=true');?>" target="_blank">
                      <?=$cat?>
                    </a>
                  </h6>
                </div>
              </div>
              <?php endforeach ?>
              <div class="card">
                <div class="card-header" role="tab" id="headIzin">
                  <h6 class="mb-0">
                    <a href="<?=base_url('absen/wajah?token='.$_GET['token'].'&kategori=Absen Upacara&is_manual=true');?>" target="_blank">
                      Absen Upacara
                    </a>
                  </h6>
                </div>
              </div>
              <div class="card">
                <div class="card-header" role="tab" id="headIzin">
                  <h6 class="mb-0">
                    <a href="<?=base_url('absen/wajah?token='.$_GET['token'].'&kategori=Absen Senam&is_manual=true');?>" target="_blank">
                      Absen Senam
                    </a>
                  </h6>
                </div>
              </div>

            </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=SITENAME();?></h3>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
               <div class="row">
                   <div class="col-md-12">
                       Hai <strong><?=$this->session->userdata('nama');?></strong>. Selamat Datang di <?=SITENAME()?> Kab. Labuhanbatu Utara
                   </div>
               </div>
                   <?php
                   
                        // if($this->session->userdata('user_id') == 1 && $this->session->userdata('jenis_pegawai') == "tks"){
                        //     echo "<pre>";
                        //     print_r($_SESSION);
                        //     echo "</pre>";
                        // }
                   
                   ?>
            </li>
        </ul>
    </div>

    <div class="row mt-3">
        <div class="col-6 col-md-6">
          <div class="card border-0 pt-3 pb-3">
            <a href="javascript:void(0)" class="stretched-link text-secondary" data-toggle="modal" data-target="#modalAbsensi">
            <div class="card-body text-center">
                <img src="assets/img/icon/face-recog.png" width="80" height="80"><br>
                <h4 class="btn btn-sm btn-primary mb-0 order-xl-0" style="padding:5px 7px"><em class="ti-angle-double-right"></em> Absen Sekarang</h4>
              <!--<p class="mb-0 mt-2 text-warning">Absensi Masuk Kerja</p>-->
            </div>
            </a>
          </div>
        </div>
        <div class="col-6 col-md-6">
          <div class="card border-0 pt-3 pb-3">
            <a href="<?=base_url('logabsen?token='.$_GET['token']);?>" class="stretched-link text-secondary">
            <div class="card-body text-center">
                <img src="assets/img/icon/icon-mobile.png" class="mb-2" width="80" height="70"><br>
                <h4 class="btn btn-sm btn-warning mb-0 order-xl-0" style="padding:5px 7px"><em class="ti-search"></em> Lihat Log Absen</h4>
            </div>
            </a>
          </div>
        </div>        
    </div>

    <div class="accordion mt-3" id="ACD" role="tablist">
      <div class="card">
        <div class="card-header" role="tab" id="headIzin">
          <h6 class="mb-0">
            <a data-toggle="collapse" href="#izin" aria-expanded="false" aria-controls="izin">
              IZIN KERJA <?=$jumlahAntrianIzinKerja>0 ? "(".$jumlahAntrianIzinKerja.")" : null;?>
            </a>
          </h6>
        </div>
        <div id="izin" class="collapse" role="tabpanel" aria-labelledby="izin" data-parent="#ACD">
            <ul class="list-group list-group-flush">
                <a href="<?=base_url('izinkerja/addizin?token='.$_GET['token']);?>">
                    <li class="list-group-item btn-warning">
                        Buat Baru
                        <div>
                            <small>Klik disini untuk mengajukan data izin kepada atasan langsung.</small>
                        </div>
                    </li>
                </a>
                <a href="<?=base_url('izinkerja/dataizinkerja?token='.$_GET['token']);?>">
                    <li class="list-group-item btn-warning">
                        Permohonan Saya
                        <div>
                            <small>Klik disini untuk melihat status permohonan Anda kepada atasan.</small>
                        </div>
                    </li>
                </a>
                <a href="<?=base_url('verifikasi/izinkerja?token='.$_GET['token']);?>">
                    <li class="list-group-item btn-warning">
                        Permohonan Bawahan
                        <div>
                            <small>Klik disini untuk verifikasi permohonan bawahan Anda kepada Anda.</small>
                        </div>

                    </li>
                </a>
            </ul>
        </div>
      </div>
      <div class="card">
        <div class="card-header" role="tab" id="headAbsenManual">
          <h6 class="mb-0">
            <a data-toggle="collapse" href="#absenmanual" aria-expanded="false" aria-controls="absenmanual">
              ABSEN MANUAL <?=$jumlahAntrianAbsenManual>0 ? "(".$jumlahAntrianAbsenManual.")" : null;?>
            </a>
          </h6>
        </div>
        <div id="absenmanual" class="collapse" role="tabpanel" aria-labelledby="absenmanual" data-parent="#ACD">
            <ul class="list-group list-group-flush">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalAbsensiManual">
                    <li class="list-group-item btn-warning">
                        Buat Baru
                        <div>
                            <small>Klik disini untuk mengajukan absen manual kepada atasan langsung.</small>
                        </div>
                    </li>
                </a>
                <a href="<?=base_url('absenmanual?token='.$_GET['token']);?>">
                    <li class="list-group-item btn-warning">
                        Permohonan Saya
                        <div>
                            <small>Klik disini untuk melihat status permohonan Anda kepada atasan.</small>
                        </div>

                    </li>
                </a>
                <a href="<?=base_url('verifikasi/absenmanual?token='.$_GET['token']);?>">
                    <li class="list-group-item btn-warning">
                        Permohonan Bawahan
                        <div>
                            <small>Klik disini untuk verifikasi permohonan bawahan Anda kepada Anda.</small>
                        </div>

                    </li>
                </a>
            </ul>
        </div>
      </div>
      <!--<div class="card">-->
      <!--  <div class="card-header" role="tab" id="headAbsenUpacara">-->
      <!--    <h6 class="mb-0">-->
      <!--      <a data-toggle="collapse" href="#absenupacara" aria-expanded="false" aria-controls="absenupacara">-->
      <!--        ABSEN UPACARA-->
      <!--      </a>-->
      <!--    </h6>-->
      <!--  </div>-->
      <!--  <div id="absenupacara" class="collapse" role="tabpanel" aria-labelledby="absenupacara" data-parent="#menuPegawai">-->
      <!--      <ul class="list-group list-group-flush">-->
      <!--          <a href="<?=base_url('absenmanual/addmanual?token='.$_GET['token']);?>">-->
      <!--              <li class="list-group-item btn-warning">-->
      <!--                  Buat Baru-->
      <!--                  <div>-->
      <!--                      <small>Klik disini untuk mengajukan absen upacara kepada atasan langsung.</small>-->
      <!--                  </div>-->

      <!--              </li>-->
      <!--          </a>-->
      <!--          <a href="<?=base_url('absenmanual?token='.$_GET['token']);?>">-->
      <!--              <li class="list-group-item btn-warning">-->
      <!--                  Permohonan Saya-->
      <!--                  <div>-->
      <!--                      <small>Klik disini untuk melihat status permohonan Anda kepada atasan.</small>-->
      <!--                  </div>-->
      <!--              </li>-->
      <!--          </a>-->
      <!--          <a href="<?=base_url('verifikasi/absenmanual?token='.$_GET['token']);?>">-->
      <!--              <li class="list-group-item btn-warning">-->
      <!--                  Permohonan Bawahan-->
      <!--                  <div>-->
      <!--                      <small>Klik disini untuk verifikasi permohonan bawahan Anda kepada Anda.</small>-->
      <!--                  </div>-->

      <!--              </li>-->
      <!--          </a>-->
      <!--      </ul>-->
      <!--  </div>-->
      <!--</div>-->
      <!--<div class="card">-->
      <!--  <div class="card-header" role="tab" id="headAbsenSenam">-->
      <!--    <h6 class="mb-0">-->
      <!--      <a data-toggle="collapse" href="#absensenam" aria-expanded="false" aria-controls="absensenam">-->
      <!--        ABSEN SENAM-->
      <!--      </a>-->
      <!--    </h6>-->
      <!--  </div>-->
      <!--      <div id="absensenam" class="collapse" role="tabpanel" aria-labelledby="absensenam" data-parent="#menuPegawai">-->
      <!--      <ul class="list-group list-group-flush">-->
      <!--          <a href="<?=base_url('absenmanual/addmanual?token='.$_GET['token']);?>">-->
      <!--              <li class="list-group-item btn-warning">-->
      <!--                  Buat Baru-->
      <!--                  <div>-->
      <!--                      <small>Klik disini untuk mengajukan absen senam kepada atasan langsung.</small>-->
      <!--                  </div>-->

      <!--              </li>-->
      <!--          </a>-->
      <!--          <a href="<?=base_url('absenmanual?token='.$_GET['token']);?>">-->
      <!--              <li class="list-group-item btn-warning">-->
      <!--                  Permohonan Saya-->
      <!--                  <div>-->
      <!--                      <small>Klik disini untuk melihat status permohonan Anda kepada atasan.</small>-->
      <!--                  </div>-->

      <!--              </li>-->
      <!--          </a>-->
      <!--          <a href="<?=base_url('verifikasi/absenmanual?token='.$_GET['token']);?>">-->
      <!--              <li class="list-group-item btn-warning">-->
      <!--                  Permohonan Bawahan-->
      <!--                  <div>-->
      <!--                      <small>Klik disini untuk verifikasi permohonan bawahan Anda kepada Anda.</small>-->
      <!--                  </div>-->

      <!--              </li>-->
      <!--          </a>-->
      <!--      </ul>-->
      <!--  </div>-->
      <!--</div>-->
    
    </div>

</div>


<?php $this->view('template/javascript'); ?>
