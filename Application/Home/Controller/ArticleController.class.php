<?php 
namespace Home\Controller;
use Think\Controller;
/**
 * 每日必读列表
 * **/
class ArticleController extends Controller {
    /**
     * 今日知识，育儿知识最近几篇
     * **/
    public function getTodayArticle(){
            $articleMod = M("article");
            $list = $articleMod->field("id,title,img,url,cid")->where(array("type"=>2,'is_show'=>1))->limit(0,4)->order('id desc')->select();
            $result = array();
            $data = array();
            $status = C('CODE_STATUS');
            $cateList = C('cate_list');
            if(!$list){
                $result = array(
                    'code' => 10006,
                    'msg' => $status['10006'],
                );
            }else{
               foreach ($list as $k=>$v){
                  $list[$k]['cateName'] = isset($cateList[$v['cid']]) ? $cateList[$v['cid']] : '';
                  $list[$k]['imageUrl'] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$v['img'];
                  unset($list[$k]['img']);
                  unset($list[$k]['cid']);
               } 
               $data['todayArticle'] = $list;
               //获取轮播图片
                $imagesList = M('carousel_images')->where(array('status'=>1,'type'=>1))->order("sort desc,id asc")->select();
                foreach ($imagesList as $k=>$v){
                    $imagesList[$k]['imageUrl'] =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$v['img'];
                    unset($imagesList[$k]['img']);
                }
                $data['carouselImages'] = $imagesList;
                //萌娃启智文章
                $mrList = M('article')->field('id,cid,url,appid,path')->where(array('type'=>4))->select();
                $cate = C('cate');
                foreach ($mrList as $k=>$v){
                    $mrList[$k]['cateName'] = $cate[$v['cid']];
                    unset($mrList[$k]['cid']);
                }
                $mrFirst = $mrList[0];
                $mrSecond = $mrList[1];
                $mrThird = $mrList[2];
                $data['mrFirst'] = $mrFirst;
                $data['mrSecond'] = $mrSecond;
                $data['mrThird'] = $mrThird;
                $result = array(
                   'code' => 10000,
                   'msg' => $status['10000'],
                   'data' => $data
               );
            }
            $this->ajaxReturn($result);
    }
    
