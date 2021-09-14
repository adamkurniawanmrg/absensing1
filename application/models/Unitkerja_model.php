<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Unitkerja_model extends CI_Model
{
    
    public function get($opd_id=false){
        $user_key   = API()->user_key;
        $pass_key   = API()->pass_key;
        $URL        = API()->getUnitKerja;
        
        $posts      ='user_key='.$user_key.'&pass_key='.$pass_key;
        $posts      .= $opd_id ? '&opd_id='.$opd_id: null;
        
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
