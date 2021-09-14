<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <div class="col-6 mt-3">
            
        <a href="<?= base_url("upacara/addupacara?token=".$_GET['token']) ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
           Tambah Upacara & Libur
        </a>
    
        </div>
        <hr>
        
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                 <?= $this->session->flashdata('pesan'); ?>
                    <table id="tableUpacara" class="table table-striped">
                        
                      <thead>
                        <tr>
                            <!--<th>#</th>-->
                            <th>Nama Hari</th>
                            <th>Tanggal</th>
                            <th>Kategori <small>(Libur/Upacara)</small></th>
                           
                            <th align="right">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $no = 1;
                        foreach ($upacara as $up) : ?>
                            <tr>
                                 <!--<td scope="row"><?= $no; ?></td>-->
                                <td><?= $up['nama_hari']; ?></td>
                                <td><?= date('d F Y', strtotime($up['tanggal'])); ?></td>
                                <td><?= $up['kategori']; ?></td>
                                <td align="right">
                                    <a href="<?= base_url('upacara/editupacara/') . $up['id'] . '?token=' . $_GET['token'] ?>" class="btn btn-success btn-sm" style="padding: 5px 15px">Ubah</a>
                                    <a href="<?= base_url('upacara/deleteupacara/') . $up['id']. '?token=' . $_GET['token'] ?>" class="btn btn-danger"  style="padding: 5px 15px" title="Delete" onclick="javascript: return confirm('Anda yakin hapus ?')">Hapus</a>
                                </td>
                            </tr>
                        <?php $no++ ?>
                        <?php endforeach;
                        ?>
                      </tbody>
                    </table>
                </div>
              </div>
            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>


