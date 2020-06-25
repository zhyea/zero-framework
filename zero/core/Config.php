<?php
defined('_ZERO_PATH_') or exit('You shall not pass!');


if (!function_exists('is_https')) {
    /**
     * Is HTTPS?
     *
     * Determines if the application is accessed via an encrypted (HTTPS) connection.
     *
     * @return    bool
     */
    function is_https()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return TRUE;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return TRUE;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return TRUE;
        }

        return FALSE;
    }
}


if (!function_exists('site_url')) {
    /**
     * Get site url
     *
     * @param string $context the context or namespace of the site
     * @return string
     */
    function site_url($context = '')
    {
        if (isset($_SERVER['SERVER_ADDR'])) {
            if (strpos($_SERVER['SERVER_ADDR'], ':') !== FALSE) {
                $server_addr = '[' . $_SERVER['SERVER_ADDR'] . ']';
            } else {
                $server_addr = $_SERVER['SERVER_ADDR'];
            }

            $server_port = 80;
            if (isset($_SERVER['SERVER_PORT'])) {
                $server_port = $_SERVER['SERVER_PORT'];
            }

            if (empty($context)) {
                $script_name = $_SERVER['SCRIPT_NAME'];
                $context = substr($script_name, 0, strpos($script_name, basename($script_name)));
            } else {
                $context = '/' . $context . '/';
            }

            $base_url = (is_https() ? 'https' : 'http') . '://' . $server_addr
                . ($server_port == 80 ? '' : ':' . $server_port)
                . $context;
        } else {
            $base_url = 'http://localhost/';
        }

        return $base_url;
    }
}


if (!function_exists('error_503')) {
    /**
     * handle 503 error
     */
    function error_503()
    {
        if (array_key_exists('503', _R_) && !empty(_R_['503'])) {
            redirect_in_site(_R_['503']);
        } else {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
        }
        http_response_code(503);
        exit(3); // EXIT_CONFIG
    }
}


if (!function_exists('app_path')) {
    /**
     * check app folder
     *
     * @param $app_folder string app folder
     * @return string app folder
     */
    function app_path($app_folder)
    {
        if (is_dir($app_folder)) {
            $app_folder = real_path($app_folder);
        } elseif (is_dir(_ROOT_DIR_ . $app_folder . DIRECTORY_SEPARATOR)) {
            $app_folder = append_child_path(_ROOT_DIR_, $app_folder);
        } else {
            error_503();
        }
        if (str_end_with($app_folder, DIRECTORY_SEPARATOR)) {
            return $app_folder;
        }
        return $app_folder . DIRECTORY_SEPARATOR;
    }
}


if (!function_exists('view_path')) {
    /**
     * check view folder
     *
     * @param $view_folder string view folder
     * @return string app folder
     */
    function view_path($view_folder)
    {
        // The path to the "views" directory
        if (!isset($view_folder[0]) && is_dir(_APP_PATH_ . 'views' . DIRECTORY_SEPARATOR)) {
            $view_folder = _APP_PATH_ . 'views';
        } elseif (is_dir($view_folder)) {
            $view_folder = real_path($view_folder);
        } elseif (is_dir(_APP_PATH_ . $view_folder . DIRECTORY_SEPARATOR)) {
            $view_folder = append_child_path(_APP_PATH_, $view_folder);
        } else {
            error_503();
        }

        if (str_end_with($view_folder, DIRECTORY_SEPARATOR)) {
            return $view_folder;
        }
        return $view_folder . DIRECTORY_SEPARATOR;
    }
}


if (!function_exists('require_app_file')) {
    /**
     * add app file
     *
     * @param $app_file string app file
     */
    function require_app_file($app_file)
    {
        require_once _APP_PATH_ . $app_file . '.php';
    }
}


if (!function_exists('require_model')) {
    /**
     * add model class page
     *
     * @param $model_class string model class page
     */
    function require_model($model_class)
    {
        require_app_file('model/' . $model_class);
    }
}


if (!function_exists('require_service')) {
    /**
     * add service class page
     *
     * @param $service_class string service class page
     */
    function require_service($service_class)
    {
        require_app_file('service/' . $service_class);
    }
}


if (!function_exists('require_by_dir')) {
    /**
     * add all php files in target dir
     *
     * @param $dir string target directory
     */
    function require_by_dir($dir)
    {
        $files = get_files($dir, true);
        foreach ($files as $f) {
            if (str_end_with($f, '.php')) {
                require $f;
            }
        }
    }
}


if (!function_exists('require_once_by_dir')) {
    /**
     * add all php files in target dir
     *
     * @param $dir string target directory
     */
    function require_once_by_dir($dir)
    {
        $files = get_files($dir, true);
        foreach ($files as $f) {
            if (str_end_with($f, '.php')) {
                require_once $f;
            }
        }
    }
}

/**
 * define site url
 */
define('_SITE_URL_', site_url());

/**
 * define app path and controller path
 */
$app_folder = app_path($app_folder);

define('_APP_PATH_', $app_folder);

define('_CONTROLLER_PATH_', $app_folder . 'controller' . DIRECTORY_SEPARATOR);


/**
 * define view path
 */
$view_folder = view_path($view_folder);

define('_VIEW_PATH_', $view_folder);


/**
 * define router
 */
require_once _APP_PATH_ . 'config' . DIRECTORY_SEPARATOR . 'routes' . '.php';
uksort($routes, 'str_len_cmp');
define('_R_', $routes);


/**
 * define db config
 */
require_once _APP_PATH_ . 'config' . DIRECTORY_SEPARATOR . 'database' . '.php';
define('_DB_', $db[$active_group]);


/**
 * define common config
 */
require_once _ZERO_PATH_ . 'config' . DIRECTORY_SEPARATOR . 'config' . '.php';
require_once _ZERO_PATH_ . 'config' . DIRECTORY_SEPARATOR . 'hooks' . '.php';
require_once _APP_PATH_ . 'config' . DIRECTORY_SEPARATOR . 'config' . '.php';
require_once _APP_PATH_ . 'config' . DIRECTORY_SEPARATOR . 'hooks' . '.php';
define('_CFG_', $config);

/**
 * define upload path
 */
define('_UPLOAD_PATH_', _VIEW_PATH_ . DIRECTORY_SEPARATOR . 'upload');

if (!is_dir(_UPLOAD_PATH_)) {
    mkdir(_UPLOAD_PATH_, 0777, true);
}


if (!empty(_CFG_['time_zone'])) {
    date_default_timezone_set('Asia/Shanghai');
}
