<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$dsn = sprintf(
    'mysql:dbname=%s;unix_socket=%s/%s',
    'absensi',
    '/cloudsql',
    'absensi-325704:asia-southeast2:absensi'
);

$db['default'] = array(
	'dsn'	=> $dsn,
	'hostname' => '',
    'username' => 'root',
	'password' => 'absensi-325704:asia-southeast2:absensi',
	'database' => 'absensi',
	'dbdriver' => 'pdo',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
