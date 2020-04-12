<?php
defined('_ZERO_PATH_') OR exit('You shall not pass!');


class Z_Model
{

    /**
     * 查询获取一行记录
     *
     * @param $sql string SQL语句
     * @param $params array 查询参数
     * @return array 查询结果
     */
    protected function _get($sql, $params)
    {

        $dbh = $this->conn();
        $stmt = $dbh->prepare($sql);
        $result = array();
        if ($stmt->execute($params)) {
            if ($row = $stmt->fetch()) {
                $result = $row;
            }
        }
        $dbh = null;
        return $result;
    }

    /**
     * 查询获取多行记录
     *
     * @param $sql string SQL语句
     * @param $params array 查询参数
     * @return array 查询结果
     */
    protected function _query($sql, $params)
    {
        $dbh = $this->conn();
        $stmt = $dbh->prepare($sql);
        $result = array();
        if ($stmt->execute($params)) {
            while ($row = $stmt->fetch()) {
                array_push($result, $row);
            }
        }
        $dbh = null;
        return $result;
    }


    /**
     * 执行sql语句
     *
     * @param $sql string SQL语句
     * @param $params array SQL参数
     * @return bool 是否执行成功
     */
    protected function _execute($sql, $params)
    {
        $dbh = $this->conn();
        $stmt = $dbh->prepare($sql);
        $r = $stmt->execute($params);
        $dbh = null;
        return $r;
    }


    /**
     * 创建连接
     *
     * @return PDO PDO对象
     */
    private function conn()
    {
        extract(_DB_);
        $dsn = "$dbsystem:host=$hostname;dbname=$database";
        return new PDO($dsn, $username, $password, $options);
    }

}