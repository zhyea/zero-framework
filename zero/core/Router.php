<?php
defined('_ZERO_PATH_') OR exit('You shall not pass!');


class Router
{

    /**
     * Controller信息
     *
     * @var array controller config info
     */
    private $controller_config;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $path = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '/';
        $this->controller_config = $this->_parse_controller($path);
    }


    public function dispatch()
    {
        if (null == $this->controller_config) {
            $this->error_404();
        } else {
            $class = $this->controller_config[1];
            $method = $this->controller_config[2];
            $args = $this->controller_config[3];

            $c = new $class();
            if (!method_exists($c, $method)) {
                $this->error_404();
            }
            $c->$method($args);
        }
    }


    private function _parse_controller($path)
    {
        foreach (_R_ as $key => $value) {
            if (str_start_with($path, $key)) {
                $cfg = $this->_parse_controller0($value);
                if (null != $cfg) {
                    $sub = rtrim($path, $key);
                    if (!empty($sub)) {
                        $params = explode('/', $sub);
                        array_merge($cfg[3], $params);
                    }
                    return $cfg;
                }
                break;
            }
        }
        return $this->_parse_controller0($path);
    }


    private function _parse_controller0($path)
    {
        $arr = explode('/', $path);
        $tmp = '';
        $size = count($arr);
        for ($i = 0; $i < $size; $i++) {
            $str = $arr[$i];
            $class = ucwords(strtolower($str)) . 'Controller';
            if (file_exists(_CONTROLLER_PATH_ . $tmp . $class . '.php')) {
                require_once _CONTROLLER_PATH_ . $tmp . $class . '.php';
                $m = ($i + 1) < $size ? $arr[$i + 1] : 'index';
                $param = ($i + 2) < $size ? array_slice($arr, $i + 2) : array();
                return array(
                    1 => $class,
                    2 => $m,
                    3 => $param
                );
            }
            $tmp = $tmp . '/' . $str . '/';
        }
        return null;
    }


    private function error_404()
    {
        println('404 Error');
    }

}