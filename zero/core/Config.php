<?php
defined('_ZERO_PATH_') OR exit('You shall not pass!');


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
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
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
        } elseif (is_dir(_ROOT_DIR__ . $app_folder . DIRECTORY_SEPARATOR)) {
            $app_folder = append_child_path(_ROOT_DIR__, $app_folder);
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


if (!function_exists('require_model')) {
    /**
     * add model class page
     *
     * @param $model_class string model class page
     */
    function require_model($model_class)
    {
        require_once _APP_PATH_ . '/model/' . $model_class . '.php';
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

define('_CONTROLLER_PATH_', $app_folder . '/controller/');

/**
 * define view path
 */
$view_folder = view_path($view_folder);

define('_VIEW_PATH_', $view_folder);

/**
 * define router
 */
require_once _APP_PATH_ . '/config/routes.php';

define('_R_', $routes);


/**
 * define db config
 */
require_once _APP_PATH_ . '/config/database.php';

define('_DB_', $db[$active_group]);