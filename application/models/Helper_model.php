<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Helper_model extends CI_Model
{
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    public function tanggal($datetime, $showAll=false){
        $date_created = explode(' ', $datetime);
        $tanggal = explode('-', $date_created[0]);

        $r_hari = $date_created[0]&&$date_created[0]!="" ? $this->hari[date("w", strtotime($date_created[0]))]:null;
        $r_tanggal = $date_created[0]&&$date_created[0]!="" ? $tanggal[2]." ".$this->bulan[(int) $tanggal[1]]." ".$tanggal[0] : null;
        $r_jam = $date_created[1]&&$date_created[1]!="" ? date("H:i", strtotime($date_created[1])) : null;

        if ($showAll) {
        	return $r_hari.", ".$r_tanggal." <em class='text-info'>".$r_jam."</em>";
        }

        return array(
            "hari"       => $r_hari,
            "tanggal"    => $r_tanggal,
            "jam"        => $r_jam
        );
    }

    function alert($color, $title, $description)
    {
        $alert ='<div class="alert alert-'.$color.'" id="finish-alert">
                    <button type="button" class="close" data-dismiss="alert">x</button>
                    <strong id="alert_judul">'.$title.'</strong> <br /><span id="alert_deskripsi">'.$description.'</span>
                </div>';
        return $this->session->set_flashdata('alert', $alert);
    }    
    function photo_profile($user){
        return  '<div style="border-radius: 100%; width: 35px; margin: auto; height: 35px; background: #eaeaea;text-align: center; background: '.$user->color_profile.'"></div>
                <center>
                    <span style="font-size: 24px; display: block; color: #fff; text-transform: uppercase; margin: auto; margin-top: -35px;">'.strtoupper(substr($user->nama_depan, 0,1)).'</span>
                </center>';

    }
}
