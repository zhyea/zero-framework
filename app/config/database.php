<?php
defined('_APP_PATH_') OR exit('You shall not pass!');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array('dbsystem' => 'mysql'
, 'hostname' => 'localhost'
, 'username' => 'root'
, 'password' => 'root'
, 'database' => 'calf'
, 'options' => array(PDO::ATTR_PERSISTENT => true)
);