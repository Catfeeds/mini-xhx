<?php
$config = array(
    //'配置项'=>'配置值'

    /*********数据库配置**********/
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'xiaohuanxiong', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'x_',
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

    /********url配置**************/
    'URL_MODEL' => 0,// url链接模式
    'VAR_MODULE'            =>  'm',     // 默认模块获取变量
    'VAR_CONTROLLER'        =>  'c',    // 默认控制器获取变量
    'VAR_ACTION'            =>  'a',    // 默认操作获取变量

    /********模板文件配置*********/

    'TMPL_FILE_DEPR'=>'_',

    /********登入配置*************/
    'AUTHCODE_USER' => 'webiwokeruser',
    'AUTHCODE_ADMIN' => 'webiwokeradmin',

    /********上传配置*************/
    'UPLOAD_MAX_SIZE' => 3145728000,// 设置附件上传大小
    'UPLOAD_PATH' => './Uploads/', // 设置附件上传目录
    'UPLOAD_IMG_EXTS' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型

    /****定时配置  更改得重新发起*****/
    'IGNORE_S' => 1,

    /**********引入配置文件************/
    'LOAD_EXT_CONFIG' => array('code','myconfig'),  
);
return array_merge($config);
?>
