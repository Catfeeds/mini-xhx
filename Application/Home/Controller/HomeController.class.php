<?php
namespace Home\Controller;
use Think\Controller;
class HomeController extends Controller {
    //ajax返回默认成功
    public function ajaxPut($data,$code='',$msg=''){
        $status = C('CODE_STATUS');
        if(empty($code)){
            $result = array(
                'code' => 10000,
                'msg' => $status[10000],
                'data' => $data
            );
        }elseif(empty($msg)){
            $result = array(
                'code' => $code,
                'msg' => $status[$code],
                'data' => $data
            );
        }else{
            $result = array(
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            );
        }
        $this->ajaxReturn($result);
    }
    //ajax成功
    public function ajaxSuccess($data){
        $status = C('CODE_STATUS');
        $code = 10000;
        $result = array(
            'code' => $code,
            'msg' => $status[$code],
            'data' => $data
        );
        $this->ajaxReturn($result);
    }
    //ajax错误
    public function ajaxError($data){
        $status = C('CODE_STATUS');
        $code = 10001;
        $result = array(
            'code' => $code,
            'msg' => $status[$code],
            'data' => $data
        );
        $this->ajaxReturn($result);
    }
}