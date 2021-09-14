<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AbsenManual_model extends CI_Model
{
    public function __construct()
    {
        error_reporting(0);
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Sms_model']);

    }
    public function getAllAbsenManual()
    {
        return $this->db->get('tb_absen_manual')->result_array();
    }

    public function getManualById($id)
    {
        return $this->db->get_where('tb_absen_manual', ['id' => $id])->row_array();
    }

    public function getAbsenManual()
    {
        return $this->db->get('tb_absen_manual')->row_array();
    }

    public function addDataManual()
    {

        $hasil_upload    = null;
        $hasil_upload2   = null;

        $akses           = [1,2,3]; 
        $hak_akses       = in_array($this->session->userdata('role_id'), $akses);

        $skpd_id         = $hak_akses ? $this->input->post('skpd_id', true)         : $this->session->userdata('skpd_id'); 
        $jenis_pegawai   = $hak_akses ? $this->input->post('jenis_pegawai', true)   : $this->session->userdata('jenis_pegawai'); 
        $pegawai_id      = $hak_akses ? $this->input->post('pegawai_id', true)      : $this->session->userdata('user_id'); 

        if ($this->input->post('jenis_absen', true) == "AMP dan AMS") {
            $upload = $this->_upload($skpd_id);
            $upload2 = $this->_upload2($skpd_id);

            $hasil_upload = $upload[1];
            $hasil_upload2 = $upload2[1];

            if ($upload[0] == false && $upload2[0] == false) {
                return $upload2;
            } else if ($upload[0] == false) {
                return $upload;
            } else if ($upload2[0] == false) {
                return $upload2;
            }
        } else if ($this->input->post('jenis_absen', true) == "AMP") {
            $upload = $this->_upload($skpd_id);
            $hasil_upload = $upload[1];
            $hasil_upload2 = $upload[1];

            if ($upload[0] == false) {
                return $upload;
            }
        } else if ($this->input->post('jenis_absen', true) == "AMS") {
            $upload2 = $this->_upload2($skpd_id);
            $hasil_upload = $upload2[1];
            $hasil_upload2 = $upload2[1];

            if ($upload2[0] == false) {
                return $upload2;
            }
        }


        $data = [
            "skpd_id"           => $skpd_id,
            'pegawai_id'        => $pegawai_id,
            'jenis_pegawai'     => $jenis_pegawai,
            "jenis_absen"       => $this->input->post('jenis_absen', true),
            "tanggal"           => date("Y-m-d", strtotime($this->input->post('tanggal', true))),
            "lampiran_amp"      => $upload[1],
            "lampiran_ams"      => $upload2[1],
            "created_by"        => $this->session->userdata('user_id')
        ];

        $this->db->insert('tb_absen_manual', $data);
        
        $pegawais           = $this->Pegawai_model->getPegawai();
        $tkss               = $this->Pegawai_model->getPegawaiTks();
        $pegawai_meta       = $jenis_pegawai == 'pegawai' ? 
                              $this->db->where('pegawai_id', $pegawai_id)->get('tb_pegawai_meta')->row_array() :
                              $this->db->where('tks_id', $pegawai_id)->get('tb_tks_meta')->row_array();

        if(isset($pegawai_meta['pegawai_atasan'])){
            $pegawai        = $this->generatePegawai($pegawai_id, $jenis_pegawai, $pegawais, $tkss);
            $pegawai_atasan = $this->generatePegawai($pegawai_meta['pegawai_atasan'], 'pegawai', $pegawais, $tkss);
            
            $pesan          = "[ABSENSI-NG]\n\nAda Absen Manual '".$this->input->post('jenis_absen', true)."' dari ".$pegawai['nama'].".\n\nSilahkan konfirmasi : https://absensi-ng.labura.go.id";

            $this->Sms_model->send($pegawai_atasan['no_hp'], $pesan);
        }


        return array(true, "Absen Manual baru telah ditambahkan, mohon tunggu untuk konfirmasi dari atasan!");
    }
    
    private function generatePegawai($pegawai_id, $jenis_pegawai, $pegawais, $tkss){

        if($jenis_pegawai=='pegawai'){
            $indexPegawai   = array_search($pegawai_id, array_column($pegawais, 'user_id'));
            $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
            $pegawai        = (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']);
        }else{
            $indexTks       = array_search($pegawai_id, array_column($tkss, 'user_id'));
            $indexTks       = $indexTks!==false ? $indexTks : "none"; 
            $pegawai        = (isset($tkss[$indexTks]) ? $tkss[$indexTks] : ['nama'=>'undefined']);
        }
        $gelarDepan         = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
        $gelarBelakang      = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

        $pegawai['nama']    = $gelarDepan.$pegawai['nama'].$gelarBelakang;

        return $pegawai;
    }

    public function editDataManual()
    {
        $upload = $this->_upload($this->input->post('skpd_id', true));
        $upload2 = $this->_upload2($this->input->post('skpd_id', true));

        if ($_FILES["lampiran_amp"]["name"] != "") {
            $data = [
                "skpd_id"            => $this->input->post('skpd_id', true),
                'pegawai_id'        => $this->input->post('pegawai_id', true),
                "jenis_absen"       => $this->input->post('jenis_absen', true),
                "tanggal"           => date("Y-m-d", strtotime($this->input->post('tanggal', true))),
                "lampiran_amp"      => $this->_upload($this->input->post('skpd_id', true)),
                // "lampiran_ams"      => $this->_upload2($this->input->post('skpd_id', true)),
            ];
            $this->_deleteImage($this->input->post('id'));
        } elseif ($_FILES["lampiran_ams"]["name"] != "") {
            $data = [
                "skpd_id"            => $this->input->post('skpd_id', true),
                'pegawai_id'        => $this->input->post('pegawai_id', true),
                "jenis_absen"       => $this->input->post('jenis_absen', true),
                "tanggal"           => date("Y-m-d", strtotime($this->input->post('tanggal', true))),
                // "lampiran_amp"      => $this->_upload($this->input->post('skpd_id', true)),
                "lampiran_ams"      => $this->_upload2($this->input->post('skpd_id', true)),
            ];
            $this->_deleteImage($this->input->post('id'));
        } else {
            $data = [
                "skpd_id"            => $this->input->post('skpd_id', true),
                'pegawai_id'        => $this->input->post('pegawai_id', true),
                "jenis_absen"       => $this->input->post('jenis_absen', true),
                "tanggal"           => date("Y-m-d", strtotime($this->input->post('tanggal', true))),
            ];
        }

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tb_absen_manual', $data);

        return array(true, "Absen Manual telah diubah");
    }

    private function _upload($skpd_id)
    {
        $this->load->model('Skpd_model');
        $skpd       = $this->Skpd_model->getSkpdById($skpd_id);
        
        $ext = strtolower(pathinfo($_FILES["lampiran_amp"]["name"], PATHINFO_EXTENSION));
        $ext = strtolower(pathinfo($_FILES["lampiran_ams"]["name"], PATHINFO_EXTENSION));

        $sub_dir = $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y') . "/" . date('m') . "/";

        $directory = './resources/berkas/absen_manual/' . $sub_dir;


        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true)) {
                die('Failed to create folders...');
            }
        }

        $this->_createdPHP('./resources/berkas/absen_manual');
        $this->_createdPHP('./resources/berkas/absen_manual/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd']);
        $this->_createdPHP('./resources/berkas/absen_manual/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y'));
        $this->_createdPHP('./resources/berkas/absen_manual/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y') . "/" . date('m'));

        $_FILES['lampiran_amp']['name'] = "amp_" . time() . "_" . $_FILES['lampiran_amp']['name'];

        $config['upload_path']          = $directory;
        $config['allowed_types']        = 'pdf|jpg|jpeg|png';
        $config['overwrite']            = true;
        $config['max_size']             = 524; // 500KB

        $this->load->library('upload', $config);



        if ($this->upload->do_upload('lampiran_amp')) {
            $directory = $sub_dir . "" . $this->upload->data("file_name");
            return array(true, $directory);
        } else {
            return array(false, $this->upload->display_errors());
        }
    }

    private function _upload2($skpd_id)
    {
        $ext = strtolower(pathinfo($_FILES["lampiran_amp"]["name"], PATHINFO_EXTENSION));

        $this->load->model('skpd_model');
        $skpd = $this->skpd_model->getskpdById($skpd_id);
        $sub_dir2 = $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y') . "/" . date('m') . "/";
        $directory2 = './resources/berkas/absen_manual/' . $sub_dir2;


        if (!file_exists($directory2)) {
            if (!mkdir($directory2, 0777, true)) {
                die('Failed to create folders...');
            }
        }

        $this->_createdPHP('./resources/berkas/absen_manual');
        $this->_createdPHP('./resources/berkas/absen_manual/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd']);
        $this->_createdPHP('./resources/berkas/absen_manual/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y'));
        $this->_createdPHP('./resources/berkas/absen_manual/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y') . "/" . date('m'));

        $_FILES['lampiran_ams']['name'] = "ams_" . time() . "_" . $_FILES['lampiran_ams']['name'];

        $config['upload_path']          = $directory2;
        $config['allowed_types']        = 'pdf|jpg|png|jpeg';
        $config['overwrite']            = true;
        $config['max_size']             = 524; // 500KB

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('lampiran_ams')) {
            $directory2 = $sub_dir2 . "" . $this->upload->data("file_name");
            return array(true, $directory2);
        } else {
            return array(false, $this->upload->display_errors());
        }
    }

    public function _createdPHP($DIR)
    {
        $index = fopen($DIR . "/index.php", "w") or die("Unable to open file!");
        $val = "<script>location.href='" . base_url() . "auth/blocked'</script>";
        fwrite($index, $val);
        fclose($index);
    }


    public function getIzinById($id = false)
    {
        return $this->db->get_where('tb_absen_manual', ['id' => $id])->row_array();
    }

    public function deleteDataManual($id)
    {
        $this->_deleteImage($id);
        $this->db->where('id', $id);
        $this->db->delete('tb_absen_manual');
    }

    private function _deleteImage($id)
    {
        $manual = $this->getManualById($id);

        $filename = $manual['lampiran_amp'];
        $filename = $manual['lampiran_ams'];
        return array_map('unlink', glob(FCPATH . "resources/berkas/absen_manual/$filename"));
    }

    function insert_data($data, $table)
    {
        $this->db->insert($table, $data);
    }

    function delete_data($where, $table)
    {
        $this->db->delete($table, $where);
    }
}
