<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
	public function __construct(){
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
    }
    
    public function index(){
        redirect('https://layanan.labura.go.id/');
    }
    
	public function login($user_id=false, $token=false)
	{
        if(!$user_id || !$token){
            return;
        }
        $user_key = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
        $pass_key = 'b546a6dfc4';

        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&token='.$token.'&user_id='.$user_id;

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, 'https://layanan.labura.go.id/api/getLogin');
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $posts);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
        
        $return = json_decode($results, true);

        if(!$return){
            echo "<script>
                alert('Gagal!');
                window.close();
            </script>";
            return;
        }
        $return['token']         = $token;
        $return['start_token']   = date("Y-m-d H:i:s");
        $return['websiteLogo']   = $this->getLogo();

        $this->session->set_userdata($return);
        redirect('home?token='.$this->session->userdata('token'));
        return;

    }
    
    private function getLogo(){
        $user_key   = API()->user_key;
        $pass_key   = API()->pass_key;
        $url        = API()->getWebsiteLogo;

        $posts ='user_key='.$user_key.'&pass_key='.$pass_key;

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
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

    public function regetToken(){
        if(isset($_GET['token'])){

            $token = getToken($this->session->userdata('token'), $this->session->userdata('user_id'));
            if(!$token){ redirect('auth/logout/invalidToken'); return;}

            if($token['status']==0){redirect('auth/logout/invalidToken'); return;}

            redirect('home?token='.$this->session->userdata('token'));
            return;
        }
        redirect('auth/regetToken?token='.md5(rand()." - ".time()));
    }

    public function logout($customCapt=false)
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('nama');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('opd_id');
        $this->session->unset_userdata('role_id');
        $this->session->unset_userdata('token');
        $this->session->unset_userdata('start_token');

        $this->session->set_flashdata('pesan', '
        <div class="alert alert-'.($customCapt ? 'danger' : 'success').'" role="alert">
        <button type="button" class="close" data-dismiss="alert">x</button>
        '.($customCapt ? 'Invalid token!' : 'Kamu Telah Logout').'
        </div>
        ');

        redirect('https://layanan.labura.go.id/');
        return;
    }
    
     public function logoutaplikasi()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('nama');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('opd_id');
        $this->session->unset_userdata('role_id');
        $this->session->unset_userdata('token');
        $this->session->unset_userdata('start_token');

        redirect('https://layanan.labura.go.id//auth/logout');
        return;
    }
    
    public function forcelogout()
    {

        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('nama');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('opd_id');
        $this->session->unset_userdata('role_id');
        $this->session->unset_userdata('token');
        $this->session->unset_userdata('start_token');

        $this->session->set_flashdata('pesan', '
        <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong>Force Logout !</strong> Anda telah logout!
        </div>
        ');

        redirect('https://layanan.labura.go.id/');
        return;
    }


    public function blocked404(){
        $data = [
            'page'  => 'blocked'
        ];
        $this->load->view('template/custom', $data);
    }
    public function notfound404(){
        $data = [
            'page'  => '404'
        ];
        $this->load->view('template/custom', $data);
    }

    
}
