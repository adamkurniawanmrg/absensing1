<?php
function SITENAME(){
    return 'Absensi';
}
function API(){
    return (object) [
                        "user_key"                  => "64240-d0ede73ccaf823f30d586a5ff9a35fa5",
                        "pass_key"                  => "b546a6dfc4",
                        "getSKP"                    => "https://layanan.labura.go.id/api/getSKP",
                        "getOpd"                    => "https://layanan.labura.go.id/api/getOpd",
                        "getToken"                  => "https://layanan.labura.go.id/api/getToken",
                        "getLogin"                  => "https://layanan.labura.go.id/api/getLogin",
                        "getSkpd"                   => "https://layanan.labura.go.id/api/getSkpd",
                        "getSkpdById"               => "https://layanan.labura.go.id/api/getSkpdById",
                        "getPegawai"                => "https://layanan.labura.go.id/api/getPegawai",
                        "getPegawaiMeta"            => "https://layanan.labura.go.id/api/getPegawaiMeta",
                        "getWebsiteLogo"            => "https://layanan.labura.go.id/api/getWebsiteLogo",
                        "getPegawaiByOpd"           => "https://layanan.labura.go.id/api/getPegawaiByOpd",
                        "getUnitKerja"              => "https://layanan.labura.go.id/api/getUnitKerja",
                        "getPegawaiAtasan"          => "https://layanan.labura.go.id/api/getPegawaiAtasan",
                        "getPegawaiByPegawaiAtasan" => "https://layanan.labura.go.id/api/getPegawaiByPegawaiAtasan"
                        ];
}

function is_logged_in()
{
    $ci = get_instance();

    if (!isset($_GET['token'])) { redirect('auth/regetToken?token='.md5(time()."-".rand())); exit(); }

    if (!$ci->session->userdata('token')) { redirect('auth'); exit(); }

    $getToken = getToken($ci->session->userdata('token'), $ci->session->userdata('user_id'));
    
    if(!$getToken){ redirect('auth/forcelogout'); exit();}

    $role = role();
    
    if(!$role){
        redirect('auth/forcelogout');
    }

    $user_id        = $ci->session->userdata('user_id');
    $url            = $ci->uri->segment(2)!="" ? ($ci->uri->segment(1)."/".$ci->uri->segment(2)) : $ci->uri->segment(1);
    $menu           = $ci->db->where('url', $url)->get('tb_menu')->row();

    if(!$menu){
        return;
    }

    $userAccess  = $ci->db->get_where('tb_role_access', [
        'role_id' => $role->role_id,
        'menu_id' => $menu->id
    ]);

    // echo "<pre>";
    // print_r($role);
    // print_r($menu);
    // print_r($userAccess->row());
    // exit();

    if ($userAccess->num_rows() < 1){ redirect('auth/blocked404'); exit();}

}

function getToken($token, $user_id){
    $user_key    = API()->user_key;
    $pass_key    = API()->pass_key;
    $URL         = API()->getToken;

    $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&token='.$token.'&user_id='.$user_id;

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $URL);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $posts);
    curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    $results = curl_exec($curlHandle);
    curl_close($curlHandle);
    
    return json_decode($results, true);
}

function userLogin(){
    $ci = get_instance();

    $user_key       = API()->user_key;
    $pass_key       = API()->pass_key;
    $URL            = API()->getLogin;
    $user_id        = $ci->session->userdata('user_id');
    $token          = $ci->session->userdata('token');

    $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&token='.$token.'&user_id='.$user_id;

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $URL);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $posts);
    curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    $results = curl_exec($curlHandle);
    curl_close($curlHandle);
    
    return json_decode($results, true);
}

function role(){
    $ci = get_instance();

    $role_id       = $ci->session->userdata('role_id');

    return $ci->db->where('role_id', $role_id)->get('tb_role')->row();

}


function getmenu(){
    $ci = get_instance();
    $hasil = '<ul class="nav page-navigation">';
    $role_id = role()->role_id;
    $menu = $ci->db   ->select('tb_role_access.*, tb_menu.*')
                        ->where('tb_role_access.role_id', $role_id)
                        ->where('tb_menu.parent_id', null)
                        ->join('tb_menu', 'tb_role_access.menu_id=tb_menu.id', 'left')
                        ->order_by('tb_menu.urutan', 'asc')
                        ->get('tb_role_access')->result();

        foreach($menu as $m){
            $cekChild = $ci->db->where('parent_id', $m->id)->get('tb_menu')->num_rows();
            if($cekChild>0){
                $hasil .= '<li class="nav-item">
                              <a href="#" class="nav-link" onclick="return false">
                                <i class="'.$m->icon.' menu-icon"></i>
                                <span class="menu-title">'.$m->nama_menu.'</span>
                                <i class="menu-arrow"></i>
                              </a>
                              <div class="submenu">
                                <ul class="submenu-item">
                                ';
                            
                $hasil .= menu($m->id);
                $hasil .= '</ul></div></li>';
                continue;
            }else{
                $hasil .= '<li class="nav-item">
                              <a class="nav-link" href="'.base_url($m->url."?token=".$_GET['token']).'">
                                <i class="'.$m->icon.' menu-icon"></i>
                                <span class="menu-title">'.$m->nama_menu.'</span>
                              </a>
                            </li>';
            }
        }
    $hasil .= "</ul>";
    return $hasil;
}

function menu($parent_id){
        $ci = get_instance();
        $role_id = role()->role_id;
        $menu = $ci->db   ->select('tb_role_access.*, tb_menu.*')
                            ->where('tb_role_access.role_id', $role_id)
                            ->where('tb_menu.parent_id', $parent_id)
                            ->join('tb_menu', 'tb_role_access.menu_id=tb_menu.id', 'left')
                            ->order_by('tb_menu.urutan', 'asc')
                            ->get('tb_role_access')->result();

        $hasil ="";
        foreach($menu as $m){
            $cekChild = $ci->db->where('parent_id', $m->id)->get('tb_menu')->num_rows();
            if($cekChild>0){
                $hasil .= '<li class="nav-item">
                              <a href="#" class="nav-link" onclick="return false">
                                <i class="'.$m->icon.' menu-icon"></i>
                                <span class="menu-title">'.$m->nama_menu.'</span>
                                <i class="menu-arrow"></i>
                              </a>
                              <div class="submenu">
                                <ul class="submenu-item">
                                ';
                            
                $hasil .= menu($m->id);
                $hasil .= '</ul></div></li>';
                continue;
            }else{
                $hasil .= '<li class="nav-item">
                              <a class="nav-link" href="'.base_url($m->url."?token=".$_GET['token']).'">
                                <i class="'.$m->icon.' menu-icon"></i>
                                <span class="menu-title">'.$m->nama_menu.'</span>
                              </a>
                            </li>';
            }
        }
    return $hasil;
}

function check_access($role_id, $menu_id){

    $ci = get_instance();
    $ci->db->where('role_id', $role_id);
    $ci->db->where('menu_id', $menu_id);
    $result = $ci->db->get('tb_user_access_menu');

    if ($result->num_rows() > 0) {
        return "checked='checked'";
    }

}


