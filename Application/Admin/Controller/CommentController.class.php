<?php
    namespace Admin\Controller;
    class CommentController extends AdminController
    {
         public function _initialize()
          {
                parent::_initialize();
          }

          public function index()
          {
                $commentModel=M('comment');
                $data = I('get.');
                empty($data['aid'])? : $where['a_id'] = array('eq',$data['aid']);
                $count=$commentModel->where($where)->count();
                $Page=new \Think\Page($count,20);//实例化分页类
                $Page->setConfig('prev', '上一页');
                $Page->setConfig('next', '下一页');
                $show=$Page->show();//分页显示输出
                $list = $commentModel->where($where)->order('c_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
                $articleList = M("article")->field("id,title")->select();
                foreach($list as $k=>$v){
                    $articleInfo = M('article')->where(array('id'=>$v['a_id']))->find();
                    $list[$k]['articleTitle'] = $articleInfo['title'];
                }
                $this->assign('page',$show);//赋值分页输出
                $this->assign('list',$list);//赋值数据集
                $this->assign('where',$data);
                $this->assign('articleList',$articleList);
                $this->display();
          }

          public function setTop()
          {
                $isTop = I("POST.istop");
                $id = I("POST.id");
                $info = M('comment')->where(array('c_id'=>$id))->find();
              if(!empty($id) && !empty($isTop)){
                  $save['is_top'] = $isTop;
                  $uid = M('comment')->where(array('c_id'=>$id))->save($save);
                  if($uid){
                      $rest['code'] = 10000;
                      $rest['msg'] = '修改成功';
                      $this->ajaxReturn($rest);
                      exit();
                  }else{
                      $rest['code'] = 10002;
                      $rest['msg'] = '修改失败';
                      $this->ajaxReturn($rest);
                      exit();
                  }
              }else{
                  $rest['code'] = 10002;
                  $rest['msg'] = '修改数据不
                  exit();能为空';
                  $this->ajaxReturn($rest);
              }
          }

          public function del()
          {
                $id = I('GET.id');
                $info = M('comment')->where(array("c_id"=>$id))->find();
                if($info){
                    $sid = M('comment')->where(array("c_id"=>$id))->delete();
                    if($sid){
                        //更新文章评论数量
                        M('article')->where(array('id'=>$info['a_id']))->setDec('comentnum');
                    }
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
?>