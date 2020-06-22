<?php

defined('_ZERO_PATH_') or exit('You shall not pass!');


if (!function_exists('redirect')) {
    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * @param string $uri URL
     * @param string $method Redirect method
     *            'auto', 'location' or 'refresh'
     * @param int $code HTTP Response status code
     * @return    void
     */
    function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = site_url($uri);
        }

        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) or !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                    ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }

        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, TRUE, $code);
                break;
        }
        exit;
    }
}


if (!function_exists('session_of')) {

    /**
     * obtain value from session
     * @param $key string key of session
     * @param $default mixed default value
     * @return mixed|null value
     */
    function session_of($key, $default = NULL)
    {
        if (empty($_SESSION)) {
            return $default;
        }
        $v = array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
        return empty($v) ? $default : $v;
    }
}


if (!function_exists('error_404')) {

    /**
     * 404 错误处理
     */
    function error_404()
    {
        if (array_key_exists('404', _R_) && !empty(_R_['404'])) {
            redirect(_R_['404']);
        } else {
            println('404 Error');
        }
        exit();
    }
}


if (!function_exists('error_500')) {
    /**
     * 500 错误处理
     */
    function error_500()
    {
        if (array_key_exists('500', _R_) && !empty(_R_['500'])) {
            redirect(_R_['500']);
        } else {
            println('500 Error');
        }
        exit();
    }
}

