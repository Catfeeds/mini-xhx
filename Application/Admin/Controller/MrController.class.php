<?php 
namespace Admin\Controller;

class MrController extends AdminController
{
    public function _initialize()
    {
        parent::_initialize();
    }
    
    public function index(){
        $list = M("article")->where(array('type'=>4))->select();
        $cate = C('cate');
        foreach ($list as $k=>$v){
            $list[$k]['cateName'] = $cate[$v['cid']];
        }
        $this->assign('list',$list);
        $this->display();
    }
    
    public function edit(){
        $id = I('GET.id');
        if($_POST){
            $param = I("POST.");
            $data['title'] = $param['title'];
            $data['content'] = $param['editorValue'];
            $data['url'] = $param['url'];
            $data['is_show'] = $param['is_show'];
            $data['appid'] = trim($param['appid']);
            $data['path'] = trim($param['path']);
            $flag = M('article')->where(array('id'=>$param['id']))->save($data);
            if($flag !== false){
                $this->success('操作成功');
            }else{
                $this->error("操作失败");
            }
        }else{
            $info = M('article')->where(array('type'=>4,'id'=>$id))->find();
            $cate = C('cate');
            $info['cateName'] = $cate[$info['cid']];
            $this->assign('info',$info);
            $this->display();
        }
    }
}

?>