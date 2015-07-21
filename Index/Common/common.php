<?php
/**
 * 格式化打印数组
 */
 function p($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
 }
/**
 * 异位或加密字符串
 * @param [string] $value [需要加密的字符串]
 * @param [integer] $type [加密解密 (0:加密，1：解密)]
 * @return [string] [加密或解密后的字符串]
 */
 function encryption($value,$type=0){
    $key = md5(C('ENCRYPTION_KEY'));
    if(!$type){
        return str_replace('=','',base64_encode($value ^ $key));
    }
    $value = base64_decode($value);
    return $value ^ $key;
 }
 
 /**
  * 格式化时间戳
  */
 function time_format($time){
    //当前时间
    $now = time();
    $today = strtotime(date('y-m-d'),$now);
    //传递时间与当前时秒相差的秒数
    $diff = $now -$time;
    $str = '';
    switch($time){
        case $diff < 60:
            $str = $diff . '秒前';
            break;
        case $diff < 3600:
            $str = floor($diff/60).'分钟前';
            break;
        case $diff <(3600*8):
            $str = floor($diff/3600).'小时前';
            break;
        case $time>$today:
            $str = '今天&nbsp;&nbsp;'.date('H:i',$time);
            break;
        default:
            $str = date('Y-m-d H:i:s',$time);
    }
    return $str;
 }
 
 /**
  * 替换微博内容URL地址，@用户与表情
  */
 function replace_weibo($content){
   //$content = '[呵呵]后端网地址：http://www.houduan.com/?wqw=wqetet&eafs=klea @孙鳄梨 你是[亲亲]';
   if(empty($content)) return;
    
    //给URL地址加上正则<a>链接
    $preg  = '/(?:http:\/\/)?([\w.]+[\w\/]*\.[\w.]+[\w\/]*\??[\w=\%\&\+]*)/is';
    $content = preg_replace($preg,'<a href="http://\\1" target="_blank">\\1</a>',$content);
    
    //给@用户加<a>链接
    $preg = '/@(\S+)\s/is';
    $content = preg_replace($preg,'<a href="'.__APP__.'/User/index/uname/\\1">\\1</a>',$content);
    
    //提取微博内容里的所有表情文件
    $preg = '/\[(\S+)*\]/is';
    preg_match_all($preg,$content,$arr);
    
    //载入表情包
    $phiz = include './Public/Data/phiz.php';
    if(!empty($arr[1])){
        foreach($arr[1] as $k => $v){
            $name = array_search($v,$phiz);
            if($name){
                $content = str_replace($arr[0][$k],'<img src="' .__ROOT__. '/PUBLIC/Images/phiz/'.$name.'.gif" title="'.$v.'"/>',$content);
            }
        }
    }
    
    return  $content;
 }
 
 
 
 
 
 
 
 
 
?>