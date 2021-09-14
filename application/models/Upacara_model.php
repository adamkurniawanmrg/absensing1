<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upacara_model extends CI_Model
{
    public function getAllUpacara()
    {
        return $this->db->order_by('tanggal', "desc")->get('tb_upacara_libur')->result_array();
    }

    public function getUpacaraById($id)
    {
        return $this->db->get_where('tb_upacara_libur', ['id' => $id])->row_array();
    }
    public function getUpacara()
    {
        return $this->db->get('tb_upacara_libur')->row_array();
    }

    public function addDataUpacara()
    {
        if ($this->input->post('kategori') == 2) {
            $upacara_hari_libur = null;
        } else if ($this->input->post('kategori') == 1 && !$this->input->post('upacara_libur')) {
            $upacara_hari_libur = "no";
        } else if ($this->input->post('kategori') == 1 && $this->input->post('upacara_libur') == "yes") {
            $upacara_hari_libur = "yes";
        }
        $data = [
            "nama_hari"          => $this->input->post('nama_hari', true),
            "tanggal"            => date("Y-m-d", strtotime($this->input->post('tanggal', true))),
            "kategori"           => $this->input->post('kategori', true),
            "upacara_hari_libur" => $upacara_hari_libur,
        ];

        $this->db->where('id', $this->input->post('id'));
        $this->db->insert('tb_upacara_libur', $data);
    }

    public function editDataUpacara()
    {

        if ($this->input->post('kategori') == "Libur") {
            $upacara_hari_libur = null;
        } else if ($this->input->post('kategori') == "Upacara" && !$this->input->post('upacara_libur')) {
            $upacara_hari_libur = "no";
        } else if ($this->input->post('kategori') == "Upacara" && $this->input->post('upacara_libur') == "yes") {
            $upacara_hari_libur = "yes";
        }
        $data = [
            "nama_hari"             => $this->input->post('nama_hari', true),
            "tanggal"               => date("Y-m-d", strtotime($this->input->post('tanggal', true))),
            "kategori"              => $this->input->post('kategori', true),
            "upacara_hari_libur"    => $upacara_hari_libur,
            "created_at"            => time()
        ];
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tb_upacara_libur', $data);
    }


    public function deleteDataUpacara($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_upacara_libur');
    }
}
