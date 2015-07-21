<?php
/**
 * ���ÿ�����
 */
 Class CommonAction extends Action{
    //�Զ����з���
    Public function _initialize(){
        //�����Զ���½
        if(isset($_COOKIE['auto']) && !isset($_SESSION['uid'])){
            $value = explode('|',encryption($_COOKIE['auto'],1));
            $ip = get_client_ip();
            
            //���ε�¼IP����һ�ε�¼IPһ��ʱ
            if($ip == $value[1]){
                $account  = $value[0];
                $where = array('account'=>$account);
                
                $user = M('user')->where($where)->field(array('id','lock'))->find();
                //�������û���Ϣ���Ҹ��û�û�б�����ʱ�������¼״̬
                if($user && !$user['lock']){
                    session('uid',$user['id']);
                }
            }
        }
        
        //�ж��û��Ƿ��ѵ�½
        if(!isset($_SESSION['uid'])){
            redirect(U('Login/index'));
        }
    }
    
    /**
     * ͷ���ϴ�
     */
    Public function uploadFace(){
        if(!$this->isPost()){
            halt('ҳ�治����!');
        }
        $upload = $this -> _upload('Face','180,80,50','180,80,50');
        echo json_encode($upload);
    }
    
    /**
     * ΢��ͼƬ�ϴ�
     */
    Public function uploadPic(){
        if(!$this->isPost()){
            halt('ҳ�治����!');
        }
        $upload = $this->_upload('Pic','800,380,120','800,380,120');
        echo json_encode($upload);
    }
    
    
    /**
     * �첽�����·���
     */
    Public function addGroup(){
        if(!$this->isAjax()){
            halt('ҳ�治���ڣ�');
        }
        $name = $this->_post('name');
        $data = array(
            'name' => $this->_post('name'),
            'uid' => session('uid')
            );
        if(M('group')->data($data)->add()){
            echo json_encode(array('status'=>1,'msg' => '����ɹ�'));
        }else{
            echo json_encode(array('status'=>0,'msg' => '����ʧ��'));
        }
    }
    
    /**
     * �첽��ӹ�ע
     */
    Public function addFollow(){
        if(!$this->isAjax()){
            halt('ҳ�治���ڣ�');
        }
        $data = array(
            'follow' => $this->_post('follow','intval'),
            'fans' => (int)session('uid'),
            'gid' => $this-> _post('gid','intval')
            );
        if(M('follow')->data($data)->add()){
            $db = M('userinfo');
            $db->where(array('uid' => $data['follow']))->setInc('fans');
            $db->where(array('uid' => session('uid')))->setInc('follow');
            echo json_encode(array('status'=>1,'msg' => '��ע�ɹ�'));
        }else{
            echo json_encode(array('status'=>0,'msg' => '��עʧ��,������..'));
        }
    }
    
    /**
     * ͼƬ�ϴ�����
     * @param [String] $path [�����ļ�������]
     * @param [String] $width [����ͼ��ȶ���ã��ŷָ�]
     * @param [String] $height [����ͼ�߶ȶ���ã��ŷָ�(Ҫ����һһ��Ӧ)]
     * @return [Array]         [ͼƬ�ϴ�·��]
     */
    Public function _upload($path,$width,$height){
        import('ORG.Net.UploadFile');   //����ThinkPHP�ļ��ϴ���
        $obj = new UploadFile();    //ʵ�����ϴ���
        $obj->maxSize = C('UPLOAD_MAX_SIZE');   // ͼƬ����ϴ���С
        $obj->savePath = C('UPLOAD_PATH').$path.'/';    //  ͼƬ����·��
        $obj->saveRule = 'uniqid';  // �����ļ���
        $obj->uploadReplace = true ;    //  ����ͬ���ļ�
        $obj->allowExts = C('UPLOAD_EXTS'); //�����ϴ��ļ��ĺ�׺��
        $obj->thumb = true; // ��������ͼ
        $obj->thumbMaxWidth = $width;   //  ����ͼ���
        $obj->thumbMaxHeight = $height;     //����ͼ�߶�
        $obj->thumbPrefix = 'max_,medium_,mini_';   //     �����ϴ��ļ���ǰ׺��
        $obj->thumbPath = $obj->savePath.date('Y_m').'/';   //����ͼ����·��
        $obj->thumbRemoveOrigin = true; //ɾ��ԭͼ
        $obj->autoSub = true ;  //  ʹ����Ŀ¼����
        $obj->subType = 'date';     //ʹ������Ϊ��Ŀ¼����
        $obj->dateFormat = 'Y_m';   //ʹ�� ��_�� ��ʽ
        
        if(!$obj->upload()){
            return array('status'=>0,'msg'=>$obj->getErrorMsg());
        }else{
            $info=$obj->getUploadFileInfo();
            $pic = explode('/',$info[0]['savename']);
            return array(
                'status'=>1,
                'path'=>array(
                    'max' => $pic[0].'/max_'.$pic[1],
                    'medium' =>  $pic[0].'/medium_'.$pic[1],
                    'mini' =>  $pic[0].'/mini_'.$pic[1],
                    )
                );
        }
    }
    
 }
 
?>