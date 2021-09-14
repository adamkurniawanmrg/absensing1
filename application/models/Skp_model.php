<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Skp_model extends CI_Model
{
    public function getAllSkp(){
        return $this->db->get('tb_skp')->result_array();
    }
    public function getAllSkpMeta()
    {
        return $this->db->get('tb_skp_meta')->result();
    }

    public function getSkpById($id)
    {
        return $this->db->get_where('tb_skp', ['id' => $id])->row_array();
    }

    public function deleteSkp($id)
    {
        $this->db->where('id', $id)->delete('tb_skp');
        $this->db->where('skp_id', $id)->delete('tb_skp_meta');
    }
    

}
