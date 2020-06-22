<?php
defined('_ZERO_PATH_') or exit('You shall not pass!');


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
        session_start();

        $p = $this->obtain_path();

        if (array_key_exists('preHandle', _CFG_['hooks'])) {
            $hooks = _CFG_['hooks']['preHandle'];
            foreach ($hooks as $h) {
                $hook = new $h;
                $hook->execute($p);
            }
        }

        if (_CFG_['suffix'] && str_end_with($p, _CFG_['suffix'])) {
            $p = substr($p, 0, strpos($p, _CFG_['suffix']));
        }
        if (empty($this->controller_config)) {
            $this->controller_config = $this->_parse_controller($p);
        }
    }


    /**
     * 执行路由
     */
    public function dispatch()
    {
        if (null == $this->controller_config) {
            error_404();
        } else {
            $class = $this->controller_config[1];
            $method = $this->controller_config[2];
            $args = $this->controller_config[3];

            try {
                $c = new ReflectionClass($class);
                if (!$c->hasMethod($method)) {
                    error_404();
                }

                $i = $c->newInstanceArgs();
                $m = $c->getmethod($method);
                $m->invokeArgs($i, $args);
            } catch (ReflectionException $e) {
                error_500();
            }
        }
    }


    /**
     * 获取请求路径信息
     */
    private function obtain_path()
    {
        $path = $this->request_uri();
        $idx = strpos($path, '?');
        if ($idx > 0) {
            $path = substr($path, 0, $idx);
        }

        if (str_start_with($path, _APP_CONTEXT_)) {
            $path = substr($path, strlen(_APP_CONTEXT_));
        }
        if (str_start_with($path, '/index.php')) {
            $path = substr($path, strlen('/index.php'));
        }
        if (str_start_with($path, 'index.php') && str_len_cmp('index.php', $path)) {
            $path = substr($path, strlen('index.php'));
        }
        return $path;
    }


    /**
     * 解析获取Controller信息
     * @param $path string 请求路径
     * @return array|null 路径对应Controller信息
     */
    private function _parse_controller($path)
    {
        $cfg = NULL;
        // 1. 用户配置路径优先级最高，执行严格匹配
        $cfg = $this->_parse_rest_path($path);
        if (NULL != $cfg) {
            return $cfg;
        }
        // 2. 默认路径次优先级
        $cfg = $this->_parse_controller0($path);

        // 3. 模糊匹配用户配置路径
        if (NULL == $cfg) {
            foreach (_R_ as $key => $value) {
                if (str_start_with($path, $key)) {
                    $p = $value;
                    $sub = substr($path, strlen($key));
                    if (!empty($sub)) {
                        if (str_start_with($sub, '/') && strlen($sub) > 1) {
                            $sub = substr($sub, 1);
                            $p = (str_end_with($value, '/') ? $value : $value . '/') . $sub;
                        }
                    }
                    $cfg = $this->_parse_controller0($p);
                    return $cfg;
                }
            }
        }
        return $cfg;
    }


    /**
     * 处理rest路径
     * @param $path string 请求路径
     * @return array|null  路径对应Controller信息
     */
    private function _parse_rest_path($path)
    {
        //1. 先全名验证
        if (array_key_exists($path, _R_)) {
            $p = _R_[$path];
            return $this->_parse_controller0($p);
        }

        // 2. rest 路径验证
        $arr = explode('/', $path);
        $size = count($arr);
        foreach (_R_ as $key => $value) {
            $a = explode('/', $key);
            if ($size != count($a)) {
                continue;
            }
            $sub = '';
            for ($i = 0; $i < $size; $i++) {
                if ($arr[$i] == $a[$i]) {
                    continue;
                }
                // 要求路径参数格式是{id}这样的格式
                if (!str_start_with($a[$i], '{') && !str_end_with($a[$i], '}')) {
                    continue 2;
                }
                if (strlen($sub) > 0) {
                    $sub = $sub . '/';
                }
                $sub = $sub . $arr[$i];
            }
            // 无法全名匹配也无法从路径中解析参数则说明是无效路径
            if (empty($sub)) {
                continue;
            }
            // 将参数接到value路径上，会在之后的逻辑中取出参数
            $p = $value . (!str_end_with($value, '/') ? '/' : '') . $sub;
            return $this->_parse_controller0($p);
        }
        return null;
    }


    /**
     * 解析获取Controller信息
     * @param $path string 请求路径
     * @return array|null 路径对应Controller信息
     */
    private function _parse_controller0($path)
    {
        $arr = explode('/', $path);
        $tmp = '';
        $size = count($arr);
        for ($i = 0; $i < $size; $i++) {
            $str = $arr[$i];
            $class = ucwords(strtolower($str)) . 'Controller';
            if (file_exists(_CONTROLLER_PATH_ . $tmp . $class . '.php')) {
                $m = ($i + 1) < $size && !empty($arr[$i + 1]) ? $arr[$i + 1] : 'index';
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


    /**
     * 获取请求路径
     * @return mixed|string
     */
    private function request_uri()
    {
        $uri = '';
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            // check this first so IIS will catch
            $uri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            // Check if using mod_rewrite
            $uri = $_SERVER['REDIRECT_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            // IIS 5.0, PHP as CGI
            $uri = $_SERVER['ORIG_PATH_INFO'];
        }
        return $uri;
    }

}