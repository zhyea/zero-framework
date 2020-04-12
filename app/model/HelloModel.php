<?php
defined('_APP_PATH_') OR exit('You shall not pass!');


class HelloModel extends Z_Model
{


    /**
     * 根据ID获取用户记录
     *
     * @param  $id int 用户ID
     * @return array 用户记录
     */
    public function get($id)
    {
        $sql = "select * from user where id=?";
        return parent::_get($sql, array(1));
    }


}