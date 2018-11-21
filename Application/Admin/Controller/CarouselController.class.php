<?php
namespace Admin\Controller;

class CarouselController extends AdminController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index(){
        $imagesModel=M('carousel_images');
        $data = I('get.');
        empty($data['status'])? : $where['status'] = array('eq',$data['status']);
        $count=$imagesModel->where($where)->count();
        $Page=new \Think\Page($count,20);//实例化分页类
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show=$Page->show();//分页显示输出
        $list = $imagesModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $k=>$v){
            $articleInfo = array();
            if($v['aid']){
                $articleInfo = M('article')->where(array('id'=>$v['aid']))->find();
            }
            $list[$k]['title'] = $articleInfo ? $articleInfo['title'] : '';
        }
        $this->assign('page',$show);//赋值分页输出
        $this->assign('list',$list);//赋值数据集
        $this->assign('where',$where);
        $this->display();
    }

    public function add(){
        $id = I('GET.id');
        if($_POST){
            $info = array();
            if($id){
                $info = M('carousel_images')->where(array('id'=>$id))->find();
            }
            if(!empty($_FILES['img']['tmp_name'])){
                $upload = new \Think\Upload(); // 实例化上传类
                $upload->maxSize   = 314572800; // 设置附件上传大小
                $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = 'carousel/'.$this->token.'/';
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
                $data['img'] = $info['img'];
            }
                if($id){
                    $data['url'] = $_POST['url'];
                    $data['sort'] = $_POST['sort'];
                    $data['status'] = $_POST['status'];
                    $data['aid'] = $_POST['aid'];
                    $data['type'] = $_POST['type'];
                    $flag = M('carousel_images')->where(array('id'=>$id))->save($data);
                }else{
                    $data['url'] = $_POST['url'];
                    $data['sort'] = $_POST['sort'];
                    $data['status'] = $_POST['status'] ? $_POST['status'] :1;
                    $data['aid'] = $_POST['aid'];
                    $data['type'] = $_POST['type'] ? $_POST['type'] : 1;
                    $data['img'] = $data['img'] ? $data['img'] : '';
                    $flag = M('carousel_images')->add($data);
                }
                if($flag || $id){
                    $this->success('操作成功',U('Carousel/index'));
                }else{
                    $this->error('操作失败');
                }
        }else{
            $info = M('carousel_images')->where(array('id'=>$id))->find();
            $articleList = M('article')->select();
            $this->assign('info',$info);
            $this->assign('articleList',$articleList);
            $this->display();
        }
    }

    public function del(){
        $id = I('GET.id');
        $info = M('carousel_images')->where(array('id'=>$id))->find();
        if(!$info){
            $this->error('记录不存在');
        }
        if(M('carousel_images')->where(array('id'=>$id))->delete()){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

}
?>