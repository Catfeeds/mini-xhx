<?php
namespace Admin\Controller;
use Think\Controller;
class CronController extends Controller{
    // 每年1月1日-6月30日期间获得草莓在当年8月31日过期, 每年7月1日-12月30日期间获得草莓在当年2月28日过期
    // 1月1日--12月31日期间获得的积分需在次年1月31日前（含当日）兑换完成，逾期清零。
    public function doExpScore(){
        ignore_user_abort(true);
        set_time_limit(0);
        do{
            file_put_contents( $publicPath = $_SERVER['DOCUMENT_ROOT']."/Public/bb.txt",time()."\n",FILE_APPEND);
            $this->expScore();
            sleep(86400);
        }while(true);
    }

    public function expScore(){
        set_time_limit(0);
        $year = date("Y");
//        if(date("m-d") == "08-31"){
//            $startTime = strtotime($year."-01-01");
//            $endTime = strtotime($year."-06-31");
//            $this->doExp($startTime,$endTime);
//        }elseif(date("m-d") == "02-28"){
//            $startTime = strtotime(($year-1)."-07-01");
//            $endTime = strtotime(($year-1)."-12-30");
//            $this->doExp($startTime,$endTime);
//        }
        if(date("m-d") == "02-01"){
            $startTime = strtotime(($year-1)."-01-01");
            $endTime = strtotime(($year-1)."-12-31");
            $this->doExp($startTime,$endTime);
        }
    }

    public function doExp($startTime,$endTime){
        set_time_limit(0);
        $page=0;
        $pageSize=5000;
        while(true){
            $limit = "$page,$pageSize";
            $list = M("bounds_log")->where(array('is_increase'=>1))->where("create_time>=".$startTime." and create_time<".$endTime)->group("openid")->limit($limit)->field('sum(strawberry) as sumstrawberry,openid')->select();
            if(!$list){
                break;
            }
            $page += $pageSize;
            foreach ($list as $k=>$v){
                     //获得水蜜桃总数量
                    $totalPeach = M("bounds_log")->where(array('is_increase'=>1,'openid'=>$v['openid']))->where("create_time>=".$startTime." and create_time<".$endTime)->sum('peach');
                    //兑换获得水蜜桃数量
                    $changePeach = M("bounds_log")->where(array('type'=>6,'openid'=>$v['openid']))->where("create_time>=".$startTime." and create_time<".$endTime)->sum('peach');
                    //兑换消耗草莓的数量
                    $changeStrawberry = M("bounds_log")->where(array('type'=>6,'openid'=>$v['openid']))->where("create_time>=".$startTime." and create_time<".$endTime)->sum('strawberry');
                    //兑换商品消耗草莓数量
                    $exChangeStrawberry = M("bounds_log")->where(array('type'=>7,'openid'=>$v['openid']))->where("create_time>=".$startTime." and create_time<".$endTime)->sum('strawberry');
                    //兑换商品消耗的水蜜桃数量
                    $exChangePeach = M("bounds_log")->where(array('type'=>7,'openid'=>$v['openid']))->where("create_time>=".$startTime." and create_time<".$endTime)->sum('peach');
                    $leftStrawberry = 0;//实际没使用的草莓数量
                    $leftPeach = 0;//实际没使用的水蜜桃数量
                    
                    
                    $userInfo = M("user")->where(array('openid'=>$v['openid']))->find();
                    if($v['sumstrawberry'] > ($changeStrawberry+$exChangeStrawberry)){
                        $leftStrawberry = $v['sumstrawberry'] - ($changeStrawberry+$exChangeStrawberry);
                        $leftStrawberry = $leftStrawberry > $userInfo['strawberry'] ? $userInfo['strawberry'] : $leftStrawberry;
                    }
                    var_dump($leftStrawberry);
                    if($totalPeach+$changePeach-$exChangePeach > 0){
                        $leftPeach = $totalPeach+$changePeach-$exChangePeach;
                        $leftPeach = $leftPeach > $userInfo['peach'] ? $userInfo['peach'] : $leftPeach;
                    }
                    if($leftStrawberry || $leftPeach){
                        $logData = array();
                        $logData['openid'] = $v['openid'];
                        $logData['type'] = 8;
                        $logData['strawberry'] = $leftStrawberry;
                        $logData['peach'] = $leftPeach;
                        $logData['create_time'] = time();
                        $logData['is_increase'] = 2;
                        if(M("bounds_log")->add($logData)){
                            M("user")->where(array('openid'=>$v['openid']))->setDec('strawberry',$leftStrawberry);
                            M("user")->where(array('openid'=>$v['openid']))->setDec('peach',$leftPeach);
                        }
                    }
            }
        }
    }
    public function doSendBirthScore(){
        ignore_user_abort(true);
        set_time_limit(0);
        do{
            file_put_contents( $publicPath = $_SERVER['DOCUMENT_ROOT']."/Public/cc.txt",time()."\n",FILE_APPEND);
            $this->sendBirthScore();
            sleep(86400);
        }while(true);
    }
    //生日送积分
    public function sendBirthScore(){
        $birthScore = getScoreConfig('birth');
        if(!$birthScore){
            return 0;
        }else{
            //获取当天生日的用户
            $offset = 0;
            $pageSize = 1000;
            while (true){
                $date = date("m-d");
                $list = M('user')->where(array('birth_time'=>strtotime("1970-".$date)))->limit($offset,$pageSize)->select();
                if(!$list){
                    break;
                }
                foreach ($list as $k=>$v){
                    if(!M("bounds_log")->where(array('openid'=>$v['openid'],'type'=>9))){
                        $logData = array();
                        $logData['openid'] = $v['openid'];
                        $logData['type'] = 9;
                        $logData['strawberry'] = $birthScore;
                        $logData['create_time'] = time();
                        $logData['is_increase'] = 1;
                        if(M("bounds_log")->add($logData)){
                            M("user")->where(array('openid'=>$v['openid']))->setDec('strawberry',$birthScore);
                        }
                    }

                }
                $offset += $pageSize;
            }
        }
    }
}
?>