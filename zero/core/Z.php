<?php
defined('_ZERO_PATH_') or exit('You shall not pass!');


require_once 'Common.php';
require_once 'Config.php';
require_once 'Router.php';

require_once 'Controller.php';
require_once 'Model.php';


require_once_by_dir(_ZERO_PATH_ . 'helpers');

require_once_by_dir(_ZERO_PATH_ . 'hook');

require_once_by_dir(_APP_PATH_ . 'helpers');

require_by_dir(_CONTROLLER_PATH_);

require_by_dir(_APP_PATH_ . 'hook');

gen_htaccess($context);

$router = new Router();
$router->dispatch();