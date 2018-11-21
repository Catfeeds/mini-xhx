<?php 
namespace Home\Controller;
use Think\Controller;

class GoodsController extends Controller{
    private $statusConf;
    
    public function __construct(){
        $this->statusConf = C("CODE_STATUS");
    }
    /**
     * 商品列表
     * **/
    public function getGoodsList(){
        $goodsList = M("goods")->field('g_id,g_name,g_img,g_sale_num,g_strawberry,g_peach')->order('g_id desc')->select();
        $result = array();
        if(!$goodsList){
            $result['code'] = 10006;
            $result['msg'] = $this->statusConf['10006'];
        }else{
            $list = array();
            foreach ($goodsList as $k=>$v){
                $list[$k]['id'] = $v['g_id'];
                $list[$k]['name'] = $v['g_name'];
                $list[$k]['img'] =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$v['g_img'];
                $list[$k]['saleNum'] = $v['g_sale_num'];
                $list[$k]['strawberry'] = $v['g_strawberry'];
                $list[$k]['peach'] = $v['g_peach'];
            }
            //广告图
            $advImg = M('carousel_images')->where(array('type'=>2,'status'=>1))->find();
            $advImg['imageUrl'] = $advImg['img'] ? $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$advImg['img'] : '';
            $result['code'] = 10000;
            $result['msg'] = $this->statusConf['10000'];
            $result['data'] = array('goodsList'=>$list,'advImg'=>$advImg);
        }
        return $this->ajaxReturn($result);
    }
    /**
     * 商品详情
     * **/
    public function getGoodInfo(){
        $id = I("GET.id");
        $openId = I("GET.openid");
        $result = array();
        if(!$id){
            $result['code'] = 10003;
            $result['msg'] = $this->statusConf['10003'];
        }else{
            $info = M("goods")->where(array('g_id'=>$id))->find();
            if(!$info){
                $result['code'] = 10006;
                $result['msg'] = $this->statusConf['10006'];
            }else{
                $goodInfo = array();
                $goodInfo['id'] = $info['g_id'];
                $goodInfo['name'] = $info['g_name'];
                $goodInfo['img'] =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$info['g_img'];
                $goodInfo['saleNum'] = $info['g_sale_num'];
                $goodInfo['strawberry'] = $info['g_strawberry'];
                $goodInfo['peach'] = $info['g_peach'];
                $goodInfo['desc'] = $info['g_desc'];
                $address = array();
                $userInfo = array();
                if($openId)
                {
                    $address = M("address")->where(array('openid'=>$openId,'is_default'=>1))->find();
                    if(!$address){
                        $address = M("address")->where(array('openid'=>$openId))->find();
                    }
                    
                    $userInfo = M("user")->field("strawberry,peach")->where(array('openid'=>$openId))->find();
                }
                $data['address'] = $address;
                $data['userInfo'] = $userInfo;
                $data['goodInfo'] = $goodInfo;
                $result['code'] = 10000;
                $result['msg'] = $this->statusConf['10000'];
                $result['data'] = $data;
            }
        }
        return $this->ajaxReturn($result);
    }
}

?>