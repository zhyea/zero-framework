<?php
defined('_ZERO_PATH_') or exit('You shall not pass!');


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


if (!function_exists('append_child_path')) {
    /**
     * add child path to parent path
     *
     * @param $parent_dir string parent path
     * @param $child_path string child path
     * @return string
     */
    function append_child_path($parent_dir, $child_path)
    {
        return $parent_dir . strtr(
                trim($child_path, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
            );
    }
}


if (!function_exists('println')) {
    /**
     * print string in line
     *
     * @param $str string
     */
    function println($str)
    {
        echo "$str <BR/>";
    }
}


if (!function_exists('str_start_with')) {
    /**
     * Check is str1 start with str2
     *
     * @param $str1 string target string
     * @param $str2 string compare string
     * @return bool
     */
    function str_start_with($str1, $str2)
    {
        return strpos($str1, $str2) === 0;
    }
}


if (!function_exists('str_len_cmp')) {
    /**
     * Compare str1 and str2 with length
     *
     * @param $str1 string target string
     * @param $str2 string compare string
     * @return bool
     */
    function str_len_cmp($str1, $str2)
    {
        return strlen($str2) - strlen($str1);
    }
}


if (!function_exists('str_end_with')) {
    /**
     * Check is str1 end with str2
     *
     * @param $str1 string target string
     * @param $str2 string compare string
     * @return bool
     */
    function str_end_with($str1, $str2)
    {
        return strrchr($str1, $str2) === $str2;
    }
}


if (!function_exists('get_files')) {

    /**
     * get files from certain path
     *
     * @param $path string the path
     * @param $recursive boolean  read recursively
     * @return array the files;
     */
    function get_files($path, $recursive = false)
    {
        $result = array();

        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $f) {
                $sub_path = (str_end_with($path, '/') ? $path : $path . '/') . $f;
                if ($f == '.' || $f == '..') {
                    continue;
                } else if (is_dir($sub_path) && $recursive) {
                    $sub_files = get_files($sub_path, $recursive);
                    if (sizeof($sub_files) > 0) {
                        array_push($result, ...$sub_files);
                    }
                } else {
                    array_push($result, $sub_path);
                }
            }
        }
        return $result;
    }
}


if (!function_exists('del_file')) {

    /**
     * delete file
     *
     * @param $path string path of file
     */
    function del_file($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}


if (!function_exists('del_dir')) {
    /**
     * delete dir
     * @param $dir string path of dir
     * @return bool
     */
    function del_dir($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $full_path = $dir . DIRECTORY_SEPARATOR . $file;
                if (!is_dir($full_path)) {
                    unlink($full_path);
                } else {
                    del_dir($full_path);
                }
            }
        }

        closedir($dh);

        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('array_key_rm')) {
    /**
     * return a new array which doesn't exists the target key
     *
     * @param $key mixed target key
     * @param $arr array src array
     * @return array new array which doesn't exists the target key
     */
    function array_key_rm($key, $arr)
    {
        if (!array_key_exists($key, $arr)) {
            return $arr;
        }
        $keys = array_keys($arr);
        $index = array_search($key, $keys);
        if ($index !== FALSE) {
            array_splice($arr, $index, 1);
        }
        return $arr;
    }
}


if (!function_exists('array_copy')) {
    /**
     * copy the element of a array to another one
     * @param $array array source array
     * @return array new array with all elements from the source
     */
    function array_copy($array)
    {
        $result = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $result[$key] = array_copy($val);
            } elseif (is_object($val)) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }
}


if (!function_exists('build_tree')) {

    function add_children($array, &$root, $id_key = 'id', $parent_key = 'parent', $children_key = 'children')
    {
        if (empty($root) || empty($array)) {
            return;
        }
        $id = $root[$id_key];
        foreach ($array as $ele) {
            $p = empty($ele[$parent_key]) ? 0 : $ele[$parent_key];
            if (empty($root[$children_key])) {
                $root[$children_key] = array();
            }
            if ($id == $p) {
                add_children($array, $ele, $id_key, $parent_key, $children_key);
                array_push($root[$children_key], $ele);
            }
            if (empty($root[$children_key])) {
                unset($root[$children_key]);
            }
        }
    }

    /**
     * build tree from array
     * @param $array array src array
     * @param $root array root node of tree
     * @param $default_id_value mixed default root node id key value
     * @param $id_key string id key of tree node
     * @param $parent_key string parent key of tree node
     * @param $children_key string children key of tree node
     * @return array tree
     */
    function build_tree($array, $root = array(), $id_key = 'id', $parent_key = 'parent', $children_key = 'children', $default_id_value = 0)
    {
        $root = empty($root) ? array() : $root;
        if (empty($root[$id_key])) {
            $root[$id_key] = $default_id_value;
        }
        add_children($array, $root, $id_key, $parent_key, $children_key);
        return $root;
    }
}


if (!function_exists('mb_trim')) {
    /**
     * remove the non character flags from str
     * @param $str string src str
     * @return false|string result
     */
    function mb_trim($str)
    {
        $str = mb_ereg_replace('^(([ \r\n\t])*(　)*)*', '', $str);
        $str = mb_ereg_replace('(([ \r\n\t])*(　)*)*$', '', $str);
        return $str;
    }
}
