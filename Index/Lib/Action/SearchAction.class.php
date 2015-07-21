<?php
/**
* 搜索控制器
*/
Class SearchAction extends CommonAction{
    /**
     * 搜索找人
     */
    Public function sechUser(){
        $keyword = $this->_getKeyword();
        if($keyword){
            $where = array(
                'username' => array('LIKE','%'.$keyword.'%'),
                'uid' => array('NEQ',session('uid')),
                );
            $field = array('username','sex','location','intro','face80','follow','fans','weibo','uid');
            $db = M('userinfo');
            
            import("ORG.Util.Page");// 导入分页类
            $count = $db->where($where)->count('id');
            $page  = new Page($count,4);
            $limit = $page->firstRow.','.$page->listRows;
            $result = $db->where($where)->field($field)->limit($limit)->select();

            //重新组合结果集，得到是否已关注或互相关注
            $result = $this->_getMutual($result);
            //分配搜索结果到视图
            $this->result = $result?$result:false;
            //页码
            $this->page = $page->show();
        }
        
        $this->keyword = $keyword;
        $this->display();
    }
    
    /**
     * 返回搜索关键字 私有方法
     */
    Private function _getKeyword(){
        return  $_GET['keyword'] == '搜索微博、找人' ? NULL : $this->_get('keyword');
    }
    
    /**
     * 重新组合结果集得到是否互相关注与是否已关注
     * @param [Array] $result [需要处理的结果集]
     * @return [Array]  [处理完成后的结果集]
     */
    Private function _getMutual($result){   //有点复杂
        if(!$result) return false;
        
        $db = M('follow');
        
        foreach($result as $k => $v){
            //是否相互关注
            $sql='(select `follow` from `hd_follow` where `follow` ='.$v['uid'].' and `fans` = '.session('uid').') union (select `follow` from `hd_follow` where `follow` = '.session('uid').'and `fans` ='.$v['uid'].')';
            $mutual = $db->query($sql);
            
            if(count($mutual) == 2){
                $result[$k]['mutual'] = 1;
                $result[$k]['followed'] = 1;
            }else{
                $result[$k]['mutual'] = 0;
                //未互相关注时检索是否已关注
                $where = array(
                    'follow' =>$v['uid'],
                    'fans' => session('uid')
                    );
                $result[$k]['followed']= $db->where($where)->count();
            }
        }
        return $result;
    }
}
?>