<?php

defined('_ZERO_PATH_') or exit('You shall not pass!');


if (!function_exists('upload_path')) {

    /**
     * return the upload path
     *
     * @param $file string file path
     * @return string full path of file
     */
    function upload_path($file)
    {
        return _UPLOAD_PATH_ . (str_start_with($file, '/') ? '' : DIRECTORY_SEPARATOR) . $file;
    }
}


if (!function_exists('del_upload_file')) {

    /**
     * delete the uploaded file
     *
     * @param $file string full path of file
     */
    function del_upload_file($file)
    {
        if (!empty($file)) {
            $p = upload_path($file);
            del_file($p);
        }
    }
}


if (!function_exists('gen_htaccess')) {
    /**
     * 生成.htaccess文件
     * @param $context string 上下文
     */
    function gen_htaccess($context)
    {
        $content =
            '
# BEGIN Buffalo
Options +FollowSymLinks +SymLinksIfOwnerMatch
AddDefaultCharset utf-8

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /{ctx}/
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /{ctx}/index.php [L]
</IfModule>
# END Buffalo';

        $content = str_replace('{ctx}', $context, $content);

        $path = _ROOT_DIR_ . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
    }
}