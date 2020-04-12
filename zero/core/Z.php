<?php
defined('_ZERO_PATH_') OR exit('You shall not pass!');


require_once 'Common.php';
require_once 'Config.php';
require_once 'Router.php';


require_once 'Controller.php';
require_once 'Model.php';


$router = new Router();
$router->dispatch();