<?php 
namespace Admin\Controller;
use Think\Controller;
class ExpertController extends AdminController {
    Public function _initialize(){
        parent::_initialize();
    }
    
    public function set(){
        if($_POST){
            $param = I("POST.");
            if($param['id']){
                $data['title'] = $param['title'];
                $data['content'] = $param['editorValue'];
                $data['url'] = $param['url'];
                $data['is_show'] = $param['is_show'];
                $flag = M('article')->where(array('id'=>$param['id']))->save($data);
            }else{
                
                $data['type'] = 3;
                $data['title'] = $param['title'];
                $data['content'] = $param['editorValue'];
                $data['url'] = $param['url'];
                $data['is_show'] = $param['is_show'];
                $flag = M('article')->add($data);
            }
            if($flag !== false){
                $this->success('操作成功');
            }else{
                $this->error("操作失败");
            }
        }else{
             $info = M('article')->where(array('type'=>3))->find();
             $this->assign('info',$info);
             $this->display();
        }
    }
}

?>