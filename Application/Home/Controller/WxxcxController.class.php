<?php
/*
 * 小程序公共类
 */
namespace Home\Controller;
use Think\Controller;
class WxxcxController extends Controller{
	//获取openid  并且 入库
	public function getopenid(){
		$code=$_POST['code'];
		$status = C("CODE_STATUS");
		if(!$code){
		    $result['code']=10008;
		    $result['error']=$status['10008'];
		    $this->ajaxReturn($result);
		    exit;
		}
		
		if(!$_POST['userInfo']){
			$result['msg']=$status['10005'];
			$result['code']=10005;
			$this->ajaxReturn($result);
			exit;
		}
		$userInfo=json_decode($_POST['userInfo'],true);
		$return=$this->getOAuth($code);
		if(!$return['session_key']){
			$result['msg']='getOAuth函数报错:'.$return;
			$result['code']=0;
			$this->ajaxReturn($result);
			exit;
		}
		$userMod=M('user');
		$exist=$userMod->where(array('openid'=>$return['openid']))->find();
		if($exist){
			if($exist['unionid'] == null || $exist['unionid'] == ''){
				
				$save=array(
					'unionid'=>$return['unionid'] ? $return['unionid'] : '',
					'nickname'=>base64_encode($userInfo['userInfo']['nickName']),
					'sex'=>$userInfo['userInfo']['gender'],
					'headimgurl'=>$userInfo['userInfo']['avatarUrl'],
					'session_key'=>$return['session_key'],
					'updatetime'=>time()
				);
				$is_save=$userMod->where(array('openid'=>$return['openid']))->data($save)->save();
			}
		}else{
			$unionexist=$userMod->where(array('unionid'=>$return['unionid']))->find();
			if($unionexist){
				
				$save=array(
					'openid'=>$return['openid'],
					'nickname'=>base64_encode($userInfo['userInfo']['nickName']),
					'sex'=>$userInfo['userInfo']['gender'],
					'headimgurl'=>$userInfo['userInfo']['avatarUrl'],
					'session_key'=>$return['session_key'],
					'updatetime'=>time()
				);
				
				$is_save=$userMod->where(array('unionid'=>$return['unionid']))->data($save)->save();
			}else{
			    //获得配置的登陆草莓数
                $registerScore = getScoreConfig('register');
				$add=array(
					'unionid'=>$return['unionid'] ? $return['unionid'] :'',
					'openid'=>$return['openid'],
					'nickname'=>base64_encode($userInfo['userInfo']['nickName']),
					'sex'=>$userInfo['userInfo']['gender'],
					'headimgurl'=>$userInfo['userInfo']['avatarUrl'],
					'session_key'=>$return['session_key'],
					'createtime'=>time(),
				    'strawberry'=>$registerScore,
				);
				$is_add=$userMod->data($add)->add();
				if($is_add)
				{
				    $logData = array();
				    $logData['openid'] = $return['openid'];
				    $logData['type'] = 1;
				    $logData['strawberry'] = $registerScore;
				    $logData['create_time'] = time();
				    M("bounds_log")->add($logData);//添加登录积分奖励
				}
			}
			
		}
		if($is_save||$is_add){
			$userInfo=$userMod->where(array('openid'=>$return['openid']))->find();
			session('session_key',$return['session_key']);
			session('session_openid',$return['openid']);
			$sessionid = session_id();
			$data['unionid']=$return['unionid'];
			$data['session_key']=session('session_key');
			$data['session_id']=$sessionid;
			$data['openid']=session('session_openid');
			$data['expires']=time()+$return['expires_in'];
			$result['code']=10000;
			$result['msg'] = $status['10000'];
			$result['data'] = $data;
		}else{
			$result['msg'] = $status['10001'];
			$result['code'] = 10001;
		}
		$this->ajaxReturn($result);
	}
	
	//发送code授权
	private function getOAuth($code){
	    $wechatConf = C("wechat_conf");
		$url='https://api.weixin.qq.com/sns/jscode2session?appid='.$wechatConf['appid'].'&secret='.$wechatConf['appsecret'].'&js_code='.$code.'&grant_type=authorization_code';
		$return=https_request($url, null);
		return $return;
	}

    public function getwxaqrcode()
    {
        $access_token = $this->AccessToken();
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;
        $path="pages/index/index";
        $width=430;
        $data='{"path":"'.$path.'","width":'.$width.'}';
        $return = $this->request_post($url,$data);
        //将生成的小程序码存入相应文件夹下
        file_put_contents('./Public/wximg/'.time().'.jpg',$return);
    }
    public function AccessToken()
    {
        $wechatConf = C("wechat_conf");
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$wechatConf['appid'].'&secret='.$wechatConf['appsecret'];
        $AccessToken = $this->request_post($url);
        $AccessToken = json_decode($AccessToken , true);
        $AccessToken = $AccessToken['access_token'];
        return $AccessToken;
    }
    public function request_post($url, $data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
        }
    }

}
	
	
	function object_array($data){
		if(is_object($data)) {
			$data = (array)$data;
		} if(is_array($data)) {
			foreach($data as $key=>$value) {
				$data[$key] = object_array($value);
			}
		}
		return $data;
	}

