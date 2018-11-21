<?php 
namespace Admin\Controller;
use Think\Controller;
class UserController extends AdminController {
    public function _initialize()
    {
        parent::_initialize();
    }
    //会员列表
    public function index(){
        $userModel=M('user');
        $data = I('get.');
        empty($data['nickname'])? : $where['nickname'] = array('like','%'.base64_encode($data['nickname']).'%');
        empty($data['is_special'])?:$where['is_special']=$data['is_special'];
        empty($data['openid'])?:$where['openid']=$data['openid'];
        $orderType = 1;
        if(isset($data['order'])){
            if($data['order'] == 1){
                $order = ' strawberry desc ';
                $orderType = 2;
            }else if($data['order'] == 2){
                $order = ' strawberry asc ';
                $orderType = 1;
            }
        }else{
            $order = ' id desc ';
        }
        $count=$userModel->where($where)->count();
        $Page=new \Think\Page($count,20);//实例化分页类
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show=$Page->show();//分页显示输出
        $list = $userModel->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);//赋值分页输出
        foreach ($list as $k=>$v){
            $list[$k]['nickname'] = base64_decode($v['nickname']);
        }
        $this->assign('list',$list);//赋值数据集
        $this->assign('where',$data);
        //总用户数量
        $total['userCount'] = M("user")->count();
        //今日新增用户数
        $startTime = strtotime(date("Y-m-d"));
        $endTime = time();
        $total['todayUser'] = M("user")->where("createtime>=".$startTime." and createtime<".$endTime)->count();
        $this->assign('total',$total);
        $this->assign('orderType',$orderType);
        $this->display();
    }
    public function editisSpecial(){
        $id = I("POST.id");
        $isSpecial = I("POST.is_special");
        if(M('user')->where(array('id'=>$id))->save(array('is_special'=>$isSpecial))){
            $rest['code'] = 10000;
            $rest['msg'] = '修改成功';
            $this->ajaxReturn($rest);
            exit();
        }else{
            $rest['code'] = 10001;
            $rest['msg'] = '修改失败';
            $this->ajaxReturn($rest);
            exit();
        }
    }
    
    public function score(){
        $boundsLogModel=M('bounds_log');
        $data = I('get.');
        empty($data['openid']) ? : $where['openid'] = $data['openid'];
        $count=$boundsLogModel->where($where)->count();
        $Page=new \Think\Page($count,20);//实例化分页类
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show=$Page->show();//分页显示输出
        $list = $boundsLogModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);//赋值分页输出
        $scoreType = C('score_type');
        foreach ($list as $k=>$v){
            $list[$k]['type'] =$scoreType[$v['type']];
            $list[$k]['createTime'] = date("Y-m-d H:i:s",$v['create_time']);
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
            $list[$k]['detail'] = $str;
        }
        $this->assign('list',$list);//赋值数据集
        $this->assign('where',$data);
        $this->display();
    }
}

?>