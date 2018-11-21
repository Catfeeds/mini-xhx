<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18 0018
 * Time: 下午 5:23
 */
namespace Admin\Controller;

class ChildController extends AdminController
{

    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 育儿知识文章列表
    */
    public function index(){
        $articleModel=M('article');
        $data = I('get.');
        empty($data['title'])? : $where['title'] = array('like','%'.$data['title'].'%');
        empty($data['cid'])? : $where['cid'] = array('eq',$data['cid']);
        $where['type'] = 2;
        $count=$articleModel->where($where)->count();
        $Page=new \Think\Page($count,20);//实例化分页类
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show=$Page->show();//分页显示输出
        $list = $articleModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $cateList = C('cate_list');
        foreach ($list as $k=>$v){
            $list[$k]['cateName'] = isset($cateList[$v['cid']]) ? $cateList[$v['cid']] : '';
        }
        $this->assign('page',$show);//赋值分页输出
        $this->assign('list',$list);//赋值数据集
        $this->assign('where',$where);
        $this->display();
    }
    /**
     *添加/编辑育儿知识文章
    */
    public function add(){
        $id = I("get.id");
        $articleMod =  M("article");
        $info = $articleMod->where(array('id'=>$id))->find();
        if($_POST){
            if(!empty($_FILES['img']['tmp_name'])){
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
                $data['img'] = $info['img'] ? $info['img'] : '';
            }
            if($id){
                $data['title'] = $_POST['title'];
                $data['url'] = $_POST['url'];
                $data['type'] = 2;
                $data['updatetime'] = time();
                $data['cid'] =  $_POST['cid'];
                $data['content'] = $_POST['editorValue'];
                $data['is_show'] = $_POST['is_show'];
                $flag = $articleMod->where(array('id'=>$id))->save($data);
            }else{
                $add = array();
                $data['title'] = $_POST['title'];
                $data['url'] = $_POST['url'];
                $data['type'] = 2;
                $data['createtime'] = time();
                $data['cid'] = $_POST['cid'];
                $data['content'] = $_POST['editorValue'];
                $data['is_show'] = $_POST['is_show'];
                $flag = $articleMod->where(array('id'=>$id))->add($data);
             }
             if($flag || $id){
                 $this->success('操作成功',U('Child/index'));
             }else{
                $this->error("操作失败");
             }
        }else{
            $this->assign('cateList',C('cate_list'));
            $this->assign('info',$info);
        }

        $this->display();
    }
    /***
     * 删除文章
     */
    public function del(){
        $id = I('GET.id');
        $info = M('article')->where(array("id"=>$id))->find();
        if($info){
            $sid = M('article')->where(array("id"=>$id))->delete();
        }else{
            $this->error("记录不存在");
        }
        if($sid){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }
}
