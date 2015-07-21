<?php
/**
 * �˺�����
 */
Class UserSettingAction extends CommonAction{
    /**
     * �û���Ϣ
     */
    Public function index(){
        $where=array('uid'=>session('uid'));
        $field = array('username','truename','sex','location','constellation','intro','face180');
        $user = M('userinfo')->field($field)->find();
        header('Content-Type:text/html;charset=utf-8');
        $this->user = $user;
        $this->display();
    }
    /**
     * �޸��û�������Ϣ
     */
    Public function editBasic(){
        if(!$this->isPost()){
            halt('ҳ�治����!');
        }
        header('Content-type:text/html;charset=utf-8');
        $data = array(
                'username'=>$this->_post('nickname'),
                'truename'=>$this->_post('truename'),
                'sex'=>$this->_post('sex'),
                'location'=>$this->_post('province').' '.$this->_post('city'),
                'constellation'=>$this->_post('night'),
                'intro'=>$this->_post('intro')
            );
        $where = array('uid'=>session('uid'));
        if(M('userinfo')->where($where)->save($data)){
            $this->success('�޸ĳɹ���',U('index'));
        }else{
            $this->error('�޸�ʧ�ܣ�');
        }
    }
    /**
     * �޸��û�ͼ��
     */
    Public function editFace(){
        if(!$this->isPost()){
            halt('ҳ�治����!');
        }
        $db = M('userinfo');
        $where = array('uid' => session('uid'));
        $field = array('face50','face80','face180');
        $old = $db->where($where)->field($field)->find();
        
        if($db->where($where)->save($_POST)){
            if(!empty($old['face180'])){
                @unlink('./Uploads/Face/'.$old['face180']);
                @unlink('./Uploads/Face/'.$old['face80']);
                @unlink('./Uploads/Face/'.$old['face50']);
            }
            $this->success('�޸ĳɹ���',U('index'));
        }else{
            $this->error('�޸�ʧ�ܣ�������..');
        }   
    }
    /**
     * �޸��û�����
     */
    Public function editPwd(){
        if(!$this->isPost()){
            halt('ҳ�治���ڣ�');
        }
        $db = M('user');
        //��֤������
        $where = array('id'=>session('uid'));
        $old = $db->where($where)->getField('password');
        if($this->_post('old','md5') != $old){
            $this->error('���������');
        }
        if($this->_post('new')!=$this->_post('newed')){
            $this->error('�������벻һ�£�');
        }
        $newPwd = $this->_post('new','md5');
        $data = array(
            'id' => session('uid'),
            'password' => $newPwd
            );
        if($db->save($data)){
            $this->success('�޸ĳɹ���',U('index'));
        }else{
            $this->error('�޸�ʧ�ܣ�������..');
        }
        
    }
    
}
?>