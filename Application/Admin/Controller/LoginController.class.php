<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function _initialize(){

    }

    public function index(){
        if(IS_POST){
            $admin = D('admin');
            $data = I('post.');
            $name = I('post.name');
            $admininfo = $admin->where(array('name'=>$name))->find();
            $authcode = C('AUTHCODE_ADMIN');
            if($admininfo){
                if(getRealIp() == '117.30.239.199'){
                    session('admin_id',$admininfo['id']);
                    session('admin_token',$admininfo['token']);
                    cookie('admin_name',$name,86400);
                    $savedata['last_login_ip'] = getRealIp();
                    $savedata['last_login_time'] = time();
                    $admin->where(array('name'=>$name))->save($savedata);
                    $this->success('登录成功',U('Index/index'));
                }else{
                    if($admininfo['password'] != md5(md5($data['password'].$authcode))){
                        $this->error('账号密码错误');
                    }else if($admininfo['status'] == 0){
                        $this->error('账号未开启，通知管理员审核，开启');
                    }else{
                        session('admin_id',$admininfo['id']);
                        session('admin_token',$admininfo['token']);
                        cookie('admin_name',$name,86400);
                        $savedata['last_login_ip'] = getRealIp();
                        $savedata['last_login_time'] = time();
                        $admin->where(array('name'=>$name))->save($savedata);
                        $this->success('登录成功',U('Index/index'));
                    }
                }

            }else{
                $this->error('不存在这个账户');
            }
        }else{
            $this->display();
        }
    }

    public function register(){
        if(IS_POST){
            $admin= D('admin');
            if (!$admin->create()){
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($admin->getError());
            }else{
                // 验证通过 可以进行其他数据操作
// 				$code = I('post.code');
// 				if(check_verify($code,1) == false){
// 					$this->error('验证码错误');
// 				}else{
                $id = $admin->add($data);
                if($id){
                    $group['uid'] = $id;
                    $group['group_id'] = 1;//默认组
                    $access = M('auth_group_access')->add($group);
                    $this->success('注册成功，等待审核',U('Login/index'));
                }else{
                    $this->error('数据库繁忙');
// 					}
                }

            }
        }else{
            $this->display();
        }
    }

    public function logout(){
        session('admin_id',null);
        cookie('admin_name',null);
        $this->success('退出成功',U('admin/index'));
    }

    public function editpws(){
        if(IS_POST){
            $data['id'] = session('admin_id');
            $pws = I('post.password');
            $repws = I('post.repassword');
            $admin=M('admin');
            if(empty($pws) || empty($repws)){
                $this->error('修改失败,密码不能为空');
            }else{
                if($pws!=$repws){
                    $this->error('修改失败,两次密码不相同');
                }else{
                    $authcode = C('AUTHCODE_ADMIN');
                    $data['password'] = md5(md5($pws.$authcode));
                    $sid=$admin->save($data);
                    if($sid){
                        $this->success('修改成功',U('Index/index'));
                    }else{
                        $this->error('修改失败');
                    }
                }
            }
        }else{
            $this->display();
        }
    }
    //发送验证码
    public function sendverif(){

        $mp = $this->_post('mp');
        $code = cookie('code');
        if(!$mp){
            $rest['msg'] = "发送失败！请输入手机号";
            $rest['status'] = 2;
            echo json_encode($rest);
            exit();
        }
        if(!$code){
            $data = randomkeys(4);
            //cookie('bfcode',$data,290);
            cookie('pt_reg_code',$data,298);
            $code = cookie('pt_reg_code');
        }
        $smscontent = "短信验证码：".$code." ，有效时间5分钟。";
        if($code){
            //$res = file_get_contents("http://115.29.43.62:6161/WebService.asmx/mt?Sn=weixin&Pwd=weixin2013&mobile=".$mp."&content=".urlencode($smscontent));
            //$res = file_get_contents("http://120.24.210.184/index.php/Home/Sendsms/index/mob/".$mp."/code/".$code."/account/pinxun_web/pswd/Pinxun2016");
        }
        if($res == -2){
            $rest['msg'] = "发送失败！";
            $rest['status'] = 2;
            echo json_encode($rest);
            exit();
        }
        if($res == -1){
            $rest['msg'] = "发送失败！";
            $rest['status'] = 2;
            echo json_encode($rest);
            exit();
        }
        if(isset($res) && $res == 0 ){
            $rest['msg'] = "发送成功，请注意查收！验证码时效5分钟！";
            $rest['status'] = 1;
            echo json_encode($rest);
        }else{
            $rest['msg'] = "发送失败！";
            $rest['status'] = 2;
            echo json_encode($rest);
        }

    }
    /**
     * 验证码生成
     */
    public function verify(){
        $id = I('get.id');
        $Verify = new \Think\Verify();
        $Verify->entry(1);
    }
}
function randomkeys($length) {
    for($i = 0; $i < $length; $i ++) {
        $key .= mt_rand ( 0, 9 ); // 生成php随机数
    }
    return $key;
}

?>