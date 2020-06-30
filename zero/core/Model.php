<?php
defined('_ZERO_PATH_') or exit('You shall not pass!');


abstract class Z_Model
{


    protected $table;

    /**
     * Z_Model constructor.
     *
     * @param $table string table name
     */
    public function __construct($table = '')
    {
        if (!empty($table)) {
            $this->table = $table;
        } else {
            $tmp = str_ireplace('model', '', get_called_class());
            $this->table = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $tmp));
        }
    }

    /**
     * 返回当前表的主键
     * @return string
     */
    protected function primaryKey()
    {
        return 'id';
    }

    /**
     * 根据ID获取记录
     *
     * @param $id int 记录ID
     * @return array 对应ID的记录
     */
    public function get_by_id($id)
    {
        if ($id <= 0) {
            return array();
        }
        $sql = "select * from " . $this->table . " where " . $this->primaryKey() . "=?";
        return $this->_get($sql, array($id));
    }


    /**
     * 查询获取一行记录
     *
     * @param $sql string SQL语句
     * @param $params array 查询参数
     * @param int $mode 查询模式
     * @return array 查询结果
     */
    protected function _get($sql, $params, $mode = PDO::FETCH_ASSOC)
    {

        $dbh = $this->_conn();
        $stmt = $dbh->prepare($sql);
        $result = array();
        if ($stmt->execute($params)) {
            if ($row = $stmt->fetch($mode)) {
                $result = $row;
            }
        }
        $dbh = null;
        return $result;
    }


    /**
     * 根据条件获取最新的记录
     * @param $params array 参数，kv对
     * @param int $mode 查询模式
     * @return array 查询结果
     */
    protected function _get_by($params, $mode = PDO::FETCH_ASSOC)
    {
        $first = true;
        $sql = 'select * from ' . $this->table . ' where ';
        foreach ($params as $k => $v) {
            if (!$first) {
                $sql = $sql . 'and ';
            } else {
                $first = false;
            }
            $sql = $sql . $k . '=? ';
        }
        $sql = $sql . ' order by ' . $this->primaryKey() . ' desc limit 1';
        $values = array_values($params);
        return $this->_get($sql, $values, $mode);
    }

    /**
     * 查询获取多行记录
     *
     * @param $sql string SQL语句
     * @param $params array 查询参数
     * @return array 查询结果
     */
    protected function _find($sql, $params = array())
    {
        $dbh = $this->_conn();
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
     * 执行in条件查询
     * @param $key string 查询字段
     * @param $params array in 条件值
     * @return array 查询结果
     */
    protected function _find_in($key, $params)
    {
        if (empty($params)) {
            return array();
        }
        $place_holder = array_fill(0, sizeof($params), '?');
        $sql = 'select * from ' . $this->table . ' where ' . $key . ' in (' . implode(',', $place_holder) . ')';
        return $this->_find($sql, $params);
    }


    /**
     *  执行查询
     * @param $params array 查询参数，键值对
     * @param $order string 排序字段
     * @param $direct string 排序方向
     * @return array  查询结果
     */
    protected function _find_by($params, $order = 'id', $direct = 'desc')
    {
        $sql = 'select * from ' . $this->table . ' where ';
        $first = true;
        foreach ($params as $k => $v) {
            if (!$first) {
                $sql = $sql . 'and ';
            } else {
                $first = false;
            }
            $sql = $sql . $k . '=? ';
        }
        $sql = $sql . ' order by ' . $order . ' ' . $direct;
        $values = array_values($params);
        return $this->_find($sql, $values);
    }


    /**
     * 查询全部
     * @param $order string 排序字段
     * @param $direct string 排序方向
     * @return array 查询结果
     */
    public function find_all($order = 'id', $direct = 'desc')
    {
        $sql = 'select * from ' . $this->table . ' order by ' . $order . ' ' . $direct;
        return $this->_find($sql, array());
    }


    /**
     * 执行统计
     *
     * @param $params array 条件参数，键值对
     * @return int 统计结果
     */
    protected function _count_by($params)
    {
        $sql = 'select count(id) as cnt from ' . $this->table . ' where ';
        $first = true;
        foreach ($params as $k => $v) {
            if (!$first) {
                $sql = $sql . 'and ';
            } else {
                $first = false;
            }
            $sql = $sql . $k . '=? ';
        }
        $values = array_values($params);
        return $this->_count($sql, $values);
    }


    /**
     * 执行统计
     *
     * @param $sql string 统计语句
     * @param $params array 条件参数
     * @return int 统计结果
     */
    protected function _count($sql, $params)
    {
        $r = $this->_get($sql, $params, PDO::FETCH_NUM);
        if (!empty($r)) {
            return $r[0];
        }
        return 0;
    }


    /**
     * 执行replace操作
     *
     * @param $params array 字段名和字段值的键值对
     * @return bool 是否执行成功
     */
    public function replace($params)
    {
        $keys = array_keys($params);
        $values = array_values($params);
        $place_holder = array_fill(0, sizeof($values), '?');
        $sql = 'replace into ' . $this->table . ' (' . implode(',', $keys) . ') values (' . implode(',', $place_holder) . ')';
        return $this->_execute($sql, $values);
    }


    /**
     * 执行insert操作
     *
     * @param $params array 字段名和字段值的键值对
     * @return bool 是否执行成功
     */
    public function insert($params)
    {
        $keys = array_keys($params);
        $values = array_values($params);
        $place_holder = array_fill(0, sizeof($values), '?');
        $sql = 'insert into ' . $this->table . ' (' . implode(',', $keys) . ') values (' . implode(',', $place_holder) . ')';
        return $this->_execute($sql, $values);
    }

    /**
     * 执行update操作
     *
     * @param $params array 参数列表
     * @return bool 是否更新成功
     */
    public function update($params)
    {
        $pm_key = $this->primaryKey();
        $id = $params[$pm_key];
        $params = array_key_rm($pm_key, $params);
        $values = array_values($params);
        $sql = 'update ' . $this->table . ' set ';
        $count = 0;
        foreach ($params as $k => $v) {
            if ($count++ > 0) {
                $sql = $sql . ',';
            }
            $sql = $sql . ' ' . $k . '=?';
        }
        $sql = $sql . ' where ' . $pm_key . '=?';
        array_push($values, $id);
        return $this->_execute($sql, $values);
    }


    /**
     * 新增或更新数据
     *
     * @param $params array 表字段及值
     * @return bool 是否新增或更新成功
     */
    public function insert_or_update($params)
    {
        $pm_key = $this->primaryKey();
        $id = empty($params[$pm_key]) ? 0 : $params[$pm_key];
        if (!empty($id)) {
            return $this->update($params);
        } else {
            $params = array_key_rm($pm_key, $params);
            return $this->insert($params);
        }
    }


    /**
     * 执行delete操作
     *
     * @param $params array 字段名和字段值的键值对
     * @return bool 是否执行成功
     */
    protected function _delete($params)
    {
        $sql = 'delete from ' . $this->table . ' where ';
        $first = true;
        foreach ($params as $k => $v) {
            if (!$first) {
                $sql = $sql . 'and ';
            } else {
                $first = false;
            }
            $sql = $sql . $k . '=? ';
        }
        $values = array_values($params);
        return $this->_execute($sql, $values);
    }


    /**
     * 执行in条件删除
     * @param $key string 查询字段
     * @param $params array in 条件值
     * @return bool
     */
    protected function _delete_in($key, $params)
    {
        if (empty($params)) {
            return false;
        }
        $place_holder = array_fill(0, sizeof($params), '?');
        $sql = 'delete from ' . $this->table . ' where ' . $key . ' in (' . implode(',', $place_holder) . ')';
        return $this->_execute($sql, $params);
    }

    /**
     * 根据ID删除记录
     * @param $ids array 记录ID集合
     * @return bool 是否删除成功
     */
    public function delete_by_ids($ids)
    {
        $pm_key = $this->primaryKey();
        return $this->_delete_in($pm_key, $ids);
    }


    /**
     * 执行删除操作
     * @param $id mixed 记录ID
     * @return bool 是否删除成功
     */
    public function delete_by_id($id)
    {
        $sql = 'delete from ' . $this->table . ' where ' . $this->primaryKey() . '=?';
        return $this->_execute($sql, array($id));
    }

    /**
     * 获取后代ID集合
     * @param $root_id mixed 根ID值
     * @param $parent_col string 父ID字段
     * @return array 后代ID集合
     */
    public function offspring_ids($root_id, $parent_col = 'parent')
    {
        $result = array($root_id);
        $sql = 'select ' . $this->primaryKey() . ' from ' . $this->table . ' where ' . $parent_col . '=?';
        $r = $this->_find($sql, array($root_id));
        foreach ($r as $i) {
            $id = $i[$this->primaryKey()];
            array_push($result, $id);
            $children_ids = $this->offspring_ids($id, $parent_col);
            if (empty($children_ids)) {
                continue;
            }
            $result = array_merge($result, $children_ids);
        }
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
        $dbh = $this->_conn();
        $stmt = $dbh->prepare($sql);
        $r = $stmt->execute($params);
        if (!$r) {
            echo $stmt->errorCode() . ':' . $stmt->errorInfo();
        }
        $dbh = null;
        return $r;
    }


    /**
     * 创建连接
     *
     * @return PDO PDO对象
     */
    private function _conn()
    {
        extract(_DB_);
        $dsn = "$dbsystem:host=$hostname;dbname=$database";
        return new PDO($dsn, $username, $password, $options);
    }

}