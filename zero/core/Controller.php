<?php
defined('_ZERO_PATH_') OR exit('You shall not pass!');


class Z_Controller
{




    /**
     * 展示页面
     *
     * @param $page string 页面地址
     * @param $params array 页面变量
     */
    protected function render_view($page, $params = array())
    {
        if (!file_exists(_VIEW_PATH_ . $page . '.php')) {
            $this->error_404();
        } else {
            extract($params);
            include_once _VIEW_PATH_ . $page . '.php';
        }
    }


    /**
     * 展示json
     *
     * @param $obj mixed 对象
     */
    protected function render_json($obj)
    {
        echo json_encode($obj);
    }


    /**
     * 跳转404
     */
    private function error_404()
    {
        println('404 Error');
    }
}