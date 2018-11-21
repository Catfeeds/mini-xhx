<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model {
	protected $_validate    =   array(
        array('name','require','帐号必须填'),
		array('name','','帐号名称已经存在！',0,'unique','1'), // 验证账户名是否已经存在
		array('password','1,16','密码长度不正确',2,'length'), // 验证密码是否在指定长度范围
		array('repassword','password','确认密码不正确',0,'confirm',1), // 新增的时候验证确认密码是否和密码一致
		array('tel','checktel','手机号不对',1,'callback'),
    );
	
	protected $_auto = array ( 
        array('status','0'),  // 新增的时候把status字段设置为1
		array('password','get_password',3,'callback'), // 对name字段在新增和编辑的时候回调getName方法
        array('token','get_token',1,'callback'), // 对name字段在新增和编辑的时候回调getName方法
        array('addtime','time',1,'function'), // 对update_time字段在更新的时候写入当前时间戳
	);
	 
	public function get_password(){
		$password = I('post.password');
		$authcode = C('AUTHCODE_ADMIN');
		$id = I('post.id');
		if($id){
			if($password == '' || $password == null){
				$info = M('admin')->where(array('id'=>$id))->find();
				return $info['password'];
			}else{
				return md5(md5($password.$authcode));
			}
		}else{
			if($password){
				return md5(md5($password.$authcode));
			}else{
				return md5(md5('123456'.$authcode));
			}
		}
		
	}
	
	public function checktel(){
		$tel = I('post.tel');
		return check_tel($tel);
	}
	 
	function get_token($randLength=6,$attatime=1,$includenumber=0){
		if ($includenumber){
			$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
		}else {
			$chars='abcdefghijklmnopqrstuvwxyz';
		}
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLength;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$tokenvalue=$randStr;
		if ($attatime){
			$tokenvalue=$randStr.time();
		}
		return $tokenvalue;
	}
}

?>

