<?php
defined('_APP_PATH_') OR exit('You shall not pass!');


require_model('HelloModel');


class HelloController extends Z_Controller
{


    private $model;


    public function __construct()
    {
        $this->model = new HelloModel();
    }

    public function index()
    {
        $this->render_view('welcome', array('hi' => 'Hello World'));
    }


    public function user()
    {
        $r = $this->model->get(1);
        $this->render_json($r);
    }

}