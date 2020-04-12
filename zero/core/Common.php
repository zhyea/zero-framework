<?php
defined('_ZERO_PATH_') OR exit('You shall not pass!');


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
