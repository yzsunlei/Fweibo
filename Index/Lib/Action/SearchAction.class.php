<?php
/**
* ����������
*/
Class SearchAction extends CommonAction{
    /**
     * ��������
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
            
            import("ORG.Util.Page");// �����ҳ��
            $count = $db->where($where)->count('id');
            $page  = new Page($count,4);
            $limit = $page->firstRow.','.$page->listRows;
            $result = $db->where($where)->field($field)->limit($limit)->select();

            //������Ͻ�������õ��Ƿ��ѹ�ע�����ע
            $result = $this->_getMutual($result);
            //���������������ͼ
            $this->result = $result?$result:false;
            //ҳ��
            $this->page = $page->show();
        }
        
        $this->keyword = $keyword;
        $this->display();
    }
    
    /**
     * ���������ؼ��� ˽�з���
     */
    Private function _getKeyword(){
        return  $_GET['keyword'] == '����΢��������' ? NULL : $this->_get('keyword');
    }
    
    /**
     * ������Ͻ�����õ��Ƿ����ע���Ƿ��ѹ�ע
     * @param [Array] $result [��Ҫ����Ľ����]
     * @return [Array]  [������ɺ�Ľ����]
     */
    Private function _getMutual($result){   //�е㸴��
        if(!$result) return false;
        
        $db = M('follow');
        
        foreach($result as $k => $v){
            //�Ƿ��໥��ע
            $sql='(select `follow` from `hd_follow` where `follow` ='.$v['uid'].' and `fans` = '.session('uid').') union (select `follow` from `hd_follow` where `follow` = '.session('uid').'and `fans` ='.$v['uid'].')';
            $mutual = $db->query($sql);
            
            if(count($mutual) == 2){
                $result[$k]['mutual'] = 1;
                $result[$k]['followed'] = 1;
            }else{
                $result[$k]['mutual'] = 0;
                //δ�����עʱ�����Ƿ��ѹ�ע
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