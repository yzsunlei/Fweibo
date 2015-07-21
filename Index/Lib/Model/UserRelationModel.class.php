<?php
/**
 * �û����û���Ϣ��Ĺ���ģ��
 */
 Class UserRelationModel extends RelationModel{
    //������������
    Protected $tableName = 'user';
    
    //�����û����û���Ϣ�������ϵ����
    Protected $_link = array(
            'userinfo' => array(
                    'mapping_type' => HAS_ONE,
                    'foreign_key' => 'uid'
                )
        );
        
    /**
     * �Զ����������
     */
    Public function insert($data = NULL){
        $data = is_null($data)?$_POST : $data;
        return $this->relation(true)->data($data)->add();
    }
 }
?>