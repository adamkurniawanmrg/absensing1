<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logabsen_model extends CI_Model
{
    public function getAllLogabsen(){
        return $this->db->get('tb_log_absen')->result_array();
    }
    
    function get_datatables(){
        $this->db->select('tb_log_absen.*, tb_pegawai.id id_pegawai, tb_pegawai.nama, tb_opd.nama_opd');
        $this->db->from('tb_log_absen');
        $this->db->join('tb_pegawai', 'tb_log_absen.pegawai_id = tb_pegawai.id');
        $this->db->join('tb_opd', 'tb_log_absen.opd_id = tb_opd.id');
        $this->db->order_by('jam_masuk', 'DESC');
        return $this->db->get()->result();
    }    

}
