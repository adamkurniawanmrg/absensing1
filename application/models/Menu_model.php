<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function getsubmenu()
    {
        $query = " SELECT `tb_user_sub_menu`.*, `tb_user_menu`.`menu`
                    FROM `tb_user_sub_menu` JOIN `tb_user_menu`
                    ON `tb_user_sub_menu`.`menu_id` = `tb_user_menu`.`user_menu_id` 
                    ";
        return $this->db->query($query)->result_array();
    }

    public function getAllMenu()
    {
        return $this->db->get('tb_user_menu')->result_array();
    }
    public function getMenuById($id)
    {
        return $this->db->get_where('tb_user_menu', ['user_menu_id' => $id])->row_array();
    }

    public function getSubMenuById($id)
    {
        return $this->db->get_where('tb_user_sub_menu', ['sub_menu_id' => $id])->row_array();
    }

    public function getRoleById($id)
    {
        return $this->db->get_where('tb_user_role', ['role_id' => $id])->row_array();
    }


    public function addDataMenu()
    {
        $data = [
            "menu"          => $this->input->post('menu', true)
        ];
        $this->db->where('user_menu_id', $this->input->post('user_menu_id'));
        $this->db->insert('tb_user_menu', $data);
    }

    public function editDataMenu()
    {
        $data = [
            "menu"  => $this->input->post('menu', true)
        ];
        $this->db->where('user_menu_id', $this->input->post('user_menu_id'));
        $this->db->update('tb_user_menu', $data);
    }

    public function deleteDataMenu($id)
    {
        $this->db->where('user_menu_id', $id);
        $this->db->delete('tb_user_menu');
    }

    public function addDataSubMenu()
    {
        $data = [
            'title'     => $this->input->post('title'),
            'menu_id'   => $this->input->post('menu_id'),
            'url'       => $this->input->post('url'),
            'icon'      => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active')
        ];
        $this->db->insert('tb_user_sub_menu', $data);
    }

    public function editDataSubMenu()
    {
        $data = [
            'title'     => $this->input->post('title'),
            'menu_id'   => $this->input->post('menu_id'),
            'url'       => $this->input->post('url'),
            'icon'      => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active')
        ];
        $this->db->where('sub_menu_id', $this->input->post('sub_menu_id'));
        $this->db->update('tb_user_sub_menu', $data);
    }

    public function deleteDataSubMenu($id)
    {
        $this->db->where('sub_menu_id', $id);
        $this->db->delete('tb_user_sub_menu');
    }

    public function addDataRole()
    {
        $data = [
            "role"          => $this->input->post('role', true)
        ];
        $this->db->where('role_id', $this->input->post('role_id'));
        $this->db->insert('tb_user_role', $data);
    }

    public function editDataRole()
    {
        $data = [
            "role"  => $this->input->post('role', true)
        ];
        $this->db->where('role_id', $this->input->post('role_id'));
        $this->db->update('tb_user_role', $data);
    }

    public function deleteDataRole($id)
    {
        $this->db->where('role_id', $id);
        $this->db->delete('tb_user_role');
    }
}
