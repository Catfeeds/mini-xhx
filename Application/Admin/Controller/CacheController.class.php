<?php 
    namespace Admin\Controller;
    class CacheController extends AdminController{
        public function _initialize()
        {
            parent::_initialize();
        }
        //设置配置文件
        public function set(){
          $arr = array();
          $publicPath = $_SERVER['DOCUMENT_ROOT']."/Public/";
          if($_POST){
              $arr['register'] = $_POST['register'];
              $arr['invite'] = $_POST['invite'];
              $arr['sign'] = $_POST['sign'];
              $arr['share'] = $_POST['share'];
              $arr['maxShare'] = $_POST['maxshare'];
              $arr['comment'] = $_POST['comment'];
              $arr['maxComment'] = $_POST['maxcomment'];
              $arr['birth'] = $_POST['birth'];
              $arr['rate'] = $_POST['rate'];
              file_put_contents($publicPath.'cache.txt', json_encode($arr));
              $this->success('操作成功');
          }else{
              if(file_exists($publicPath."cache.txt")){
                  $arr = json_decode(file_get_contents($publicPath."cache.txt"),true);
              }
              $this->assign('info',$arr);
              $this->display();
          }
        }
    } 
    
?>