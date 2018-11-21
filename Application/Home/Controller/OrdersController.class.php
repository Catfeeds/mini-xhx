<?php 
namespace Home\Controller;
use Think\Controller;
/**
 * 每日必读列表
 * **/
class OrdersController extends Controller {
    private $statusConf;
    public function __construct(){
        $this->statusConf = C('STATUS_CODE');
    }
    
    /**
     * 订单列表
     * **/
    public function index(){
        $openId = I("GET.openid");
        $status = I("GET.status");
        $result =  array();
        if(!$openId){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
        }else{
            $where['openid'] = $openId;
            if($status){
                $where['status'] = $status;
            }
            $list = M("orders")->field('id,gid,sn,status,strawberry,peach,num,totalstrawberry,totalpeach')->where($where)->order('id desc')->select();
            foreach ($list as $k=>$v){
                $goodInfo = M("goods")->where(array('g_id'=>$v['gid']))->find();
                $list[$k]['goodName'] = $goodInfo['g_name'];
                $list[$k]['img'] =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$goodInfo['g_img'];
                $list[$k]['status'] = $v['status'] == 1 ? '完成' : ($v['status'] == 2 ? '等待发货' : '已经发货');
            }
            $result['code'] = 10000;
            $result['msg'] = $this->statusConf['10000'];
            $result['data'] = $list;
        }
        return $this->ajaxReturn($result);
    }
    
    /**
     * 订单详情
     * **/
    public function orderInfo(){
        $id = I("GET.id");
        $info = M("orders")->where(array('id'=>$id))->find();
        if(!$info){
            $result['code'] = 10006;
            $result['msg'] = $this->statusConf['10006'];
        }else{
            $goodInfo = M("goods")->where(array('g_id'=>$info['gid']))->find();
            $info['goodName'] = $goodInfo['g_name'];
            $info['img'] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$goodInfo['g_img'];
            $result['code'] = 10000;
            $result['msg'] = $this->statusConf['10000'];
            $result['data'] = $info;
        }
        return $this->ajaxReturn($result);
    }
    
    /**
     * 确认收货
     * **/
    public function confirm(){
        $id = I("POST.id");
        $info = M("orders")->where(array('id'=>$id))->find();
        $result = array();
        if(!$info){
            $result['code'] = 10006;
            $result['msg'] = $this->statusConf['10006'];
        }else{
            $flag = M("orders")->where(array('id'=>$id))->save(array('status'=>1));
            if($flag){
                $result['code'] = 10000;
                $result['msg'] = $this->statusConf['10000'];
            }else{
                $result['code'] = 10001;
                $result['msg'] = $this->statusConf['10001'];
            }
        }
        
        return $this->ajaxReturn($result);
    }
}

?>