    /**
     * 育儿知识
     * **/
    public function getArticleByCate(){
        $result = array();
        $data = array();
        $status = C('CODE_STATUS');
        $cid = I("GET.cid");
        if($cid){
            $list = M("article")->field("id,title,img,url")->where(array('type'=>2,'cid'=>$cid,'is_show'=>1))->order('id desc')->select();
        }else{
            $list = M("article")->field("id,title,img,url")->where(array('is_show'=>2))->select();
        }
        if(!$list){
            $result = array(
                'code' => 10006,
                'msg' => $status['10006'],
            );
        }else{
            foreach ($list as $k=>$v){
                $list[$k]['imageUrl'] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].''.$v['img'];
                unset($list[$k]['img']);
            }
            $result = array(
                'code' => 10000,
                'msg' => $status['10000'],
                'data' => $list
            );
        }
        $this->ajaxReturn($result);
    }
    
    /**
     * 每日必读列表
     * **/
   public function getArticleList(){
//        $result = array();
//        $data = array();
//        $status = C('CODE_STATUS');
//        $articleMod = M('article');
//        if(I('GET.flag')){
//            $list = $articleMod->field("id,title,img,content,sharenum,comentnum,url")->where(array('is_show'=>2))->select();
//        }else{
//            $list = $articleMod->field("id,title,img,content,sharenum,comentnum,url")->where(array('type'=>1,'is_show'=>1))->select();
//        }
//        if(!$list){
//            $result = array(
//                'code' => 10006,
//                'msg' => $status['10006'],
//            );
//        }else{
//            foreach ($list as $k=>$v){
//                $list[$k]['imageUrl'] = $v['img'] ? $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$v['img'] : '';
//                $list[$k]['content'] = strip_tags(htmlspecialchars_decode($v['content']));
//                unset($list[$k]['img']);
//            }
//            $result = array(
//                'code' => 10000,
//                'msg' => $status['10000'],
//                'data' => $list
//            );
//        }
        @session_start();
        $f = cookie('tt');
        print_r($f);
       if($f){
           $result = array(
               'code'=>10001,
               'msg'=>'操作太频繁'
           );
       }else{
           cookie('tt',1,10);
           $result = array(
               'code'=>10000,
               'msg'=>'成功'
           );
       }
       $this->ajaxReturn($result);
   }
    
    /**
     * 每日必读详情
     * **/
    public function getArticleInfo(){
        $result = array();
        $data = array();
        $status = C('CODE_STATUS');
        
        $id = I('GET.id');
        $flag = I("GET.flag");
        if(!$id || !is_numeric($id)){
            $result = array(
                'code'=>10003,
                'msg'=>$status['10003'],
            );    
        }
        if($flag){
            $info = M("article")->field("id,title,content,img")->where(array('id'=>$id))->find();
        }else{
            $info = M("article")->field("id,title,content,img")->where(array('id'=>$id,'is_show'=>1))->find();
        }
        if(!$info){
            $result = array(
                'code' => 10006,
                'msg' => $status['10006'],
            );
        }else{
            $info['imageUrl'] =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER["SERVER_NAME"].$info['img'];
            unset($info['img']);
            //获取评论列表
            $commentList = M("comment")->field('c_content,c_create_time,openid')->where(array('a_id'=>$id))->order('is_top asc,c_id desc')->select();
            $list = array();
            $i=0;
            foreach ($commentList as $k=>$v){
                $userInfo = M("user")->where(array('openid'=>$v['openid']))->find();
                if(!$userInfo){
                    continue;
                }
                $list[$i]['content'] = $v['c_content'];
                $list[$i]['nickname'] = base64_decode($userInfo['nickname']);
                $list[$i]['headimgurl'] = $userInfo['headimgurl'];
                $list[$i]['createtime'] = formatDate($v['c_create_time']);
                $i++;
            }
            $info['commentList'] = $list;
            $result = array(
                'code' => 10000,
                'msg' => $status['10000'],
                'data' => $info
            );
        }
        $this->ajaxReturn($result);
    }
    
    /**
     * 增加分享数量
     * **/
    public function addShareNum(){
        $id = I('GET.id');
        $openId = I("GET.openid");
        $status = C('CODE_STATUS');
      
    if(!$id){
        $result = array(
            'code'=>10003,
            'msg'=>$status['10003']
        );
    }else{
        //查询最近分享的文章
        $info = M('share_article_detail')->where(array('openid'=>$openId))->order('id desc')->find();
        if($info&&time() - $info['create_time'] < 60){
            $result = array(
                'code'=>10001,
                'msg'=>'操作太频繁'
            );
        }else{
            $startTime = strtotime(date("Y-m-d"));
            $endTime = $startTime+86400;
            $count = M('share_article_detail')->where("openid='$openId' and aid=$id and create_time>=$startTime and create_time<$endTime")->count();
            if($count>5){
                $result = array(
                    'code'=>10001,
                    'msg'=>'分享次数超限'
                );
            }else{
                $flag = M("article")->where(array('id'=>$id))->setInc("sharenum");
                if($flag){
                    //添加分享积分
                    $time = time();
                    $startTime = strtotime(date("Y-m-d"));
                    $endTime = strtotime(date("Y-m-d"))+86400;
                    $totalSoce = M("bounds_log")->where(array('type'=>4,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
                    $logData = array();
                    $logData['openid'] = $openId;
                    $logData['type'] = 4;
                    $logData['strawberry'] = getScoreConfig('share');
                    $logData['create_time'] = $time;
                    if($totalSoce+getScoreConfig('share') > getScoreConfig('maxShare')){
                        $result = array(
                            'code' => 10015,
                            'msg' => "上限+".getScoreConfig('maxShare')."草莓",
                        );
                    }else{
                        if(M("bounds_log")->add($logData))
                        {
                            M("user")->where(array('openid'=>$openId))->setInc('strawberry',$logData['strawberry']);
                            //添加文章分享详情
                            $addData = array();
                            $addData['aid'] = $id;
                            $addData['openid'] = $openId;
                            $addData['create_time'] = $time;
                            M('share_article_detail')->add($addData);
                        }
                        $result = array(
                            'code' => 10000,
                            'msg' => $status['10000'],
                        );
                    }
                
                }else{
                    $result = array(
                        'code' => 10001,
                        'msg' => $status['10001'],
                    );
                }
            }
            
        }
        
    } 
       
        $this->ajaxReturn($result);
    }
    
    /**
     * 添加评论
     * **/
    public function addComment(){
        $id = I("POST.id");
        $openId = I("POST.openid");
        $content = I("POST.content");
        if(!$content){
            $result = array(
                'code'=>10006,
                'msg'=>'内容不能为空',
                'data'=>$id
            );
        }else{
            if(mb_strlen($content,'utf8') < 10){
                $result = array(
                    'code'=>10006,
                    'msg'=>'评论内容太短',
                    'data'=>$id
                );
            }else{
                $info = M("article")->where(array('id'=>$id))->find();
                $status = C('CODE_STATUS');
                if(!$info){
                    $result = array(
                        'code'=>10006,
                        'msg'=>$status['10006'],
                        'data'=>$id
                    );
                }else{
                    $count = M("comment")->where(array('openid'=>$openId,'a_id'=>$id))->count();
                    if($count){
                        $result = array(
                            'code'=>10007,
                            'msg'=>$status['10007'],
                            'data'=>$id
                        );
                    }else{
                        $data['openid'] = $openId;
                        $data['a_id']=$id;
                        $data['c_content'] = $content;
                        $data['c_create_time'] = time();
                        $flag = M("comment")->add($data);
                        if($flag){
                            //更新文章评论数量
                            M("article")->where(array('id'=>$id))->setInc('comentnum');
                            //添加评论积分
                            $startTime = strtotime(date("Y-m-d"));
                            $endTime = strtotime(date("Y-m-d"))+86400;
                            $totalSoce = M("bounds_log")->where(array('type'=>5,'openid'=>$openId))->where("create_time>=".$startTime." and create_time<".$endTime)->sum("strawberry");
                            if(intval($totalSoce) + getScoreConfig('comment') <= getScoreConfig('maxComment')){
                                $logData = array();
                                $logData['openid'] = $openId;
                                $logData['type'] = 5;
                                $logData['strawberry'] = getScoreConfig('comment');
                                $logData['create_time'] = time();
                                if(M("bounds_log")->add($logData))//添加登录积分奖励
                                {
                                    M("user")->where(array('openid'=>$openId))->setInc('strawberry',$logData['strawberry'] );
                                }
                                $result = array(
                                    'code' => 10000,
                                    'msg' => '增加'.$logData['strawberry'].'草莓',
                                    'data'=>$id
                                );
                            }else{
                                $result = array(
                                    'code' => 10016,
                                    'msg' => "上限".getScoreConfig('maxComment')."草莓",
                                    'data'=>$id
                                );
                            }
                
                        }else{
                            $result = array(
                                'code' => 10001,
                                'msg' => $status['10001'],
                                'data'=>$id
                            );
                        }
                    }
                }
            }
            
        }
        
        return $this->ajaxReturn($result);
    }
    
    /**
     * 获取萌娃启智文章
     * **/
//    public function getMrArticle(){
//        $list = M('article')->field('id,cid,url')->where(array('type'=>4))->select();
//        $cate = C('cate');
//        foreach ($list as $k=>$v){
//            $list[$k]['cateName'] = $cate[$v['cid']];
//            unset($list[$k]['cid']);
//        }
//        $status = C('STATUS_CODE');
//        $result['code'] = 10000;
//        $result['msg'] = $status['10000'];
//        $result['data'] = $list;
//        return $this->ajaxReturn($result);
//    }
}

?>