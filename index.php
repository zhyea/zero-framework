<?php

/**
 * App Backend Folder
 */
$app_folder = 'app';

/**
 * App Frontend Folder
 */
$view_folder = 'templates/sunny';


/**
 * zero framework folder
 */
$zero_path = 'zero';


if (!function_exists('real_path')) {
    /**
     * return absolute path
     *
     * @param $path string
     * @return string
     */
    function real_path($path)
    {
        if (($_temp = realpath($path)) !== FALSE) {
            return $_temp . DIRECTORY_SEPARATOR;
        } else {
            return strtr(
                    rtrim($path, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;
        }
    }
}


/**
 * App root dir
 */
define('_ROOT_DIR__', dirname(__FILE__));

/**
 * The name of this file
 */
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));


/**
 * define zero framework path
 */
$zero_path = real_path($zero_path);

define('_ZERO_PATH_', $zero_path);


/**
 * 加载zero框架
 */
require_once _ZERO_PATH_ . 'core/Z.php';


