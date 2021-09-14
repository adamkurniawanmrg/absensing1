<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Skpd_model extends CI_Model
{
    public function getSkpd($forceAll = false){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        
        $akses = [1];
        if(in_array($this->session->userdata('role_id'), $akses) || $forceAll){
            $URL      = API()->getSkpd;
            $posts ='user_key='.$user_key.'&pass_key='.$pass_key;
        }else{
            $URL      = API()->getSkpdById;
            $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&skpd_id='.$this->session->userdata('skpd_id');
        }
    
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
        if(in_array($this->session->userdata('role_id'), $akses) || $forceAll){
            return json_decode($results, true);
        }else{
            return [json_decode($results, true)];
        }
    }

    public function getSkpdById($skpd_id){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getSkpdById;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&skpd_id='.$skpd_id;
    
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

    
}
