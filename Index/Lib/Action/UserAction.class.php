<?php
/**
 * 用户个人主页视图
 */
Class UserAction extends CommonAction{
    //首页主视图
    Public function index(){
        P($_GET);
    }
    /**
     * 空操作
     */
    Public function _empty($name){
        $this_>_getUrl($name);
    }
    /**
     * 处理用户名空操作，获得用户ID 跳转至用户主页
     */
    Private function _getUrl($name){
        $name = htmlspecialchars($name);
        $where = array('username',$name);
        $uid = M('userinfo')->where($where)->getField('uid');
        
        if(!$uid){
            redirect(U('Index/index'));
        }else{
            redirect(U('index',array('id'=>$uid)));
        }
    }
    
}


?>