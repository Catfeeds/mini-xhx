<?php 
namespace Admin\Controller;
use Think\Controller;
class OrdersController extends AdminController {
    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 订单列表
     * **/
    public function index(){
        $ordersModel=M('orders');
        $data = I('get.');
        empty($data['status'])? : $where['status'] = array('eq',$data['status']);
        $where['type'] = 1;
        $count=$ordersModel->where($where)->count();
        $Page=new \Think\Page($count,20);//实例化分页类
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show=$Page->show();//分页显示输出
        $list = $ordersModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $k=>$v){
            $goodInfo = M("goods")->where(array('g_id'=>$v['gid']))->find();
            $list[$k]['g_name'] = $goodInfo['g_name'];
            $list[$k]['g_img'] = $goodInfo['g_img'];
        }
        $this->assign('page',$show);//赋值分页输出
        $this->assign('list',$list);//赋值数据集
        $this->assign('where',$where);
        $this->display();
    }
    
    public function add(){
        $id = I("get.id");
        $ordersMod =  M("orders");
        if($_POST){
                $data['delivery'] = $_POST['delivery'];
                $data['delivery_code'] = $_POST['deliverycode'];
                $data['status'] = $_POST['status'];
                $data['update_time'] = time();
                $flag = $ordersMod->where(array('id'=>$id))->save($data);
             if($flag){
                $this->success("操作成功",U('Orders/index'));
             }else{
                $this->error("操作失败");
             }
        }else{
            $info = $ordersMod->where(array('id'=>$id))->find();
            $this->assign('info',$info);
        }
        $this->display();
    }
    
    public function del(){
        $id = I("get.id");
        $info = M('orders')->where(array('id'=>$id))->find();
        if(!$info){
            $this->error('订单不存在');
        }else{
            $flag = M('orders')->where(array('id'=>$id))->delete();
            if($flag){
                M('goods')->where(array('g_id'=>$info['gid']))->setDec('g_sale_num');
            }
            $this->success("操作成功",U('Orders/index'));
        }
    }
}

?>