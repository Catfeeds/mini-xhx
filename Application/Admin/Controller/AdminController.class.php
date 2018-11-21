<?php

namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {

    Public function _initialize(){
        // 初始化的时候检查用户权限
        date_default_timezone_set('PRC');
        $host = 'http://'.$_SERVER['HTTP_HOST'];
        $this->host = $host;
        $this->assign('webhost',$this->host);
        $this->adminname = cookie('admin_name');
        if(!isset($this->adminname) || session('admin_id')==''){
            $this->redirect('Admin/Login/index');
        }
        $admin = M('admin')->where(array('id'=>session('admin_id')))->find();
        $this->admin = $admin;
        $this->admin['groupid'] = M('auth_group')->where(array('uid'=>$admin['id']))->find('group_id');
        $this->assign('admin',$this->admin);
        $this->assign('adminname',$this->adminname);
        cookie('admin_name',$this->adminname,86400);
        $admin_id = session('admin_id');
        session('admin_id',$admin_id);
        $auth = new \Think\Auth;
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        if (session('admin_id') != 1 && !$auth->check($url, session('admin_id'))) {
            $this->error('你没有权限');
        }
    }

    public function save($name,$url =''){
        $Db=D($name);
        if(!$Db->create($_POST)){
            $this->error($Db->getError());
        }else{
            $sid=$Db->save();
            if($sid==true){
                $this->success('操作成功',U($url));
            }else{
                $this->error('操作失败',U($url));
            }
        }
    }

    public function insert($name,$url =''){
        $Db=D($name);
        if(!$Db->create($_POST)){
            $this->error($Db->getError());
        }else{
            \Think\Log::write(date('Y:m:d H:i:s').'|权限添加数据2|'.json_encode($_POST),'WARN');
            $sid=$Db->add();
            if($sid==true){
                $this->success('操作成功',U($url));
            }else{
                $this->error('操作失败',U($url));
            }
        }
    }

    public function save_status($name,$id,$url =''){
        $Db=D($name);
        if(!$id){
            $this->error('数据不存在');
        }else{
            $status = $Db->where(array('id'=>$id))->getField('status');
            if($status == 1){
                $sid=$Db->where(array('id'=>$id))->setField('status',0);
            }else{
                $sid=$Db->where(array('id'=>$id))->setField('status',1);
            }
            if($sid==true){
                $this->success('操作成功',U($url));
            }else{
                $this->error('操作失败',U($url));
            }
        }
    }

}

?>