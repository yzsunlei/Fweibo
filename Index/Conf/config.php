<?php
return array(
	//数据库配置
    'DB_HOST' => 'localhost',   //数据库服务器地址
    'DB_USER' => 'root',    //数据库用户名
    'DB_PWD' => '123456', //数据库密码
    'DB_NAME' => 'fweibo',   //数据库名称
    'DB_PREFIX' => 'hd_',   //数据库表前缀
    
    'DEFAULT_THEME' => 'default',   //默认主题
    'URL_MODULE' => 1,  //URL访问模式
    'TOKEN_ON' => false,    //      关闭令牌功能
    
    'ENCRYPTION_KEY' => 'www.hundunwang.com', //用于异位或加密的KEY
    'AUTO_LOGIN_TIME' => time() + 3600*24*7,    //一个星期
    
    //图片上传
    'UPLOAD_MAX_SIZE' => 2000000,   //最大上传大小
    'UPLOAD_PATH' => './Uploads/',  //  文件上传路径
    'UPLOAD_EXTS' => array('jpg','jpeg','png','gif'),   //允许上传文件后缀
    
    'URL_ROUTE_ON' => 'true',       //  路由规则
    'URL_ROUTE_RULES' => array(
        ':id\d' => 'User/index'  
        ),       //定义路由规则
);
?>