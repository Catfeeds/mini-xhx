<?php
namespace Admin\Controller;
class RootController extends AdminController {
	
	public function _initialize()
    {
		parent::_initialize();
	}
	
	//账户列表
    public function userlist(){
		$admin=M('admin');//实例化admin模型
    	$count=$admin->count();//查询满足要求的总记录数
    	$Page=new \Think\Page($count,10);//实例化分页类 传入总记录数和每页显示的记录数(10)
    	$Page->setConfig('prev', '上一页');
    	$Page->setConfig('next', '下一页');
    	$show=$Page->show();//分页显示输出
    	$list = $admin->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key =>$val){
			$access = M('auth_group_access')->where(array('uid'=>$val['id']))->find();
			$group = M('auth_group')->where(array('id'=>$access['group_id']))->find();
			$list[$key]['group_id'] = $group['title'];
		}
		$this->assign('page',$show);//赋值分页输出
    	$this->assign('list',$list);//赋值数据集
    	$this->display();//输出模板
	}
	//
	public function useradd(){
		$id = I('get.id');
		if(IS_POST){
			if($id){
				$_POST['id'] = $id;
				$db=D('admin');
				if(!$db->create()){
					$this->error($db->getError());
				}else{
					$sid=$db->save();
					$group_access = M('auth_group_access')->where(array('uid'=>$id))->find();
	
					$group['uid'] = $id;
					$group['group_id'] = $_POST['group_id'];
					if($group_access){
						$access = M('auth_group_access')->where(array('uid'=>$id))->save($group);
					}else{
						$access = M('auth_group_access')->add($group);
					}
					if($id==true || $access == true){
						$this->success('操作成功',U(CONTROLLER_NAME.'/userlist'));
					}else{
						$this->error('操作失败',U(CONTROLLER_NAME.'/userlist'));
					}
				}
			}else{
				$db=D('admin');
				if(!$db->create()){
					$this->error($db->getError());
				}else{
					$sid=$db->add();
					$group_access = M('auth_group_access')->where(array('uid'=>$id))->find();
	
					$group['uid'] = $sid;
					$group['group_id'] = $_POST['group_id'];
					if($group_access){
						$access = M('auth_group_access')->where(array('uid'=>$id))->save($group);
					}else{
						$access = M('auth_group_access')->add($group);
					}
					if($sid==true || $access == true){
						$this->success('操作成功',U(CONTROLLER_NAME.'/userlist'));
					}else{
						$this->error('操作失败',U(CONTROLLER_NAME.'/userlist'));
					}
				}
			}
		}else{
			$info = M('admin')->where(array('id'=>$id))->find();
			$access = M('auth_group_access')->where(array('uid'=>$info['id']))->find();
			$info['group_id'] = $access['group_id'];
			$group = M('auth_group')->where(array('status'=>1))->select();
			$agency = M('admin')->where(array('type'=>2,'status'=>1))->field('id,name')->select();
			$this->assign('info',$info);
			$this->assign('group',$group);
			$this->assign('agency',$agency);
			$this->display();
		}
	
	}
	
	
	public function usersta(){
		$id = I('get.id');
		$info = M('admin')->where(array('id'=>$id))->find();
		if($info['status'] == 1){
			$savedata['status'] = 0;
		}else{
			$savedata['status'] = 1;
		}
		$sid =M('admin')->where(array('id'=>$id))->save($savedata);
		if($sid){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	
	//账户组列表
	public function grouplist(){
		$auth_group=M('auth_group');//实例化auth_group模型
		$count=$auth_group->count();//查询满足要求的总记录数
		$Page=new \Think\Page($count,10);//实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('prev', '上一页');
		$Page->setConfig('next', '下一页');
		$show=$Page->show();//分页显示输出
		$list = $auth_group->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);//赋值分页输出
		$this->assign('list',$list);//赋值数据集
		$this->display();//输出模板
	}
	

	public function groupadd(){
		$id = I('get.id');
		if(IS_POST){
			if($_POST['title'] == ''){
				$this->error('组标题必须填');
			}
			//$_POST['module'] = 'Admin';
			if($_POST['module'] == ''){
				$this->error('模块必须填');
			}
//			$_POST['type'] = 1;
			$rulelist = I('post.rule');
			foreach($rulelist as $key=>$val){
				if($key == 0 ){
					$_POST['rules'] .= $val;
				}else{
					$_POST['rules'] .= ','.$val;
				}
			}
			if($id){
				$_POST['id'] = $id;
				/*$Db=D('Auth_group');
					if(!$Db->create($_POST)){
					$this->error($Db->getError());
					}else{
					$sid=$Db->save();
					if($sid==true){
					$this->success('操作成功',U(CONTROLLER_NAME.'/grouplist'));
					}else{
					$this->error('操作失败',U(CONTROLLER_NAME.'/grouplist'));
					}
					}*/
				$this->save('Auth_group','Root/grouplist');
			}else{
				$this->insert('Auth_group','Root/grouplist');
			}
		}else{
			$authrule = M('auth_rule');
			$rulelist = $authrule->where(array('status'=>1,'pid'=>0))->select();
			foreach($rulelist as $k=>$v){
				$rulelist[$k]['sublist'] = $authrule->where(array('status'=>1,'pid'=>$v['id']))->select();
			}
			$this->assign('rulelist',$rulelist);
			$info = M('auth_group')->where(array('id'=>$id))->find();
			$this->assign('info',$info);
			$this->display();
		}
	}
	
	public function groupsta(){
		$id = I('get.id');
		$info = M('auth_group')->where(array('id'=>$id))->find();
		if($info['status'] == 1){
			$savedata['status'] = 0;
		}else{
			$savedata['status'] = 1;
		}
		$sid =M('auth_group')->where(array('id'=>$id))->save($savedata);
		if($sid){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	
	public function groupdel(){
		$id = I('get.id');
		$sid =M('auth_group')->where(array('id'=>$id))->delete();
		if($sid){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	//权限列表
	public function rulelist(){
		$auth_rule=M('auth_rule');//实例化auth_rule模型
		$count=$auth_rule->count();//查询满足要求的总记录数
		$Page=new \Think\Page($count,10);//实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('prev', '上一页');
		$Page->setConfig('next', '下一页');
		$show=$Page->show();//分页显示输出
		$list = $auth_rule->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);//赋值分页输出
		$this->assign('list',$list);//赋值数据集
		$this->display();//输出模板
	}
	
	
	public function ruleadd(){
		$id = I('get.id');
		if(IS_POST){
			if($_POST['name'] == ''){
				$this->error('关联的权限必须填');
			}
			if($_POST['title'] == ''){
				$this->error('标题必须填');
			}
			if($_POST['module'] == ''){
				$this->error('模块必须填');
			}
			$_POST['type'] = 1;
			\Think\Log::write(date('Y:m:d H:i:s').'|权限添加数据1|'.json_encode($_POST),'WARN');
			if($id){
				$_POST['id'] = $id;
				$this->save('auth_rule','Root/rulelist');
			}else{
				$this->insert('auth_rule','Root/rulelist');
			}
		}else{
			$info = M('auth_rule')->where(array('id'=>$id))->find();
			$pinfo = M('auth_rule')->where(array('pid'=>0))->select();
			$this->assign('info',$info);
			$this->assign('pinfo',$pinfo);
			$this->display();
		}
	}
	
	public function rulesta(){
		$id = I('get.id');
		$info = M('auth_rule')->where(array('id'=>$id))->find();
		if($info['status'] == 1){
			$savedata['status'] = 0;
		}else{
			$savedata['status'] = 1;
		}
	
		$sid =M('auth_rule')->where(array('id'=>$id))->save($savedata);
		if($sid){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	
}
	