<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController {
    public function index(){
        $data = array(1,2,3,4,'成功');
        $this->ajaxPut($data);
    }
}