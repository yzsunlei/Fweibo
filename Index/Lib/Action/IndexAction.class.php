<?php
/**
 * 首页控制器
 */
 Class IndexAction extends CommonAction{
    /**
     * 首页视图
     */
    Public function index(){        
        $db = D('WeiboView');
        //载入分页类
        import('Org.Util.Page');
        
        //取得当前用户的ID与当前用户所有关注好友的ID
        $uid = array(session('uid'));
        $where = array('fans'=>session('uid'));
        $result = M('follow')->field('follow')->where($where)->select();
        if($result){
            foreach($result as $v){
                $uid[] = $v['follow'];
            }
        }
        
        //组合WHERE条件，条件为当前用户自身的ID与当前用户所有关注好友的ID
        $where = array('uid'=>array('IN',$uid));
        
        //统计数据总条数，用于分页
        $count = $db->where($where)->count('*');
        $page = new Page($count,3);
        $limit = $page->firstRow . ',' . $page->listRows;
        
        //读取所有微博
        $result = $db->getAll($where,$limit);
        $this->weibo = $result;
        $this->page = $page->show();
        $this->display();
    }
    
    /**
     * 微博发布处理
     */
    Public function sendWeibo(){
        if(!$this->isPost()){
            halt('页面不存在！');
        }
        $data = array(
            'content' =>$this->_post('content'),
            'time' =>time(),
            'uid' => session('uid')
            );
        if($wid = M('weibo')->data($data)->add()){
            if(!empty($_POST['max'])){
                $img = array(
                    'max' => $this->_post('max'),
                    'medium' => $this->_post('medium'),
                    'mini' => $this->_post('mini'),
                    'wid' => $wid
                    );
                M('picture')->data($img)->add();
            }
            M('userinfo')->where(array('uid'=>session('uid')))->setInc('weibo');
            $this->success('发布成功！',U('index'));
        }else{
            $this->error('发布失败！请重试..');
        }
    }
    
    /**
     * 转发weibo
     */
    Public function turn(){
        if(!$this->isPost()){
            halt('页面不存在！');
        }
        //原微博ID
        $id = $this->_post('id','intval');
        $content =  $this->_post('content');
        //p($_POST);die;
        
        $data = array(
            'content' => $content,
            'isturn' => $tid ? $tid :$id ,
            'time' => time(),
            'uid' => session('uid')
        );
        //插入数据到微博表
        $db = M('weibo');
        if($db->data($data)->add()){
            $db->where(array('id' => $id))->setInc('turn');
            if($tid){
                $db->where(array('id' => $tid))->setInc('turn');
            }
            
            M('userinfo') -> where (array('uid'=>session('uid')))->setInc('weibo');
            
            //如果点击了同时评论插入内容到评论表
            if(isset($_POST['becomment'])){
                //提取插入数据
                $data = array(
                    'content' => $content,
                    'isturn' => $id,
                    'time' => time(),
                    'uid' => session('uid')
                    );
                //插入评论数据后给原微博评论次数加1
                if(M('comment')->data($data)->add()){
                    $db->where(array('id'=>$id))->setInc('comment');
                }
            }
            $this->success('转发成功！','index');
        }else{
            $this->error('转发失败，请重试..');
        }
    }
    
    /**
     * 退出登录处理
     */
    Public function loginOut(){
        //卸载session
        session_unset();
        session_destroy();
        //删除用于自动登录的COOKIE
        @setcookie('auto','',time()-3600,'/');
        //跳转至登录页
        redirect(U('Login/index'));
    }
    
    
    Public function getMsg(){
        echo 111;
    }
 }
?>