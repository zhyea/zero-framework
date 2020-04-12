<?php
defined('_APP_PATH_') OR exit('You shall not pass!');

$routes['default_controller'] = 'welcome';
$routes['404_override'] = '';
$routes['translate_uri_dashes'] = FALSE;
$routes['/hello/user'] = 'hello/user';
$routes['/'] = 'hello';