<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_model extends CI_Model
{
    
    public function getPegawaiMeta($pegawai_id,$jenis_pegawai){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawaiMeta;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&jenis_pegawai=pegawai';
        $posts .= '&pegawai_id='.$pegawai_id;
        $posts .= '&jenis_pegawai='.$jenis_pegawai;
    
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
    public function getPegawai($pegawai_id=null,$skpd_id=null){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawai;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&jenis_pegawai=pegawai';
        $posts .= $pegawai_id ? '&pegawai_id='.$pegawai_id: null;
        $posts .= $skpd_id ? '&skpd_id='.$skpd_id: null;
    
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
    
    public function getPegawaiTks($pegawai_id=null,$skpd_id=null){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawai;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&jenis_pegawai=tks';
        $posts .= $pegawai_id ? '&pegawai_id='.$pegawai_id: null;
        $posts .= $skpd_id ? '&skpd_id='.$skpd_id: null;
    
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
    public function getPegawaiByOpd($opd_id){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawaiByOpd;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&jenis_pegawai=pegawai&opd_id='.$opd_id;

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
    public function getTksByOpd($opd_id){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawaiByOpd;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&jenis_pegawai=tks&opd_id='.$opd_id;

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
    
    public function getPegawaiAtasan($pegawai_id, $jenis_pegawai){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawaiAtasan;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&pegawai_id='.$pegawai_id.'&jenis_pegawai='.$jenis_pegawai;

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
    
    public function getPegawaiByPegawaiAtasan($pegawai_id, $jenis_pegawai){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->getPegawaiByPegawaiAtasan;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.'&pegawai_atasan_id='.$pegawai_id.'&jenis_pegawai_atasan='.$jenis_pegawai;

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
