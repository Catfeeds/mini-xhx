<?php 
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller{
    private $statusConf;
    
    public function __construct(){
        $this->statusConf = C('CODE_STATUS');
    }
    /**
     * 获取用户信息
     * **/
    public function getInfo(){
        $openId = I("GET.openid");
        $result = array();
        if(!$openId){
              $result['code'] = 10009;
              $result['msg'] = $this->statusConf['10009'];
        }else{
            $info = M('user')->where(array('openid'=>$openId))->find();
            if(!$info){
                $result['code'] = 10003;
                $result['msg'] = $this->statusConf['10003'];
            }else{
                $result['code'] = 10000;
                $result['msg'] = $this->statusConf['10000'];
                $info['nickname'] = base64_decode($info['nickname']);
                $info['sex'] = $info['sex'] == 1 ? "男" : "女";
                $info['cityText'] = $info['province'] && $info['city'] && $info['area'] ? $info['province']." ".$info['city']." ".$info['area']  : "请 选 择";
                $info['birthDay'] = $info['birth_day'] ? $info['birth_day'] : '请选择日期';
                $result['data'] = $info;
            }
    }
        return $this->ajaxReturn($result);
    }

    /**
     * 我的评论列表
     * */
    public function getMyCommentList(){
        $openId = I("GET.openid");
        if(!$openId){
            $result['code'] = 10009;
            $result['msg'] = $this->statusConf['10009'];
        }else{
            $userInfo = M('user')->where(array('openid'=>$openId))->find();
            if(!$userInfo){
                $result['code'] = 10003;
                $result['msg'] = $this->statusConf['10003'];
            }else{
                //获取评论列表
                $commentList = M("comment")->where(array('openid'=>$openId))->select();
                $list = array();
                $i=0;
                foreach ($commentList as $k=>$v){
                    $articleInfo = M("article")->where(array('id'=>$v['a_id']))->find();
                    if(!$articleInfo){
                        continue;
                    }
                    $list[$i]['content'] = $v['c_content'];
                    $list[$i]['nickname'] = base64_decode($userInfo['nickname']);
                    $list[$i]['headimgurl'] = $userInfo['headimgurl'];
                    $list[$i]['createtime'] = formatDate($v['c_create_time']);
                    $list[$i]['articletitle'] = $articleInfo['title'];
                    $list[$i]['articleimg'] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$articleInfo['img']; 
                    $list[$i]['aid'] = $articleInfo['id'];
                    $i++;
                }
                $result['code'] = 10000;
                $result['msg'] = $this->statusConf['10000'];
                $result['data'] = $list;
                
            }
        }
        return $this->ajaxReturn($result);
    }
    
    /**
     * 添加收货地址
     * **/
    public function addAddress(){
        $param = I("POST.");
        $param['area'] = trim($param['area']);
        $result = array();
        if(empty($param['openid']) || empty($param['name']) || empty($param['tel']) || empty($param['area']) || empty($param['address'])){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
        }else{
            $count = M("address")->where($param)->count();
            if($count){
                $result['code'] = 10007;
                $result['msg'] = $this->statusConf['10007'];
            }else{
                $param['create_time'] = time();
                $flag = M('address')->add($param);
                if(!$flag){
                    $result['code'] = 10001;
                    $result['msg'] = $this->statusConf['10001'];
                }else{
                    $result['code'] = 10000;
                    $result['msg'] = $this->statusConf['10000'];
                }
            }
        }
        
        return $this->ajaxReturn($result);
    }
    
    /**
     * 获取收货地址
     * **/
    public function getAddressInfo(){
        $id = I("GET.id");
        if(!$id){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
        }else{
            $info = M("address")->where(array('id'=>$id))->find();
            if(!$info){
                $result['code'] = 10006;
                $result['msg'] = $this->statusConf['10006'];
            }else{
                $result['code'] = 10000;
                $result['msg'] = $this->statusConf['10000'];
                $result['data'] = $info;
            }
        }
        return $this->ajaxReturn($result);
    }
    
    //编辑收货地址
    public function editAddress(){
        $param = I("POST.");
        if(!$param['id']){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
        }else{
               M('address')->save($param);
               $result = array(
                'code' => 10000,
                'msg' => $status['10000'],
            );
        }
        
        return $this->ajaxReturn($result);
    }
    
    //获取我的地址列表
    public function getMyAddress(){
        $openId = I("GET.openid");
        if(!$openId){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
        }else{
            $list = M('address')->where(array("openid"=>$openId))->order('is_default desc')->select();
            if(!$list){
                $result['code'] = 10003;
                $result['msg'] = $this->statusConf['10003'];
            }else{
                $result['code'] = 10000;
                $result['msg'] = $this->statusConf['10000'];
                $result['data'] = $list;
            }
        }
        return $this->ajaxReturn($result);
    }
    //设置默认收货地址
    public function setDefault(){
        $id = I("POST.id");
        $openId = I("POST.openid");
        $info = M('address')->where(array('id'=>$id))->find();
        $result = array();
        if(!$info){
            $result['code'] = 10006;
            $result['msg'] = $this->statusConf['10006'];
        }else{
            M("address")->where(array('openid'=>$openId))->save(array('is_default'=>0));
            M("address")->where(array('id'=>$id))->save(array('is_default'=>1));
            $result['code'] = 10000;
            $result['msg'] = $this->statusConf['10000'];
        }
        return $this->ajaxReturn($result);
    }
    
    //删除地址
    public function delAddress(){
        $id = I("POST.id");
        $openId = I("POST.openid");
        $info = M('address')->where(array('id'=>$id))->find();
        $result = array();
        if(!$info){
            $result['code'] = 10006;
            $result['msg'] = $this->statusConf['10006'];
        }else{
            M("address")->where(array('id'=>$id))->delete();
            $result['code'] = 10000;
            $result['msg'] = $this->statusConf['10000'];
        }
        return $this->ajaxReturn($result);
    }
    
    /**
     * 生成订单
     * **/
    public function confirmOrder(){
       $gid = I("POST.gid");
       $openId = I("POST.openid");
       $message = I("POST.message");
       $addressId = I("POST.addressid");
       $num = I("POST.num") ? I("POST.num") : 1;
       $goodInfo = M("goods")->where(array('g_id'=>$gid))->find();
       $addressInfo = M("address")->where(array('id'=>$addressId))->find();
       $result = array();
       if(!$goodInfo || !$addressInfo){
           $result['code'] = 10006;
           $result['msg'] = $this->statusConf['10006'];
       }else{
//           $count = M("orders")->where(array('openid'=>$openId,'gid'=>$gid))->count();
//           if($count){
//               $result['code'] = 10010;
//               $result['msg'] = $this->statusConf['10010'];
//           }else{
                  $data = array();
                  $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
                  $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
                  $data['sn'] = $orderSn;
                  $data['gid'] = $gid;
                  $data['linkname'] = $addressInfo['name'];
                  $data['linktel'] = $addressInfo['tel'];
                  $data['linkaddress'] = $addressInfo['area'].$addressInfo['address'];
                  $data['strawberry'] = $data['peach'] = 0;
                  $userInfo = M("user")->where(array('openid'=>$openId))->find();
                  if($goodInfo['g_peach'] > 0 ){
                      if($userInfo['peach'] - $goodInfo['g_peach']*$num < 0){
                          $result['code'] = 10011;
                          $result['msg'] = $this->statusConf['10011'];
                          return $this->ajaxReturn($result);
                      }
                      $data['peach'] = $goodInfo['g_peach'];
                  }else{
                      if($userInfo['strawberry'] - $goodInfo['g_strawberry']*$num < 0){
                          $result['code'] = 10013;
                          $result['msg'] = $this->statusConf['10013'];
                          return $this->ajaxReturn($result);
                      }
                      $data['strawberry'] = $goodInfo['g_strawberry'];
                  }
                  $data['create_time'] = time();
                  $data['openid'] = $openId;
                  $data['message'] = $message;
                  $data['num'] = $num;
                  $data['totalstrawberry'] = $data['strawberry']*$num;
                  $data['totalpeach'] = $data['peach']*$num;
                  $flag = M('orders')->add($data);
                  if($flag){
                      //添加草莓扣除记录
                      $logData = array();
                      $logData['openid'] = $openId;
                      $logData['type'] = 7;
                      $logData['peach'] = $data['totalpeach'];
                      $logData['strawberry'] = $data['totalstrawberry'];
                      $logData['create_time'] = time();
                      $logData['is_increase'] = 2;
                      if(M("bounds_log")->add($logData)){
                          //更新用户账户的水蜜桃和草莓数量
                          if( $data['totalpeach'] > 0 ){
                              M("user")->where(array('openid'=>$openId))->setDec("peach", $data['totalpeach']);
                          }else{
                              M("user")->where(array('openid'=>$openId))->setDec("strawberry",$data['totalstrawberry']);
                          }
                      }
                      //更新产品销量
                      M("goods")->where(array('g_id'=>$gid))->setInc('g_sale_num',$num);
                      
                      $result['code'] = 10000;
                      $result['msg'] = $this->statusConf['10000'];
                  }else{
                      $result['code'] = 10002;
                      $result['msg'] = $this->statusConf['10002'];
                  }
//           }
       }
       
       return $this->ajaxReturn($result);
    }
    
     //签到
     public function sign(){
         $openId = I("GET.openid");
         $result = array();
         if(!$openId){
             $result['code'] = 10003;
             $result['msg'] = $this->statusConf['10003'];
         }else{
             $startTime = strtotime(date("Y-m-d"));
             $endTime = strtotime(date("Y-m-d"))+86400;
             $count = M("bounds_log")->where(array('type'=>3,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->count();
             if($count){
                 $result['code'] = 10012;
                 $result['msg'] = $this->statusConf['10012'];
             }else{
                 $startTime = strtotime(date("Y-m-d"))-86400;
                 $endTime = strtotime(date("Y-m-d"));
                 $logInfo = M("bounds_log")->where(array('type'=>3,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->find();
                 $step = 1;
                 if($logInfo){
                     $step = $logInfo['step'] + 1;
                 }
                 $strawberry = 0;
                 $peach=0;
                 $signConfig = getScoreConfig('sign');
                 $i = $step%7;
                 switch($i){
                     case 1:
                         $strawberry = $signConfig[0];
                         break;
                     case 2:
                         $strawberry = $signConfig[1];
                         break;
                     case 3:
                         $strawberry = $signConfig[2];
                         break;
                     case 4:
                         $strawberry = $signConfig[3];
                         break;
                     case 5:
                         $strawberry = $signConfig[4];
                         break;
                     case 6:
                         $strawberry = $signConfig[5];
                         break;
                     case 7:
                         $strawberry = $signConfig[6];
                         break;
                 }

                 $logData = array();
                 $logData['openid'] = $openId;
                 $logData['type'] = 3;
                 $logData['strawberry'] = $strawberry;
                 $logData['peach'] = $peach;
                 $logData['create_time'] = time();
                 $logData['step'] = $step;
                 if(M("bounds_log")->add($logData))//添加登录积分奖励
                 {
                     M("user")->where(array('openid'=>$openId))->setInc('strawberry',$strawberry);
                 }
                 $result['code'] = 10000;
                 $result['msg'] = $this->statusConf['10000'];
             }
         }
         
         return $this->ajaxReturn($result);
     }
     
     //获取签到的积分
     public function getSignScore(){
         $openId = I("GET.openid");
         $userInfo = M("user")->where(array('openid'=>$openId))->find();
         $startTime = strtotime(date("Y-m-d"));
         $endTime = strtotime(date("Y-m-d"))+86400;
         $logInfo = M("bounds_log")->field('step,strawberry,peach')->where(array('type'=>3,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->find();
         $totalScore = M("bounds_log")->where(array('type'=>3,'openid'=>$openId))->sum("strawberry");
         if(!$logInfo){
             $startTime = strtotime(date("Y-m-d"))-86400;
             $endTime = strtotime(date("Y-m-d"));
             $logInfo = M("bounds_log")->field('step,strawberry,peach')->where(array('type'=>3,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->find();
             if(!$logInfo){
                 $logInfo['step'] = 0;
             }
             $logInfo['strawberry'] = 0;
             $logInfo['peach'] = 0;
         }
         $logInfo['totalScore'] = intval($totalScore);
         $logInfo['userImg'] = $userInfo ? $userInfo['headimgurl'] : "";
         $logInfo['config'] = getScoreConfig();
         $result['code'] = 10000;
         $result['msg'] = $this->statusConf['10000'];
         $result['data'] = $logInfo;
         return $this->ajaxReturn($result);
     }
     
     //获取当天获取的积分
     public function getTodayScore(){
        error_reporting(E_ALL);
        ini_set("display_errors","on");
         $openId = I("GET.openid");
         $startTime = strtotime(date("Y-m-d"));
         $endTime = strtotime(date("Y-m-d"))+86400;
         $totalScore = M("bounds_log")->where(array('openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
         $loginScore = M("bounds_log")->where(array('type'=>1,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
         $inviteScore = M("bounds_log")->where(array('type'=>2,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
         $signScore = M("bounds_log")->where(array('type'=>3,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
         $shareScore = M("bounds_log")->where(array('type'=>4,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
         $commentScore = M("bounds_log")->where(array('type'=>5,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
         $scoreList['totalScore'] = $totalScore ? $totalScore : 0;
         $scoreList['loginScore'] = $loginScore ? $loginScore : 0;
         $scoreList['inviteScore'] = $inviteScore ? $inviteScore : 0;
         $scoreList['signScore'] = $signScore ? $signScore : 0;
         $scoreList['shareScore'] = $shareScore ? $shareScore : 0;
         $scoreList['commentScore'] = $commentScore ? $commentScore : 0;
         $result['code']=10000;
         $result['msg'] = $this->statusConf['10000'];
         $res['scoreList'] = $scoreList;
         $res['scoreConfig'] = getScoreConfig();
         $result['data'] = $res;
         return $this->ajaxReturn($result);
     }
     
     //邀请好友返积分
     public function share(){
         $openId = I("GET.openid");
         $otherOpenId = I('GET.other');
         $invite = getScoreConfig('invite');
         if($openId&&$otherOpenId){//邀请返积分
             if(!M('bounds_log')->where(array('type'=>2,'other_openid'=>$openId))->count()){//没被人邀请
                 if(!M("bounds_log")->where(array('type'=>2,'openid'=>$otherOpenId,'other_openid'=>$openId))->count()){
                     $logData = array();
                     $logData['openid'] = $otherOpenId;
                     $logData['type'] = 2;
                     $logData['strawberry'] = $invite;
                     $logData['create_time'] = time();
                     $logData['other_openid'] = $openId;
                     if(M("bounds_log")->add($logData)){
                         M("user")->where(array('openid'=>$otherOpenId))->setInc('strawberry',$invite);
                     }
                 }
             }
           }
         $result['code']=10000;
         $result['msg'] = $this->statusConf['10000'];
         return $this->ajaxReturn($result);
     }
     
     //积分兑换
     public function change(){
         $openId = I("GET.openid");
         $in = I("GET.num");
         $result = array();
         if(!$openId || !$in){
             $result['code'] = 10003;
             $result['msg'] = $this->statusConf['10003'];
         }else{
             $rate = getScoreConfig('rate');
             $out = $rate*$in;//计算最后扣除的草莓数量
             $userInfo = M("user")->where(array('openid'=>$openId))->find();
             if($out>$userInfo['strawberry']){
                 $result['code'] = 10013;
                 $result['msg'] = $this->statusConf['10013'];
             }else{
                 //添加草莓兑换水蜜桃记录
                 $logData = array();
                 $logData['openid'] = $openId;
                 $logData['type'] = 6;
                 $logData['peach'] = $in;
                 $logData['strawberry'] = $out;
                 $logData['create_time'] = time();
                 $logData['is_increase'] = 2;
                 $logData['rate'] = $rate;
                 if(M("bounds_log")->add($logData)){
                     M("user")->where(array('openid'=>$openId))->setDec('strawberry',$out);
                     M("user")->where(array('openid'=>$openId))->setInc('peach',$in);
                 }
                 $result['code']=10000;
                 $result['msg'] = $this->statusConf['10000'];
             }
         }
         return $this->ajaxReturn($result);
     }
     /**
     获取草莓和水蜜桃账户的变动情况
      **/
     public function getScoreDetail(){
         $scoreType = C('score_type');
         $openId = I("GET.openid");
         if(!$openId){
             $result['code'] = 10003;
             $result['msg'] = $this->statusConf['10003'];
         }else{
             $userInfo = M("user")->field("strawberry,peach")->where(array('openid'=>$openId))->find();
             $result = array();
             $res = array();
             $res['userInfo'] = $userInfo;
             $i=0;
             if(!$userInfo){
                 $result['code'] = 10006;
                 $result['msg'] = $this->statusConf['10006'];
             }else{
                 $list = M("bounds_log")->where(array('openid'=>$openId))->order('id desc')->select();
                 foreach ($list as $k=>$v){
                     $detailList[$i]['type'] =$scoreType[$v['type']];
                     $detailList[$i]['createTime'] = date("Y-m-d",$v['create_time']);
                     $str = '';
                     if($v['is_increase'] == 1){
                            if($v['strawberry']){
                                $str .='+'.$v['strawberry'].'草莓';
                            }elseif($v['peach']){
                                $str .= '+'.$v['peach'].'水蜜桃';
                            }
                     }else{
                            if($v['type'] == 6){
                                $str .= '-'.$v['strawberry'].'草莓,+'.$v['peach'].'水蜜桃';
                            }elseif($v['type'] == 7){
                                if($v['strawberry']){
                                    $str .='-'.$v['strawberry'].'草莓';
                                }elseif($v['peach']){
                                    $str .= '-'.$v['peach'].'水蜜桃';
                                }
                            }elseif($v['type'] == 8){
                                $str .= '-'.$v['strawberry'].'草莓';
                            }
                     }
                     $detailList[$i]['detail'] = $str;
                     $i++;
                 }
                 $res['config'] = getScoreConfig();
                 $res['detailList'] = $detailList;
                 $result['code'] = 10000;
                 $result['msg'] = $this->statusConf['10000'];
                 $result['data'] = $res;
             }
         }
         return $this->ajaxReturn($result);
     }

     //更新会员信息
    public function updateUser(){
         $openId = I("POST.openid");
         $name = I("POST.name");
         $mobile = I("POST.mobile");
         $address = I("POST.address");
         $birthDay = I("POST.birthday");
         $result = array();
         if(!$openId){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
         }else{
            //更新用户信息
             $update = array();
             $update['name'] = $name;
             $update['mobile'] = $mobile;
             $update['birth_day'] = $birthDay;
             $update['birth_time'] = strtotime("1970-".substr($birthDay,5));
             if($address){
                 $addressArr = explode(" ",$address);
                 $update['province'] =$addressArr[0];
                 $update['city'] = $addressArr[1];
                 $update['area'] = $addressArr[2];
             }
             M('user')->where(array('openid'=>$openId))->save($update);
             $result['code'] = 10000;
             $result['msg'] = $this->statusConf['10000'];
         }
         return $this->ajaxReturn($result);
    }
}

?>