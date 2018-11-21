<?php 
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends AdminController{
    /**
     * 商品列表
     * **/    
    public function index(){
        $goodsModel=M('goods');
        $data = I('get.');
        empty($data['name'])? : $where['g_name'] = array('like','%'.$data['name'].'%');
        $count=$goodsModel->where($where)->count();
        $Page=new \Think\Page($count,20);//实例化分页类
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show=$Page->show();//分页显示输出
        $list = $goodsModel->where($where)->field('g_id,g_name,g_img,g_sale_num,g_strawberry,g_peach')->order('g_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);//赋值分页输出
        $this->assign('list',$list);//赋值数据集
        $this->assign('where',$where);
        $this->display();
    }
    
    public function add(){
        $id = I("get.id");
        $goodsMod =  M("Goods");
        $info = $goodsMod->where(array('g_id'=>$id))->find();
        if($_POST){
            if(!empty($_FILES['g_img']['tmp_name'])){
                $upload = new \Think\Upload(); // 实例化上传类
                $upload->maxSize   = 314572800; // 设置附件上传大小
                $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = 'article/'.$this->token.'/';
                $uploadinfo   =   $upload->upload();
                if (!$uploadinfo) {
                    $this->error($upload->getError());
                }else {// 上传成功 获取上传文件信息
                    foreach($uploadinfo as $k=>$file){
                        $img = "/Uploads/".$file['savepath'].$file['savename'];
                        $data[$k] = empty($file['savename']) ? $info[$k] : $img;
                    }
                }
            }else{
                $data['g_img'] = $info['g_img'];
            }
            if($id){
                $data['g_name'] = $_POST['name'];
                $data['g_desc'] = $_POST['editorValue'] ? $_POST['editorValue'] : '';
                $data['g_strawberry'] = $_POST['strawberry'];
                $data['g_peach'] = isset($_POST['peach']) ? $_POST['peach'] : 0;
                $data['g_img'] = $data['g_img'] ? $data['g_img'] : '';
                $flag = $goodsMod->where(array('g_id'=>$id))->save($data);
            }else{
                $data['g_name'] = $_POST['name'];
                $data['g_desc'] = $_POST['editorValue'] ? $_POST['editorValue'] : '';
                $data['g_strawberry'] = $_POST['strawberry'];
                $data['g_peach'] = isset($_POST['peach']) ? $_POST['peach'] : 0;
                $data['g_create_time'] = time();
                $data['g_img'] = $data['g_img'] ? $data['g_img'] : '';
                $flag = $goodsMod->add($data);
            }
            if($flag || $id){
                $this->success("操作成功",U('Goods/index'));
            }else{
                $this->error("操作失败");
            }
        }else{
            $info = $goodsMod->where(array('g_id'=>$id))->find();
            $this->assign('info',$info);
        }
        
        $this->display();
        
    }
}
    
?>