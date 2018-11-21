<?php
//单号码发送
function sendcode($tel,$type='',$data,$smscontent){
    $mp = $tel;
    if(!$mp){
        return '-1';//手机号为空
    }
    if($data){
        $url='http://smssh1.253.com/msg/send/json';
        $jsondata = '{"account":"N4309052","password":"jYqioU9vOR8372","msg":"'.$smscontent.'","phone":"'.$mp.'","sendtime":"'.date('YmdHi').'","report":"true","extend":"","uid":"'.randomkeys(5).$data.'"}';
        $res = PostPay($jsondata,$url);
        $res = json_decode($res,true);
        $addrecord['tel'] = $mp;
        $addrecord['data'] = $data;
        $addrecord['catetime'] = time();
        if($type){
            $addrecord['type'] = $type;
        }else{
            $addrecord['type'] = '';
        }
        $addrecord['endtime'] = time() + 120;
        $addtel = $res;
        $addtel['data'] = $data;
        $addtel['content'] = $smscontent;
        $addtel['catetime'] = time();

        M('code_tel')->add($addtel);
        M('code_record')->add($addrecord);
        if($res['code'] == '0'){
            return 1;
        }else{
            return 0;
        }
    }else{
        return '-2';//无验证号
    }

}
/**********************
 *****身份证***********
 *****************/
function validation_filter_id_card($id_card){
    if(strlen($id_card)==18){
        return idcard_checksum18($id_card);
    }elseif((strlen($id_card)==15)){
        $id_card=idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    }else{
        return false;
    }
}
// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base){
    if(strlen($idcard_base)!=17){
        return false;
    }
    //加权因子
    $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
    //校验码对应值
    $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
    $checksum=0;
    for($i=0;$i<strlen($idcard_base);$i++){
        $checksum += substr($idcard_base,$i,1) * $factor[$i];
    }
    $mod=$checksum % 11;
    $verify_number=$verify_number_list[$mod];
    return $verify_number;
}
// 将15位身份证升级到18位
function idcard_15to18($idcard){
    if(strlen($idcard)!=15){
        return false;
    }else{
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
            $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
        }else{
            $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
        }
    }
    $idcard=$idcard.idcard_verify_number($idcard);
    return $idcard;
}
// 18位身份证校验码有效性检查
function idcard_checksum18($idcard){
    if(strlen($idcard)!=18){
        return false;
    }
    $idcard_base=substr($idcard,0,17);
    if(idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
        return false;
    }else{
        return true;
    }
}
/**
 * 验证码检查
 */
function check_verify($code, $id = ""){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

function sendEmail($sendemail,$sendpassword,$to,$title,$body){
    Vendor('PHPMailer.class#PHPMailer');
    $mail = new phpmailer();
    $mail->IsSMTP();
    $mail->Host = 'smtp.qq.com';
    $mail->SMTPDebug = '1';
    $mail->SMTPAuth = true;
    $mail->Port = 25;
    $mail->Username = $sendemail;
    $mail->Password = $sendpassword;
    $mail->SetFrom($sendemail, '微信营销平台');
    $mail->AddReplyTo($sendemail, '微信营销平台');
    $mail->Subject = $title;
    $mail->AltBody = '';
    $mail->MsgHTML($body);
    $address = $to;
    $mail->AddAddress($address, '商户');
    return($mail->Send());
}

/**
 * 手机号检查
 */
function check_tel($tel){
    if(preg_match("/^(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[0-9])[0-9]{8}$/",$tel)){
        return true;
    }else{
        return false;
    }
}

/**
 * 查询ip
 */
function getRealIp()
{
    $ip=false; //初始化ip为false
    if(!empty($_SERVER["HTTP_CLIENT_IP"])){ //如果HTTP_CLIENT_IP不为空
        $ip = $_SERVER["HTTP_CLIENT_IP"]; //获取HTTP_CLIENT_IP的值
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //如果HTTP_X_FORWARDED_FOR不为空
        $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        //把HTTP_X_FORWARDED_FOR的值用,分割后存放数组ips
        if ($ip) {
            array_unshift($ips, $ip); $ip = FALSE;
        }//遍历处理
        for ($i = 0; $i < count($ips); $i++) {
            if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
                $ip = $ips[$i]; //获得真实ip
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

/**
 * 制作token
 */
function get_token($randLength=6,$attatime=1,$includenumber=0){
    if ($includenumber){
        $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
    }else {
        $chars='abcdefghijklmnopqrstuvwxyz';
    }
    $len=strlen($chars);
    $randStr='';
    for ($i=0;$i<$randLength;$i++){
        $randStr.=$chars[rand(0,$len-1)];
    }
    $tokenvalue=$randStr;
    if ($attatime){
        $tokenvalue=$randStr.time();
    }
    return $tokenvalue;
}

function https_request($url, $data = NULL)
{
    $curl = curl_init();
    $header = 'Accept-Charset: utf-8';
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    $errorno = curl_errno($curl);

    if ($errorno) {
        return array('curl' => false, 'errorno' => $errorno);
    }
    else {
        $res = json_decode($output, 1);

        if ($res['errcode']) {
            return array('errcode' => $res['errcode'], 'errmsg' => $res['errmsg']);
        }
        else {
            return $res;
        }
    }

    curl_close($curl);
}
function httpPost($url,$data){ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话
    return $tmpInfo; // 返回数据
}

function PostPay($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS,$curlPost);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($curlPost))
    );

    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function randomkeys($length)
{
    for($i=0;$i<$length;$i++)
    {
        $key .= mt_rand(0,9);    //生成php随机数
    }
    return $key;
}

/**
 * @param $str 需要过滤的字符串/html标签过滤
 * @return string 过滤后的字符串
 */
function strhtml($str){

    $str=stripslashes($str);

    $str=preg_replace("/\s+/", ' ', $str); //过滤多余回车
    $str=preg_replace("/<[ ]+/si",'<',$str); //过滤<__("<"号后面带空格)

    $str=preg_replace("/<\!--.*?-->/si",'',$str); //注释
    $str=preg_replace("/<(\!.*?)>/si",'',$str); //过滤DOCTYPE
    $str=preg_replace("/<(\/?html.*?)>/si",'',$str); //过滤html标签
    $str=preg_replace("/<(\/?head.*?)>/si",'',$str); //过滤head标签
    $str=preg_replace("/<(\/?meta.*?)>/si",'',$str); //过滤meta标签
    $str=preg_replace("/<(\/?body.*?)>/si",'',$str); //过滤body标签
    $str=preg_replace("/<(\/?link.*?)>/si",'',$str); //过滤link标签
    $str=preg_replace("/<(\/?form.*?)>/si",'',$str); //过滤form标签
    $str=preg_replace("/cookie/si","COOKIE",$str); //过滤COOKIE标签

    $str=preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si",'',$str); //过滤applet标签
    $str=preg_replace("/<(\/?applet.*?)>/si",'',$str); //过滤applet标签

    $str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si",'',$str); //过滤style标签
    $str=preg_replace("/<(\/?style.*?)>/si",'',$str); //过滤style标签

    $str=preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si",'',$str); //过滤title标签
    $str=preg_replace("/<(\/?title.*?)>/si",'',$str); //过滤title标签

    $str=preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si",'',$str); //过滤object标签
    $str=preg_replace("/<(\/?objec.*?)>/si",'',$str); //过滤object标签

    $str=preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si",'',$str); //过滤noframes标签
    $str=preg_replace("/<(\/?noframes.*?)>/si",'',$str); //过滤noframes标签

    $str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si",'',$str); //过滤frame标签
    $str=preg_replace("/<(\/?i?frame.*?)>/si",'',$str); //过滤frame标签

    $str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si",'',$str); //过滤script标签
    $str=preg_replace("/<(\/?script.*?)>/si",'',$str); //过滤script标签
    $str=preg_replace("/javascript/si","JAVASCRIPT",$str); //过滤script标签
    $str=preg_replace("/vbscript/si","VBSCRIPT",$str); //过滤script标签
    $str=preg_replace("/on([a-z]+)\s*=/si","ON\\1=",$str); //过滤script标签
    $str=preg_replace("/&#/si","&＃",$str); //过滤script标签，如javAsCript:alert('aabb')

    $str=addslashes($str);
    return($str);
}

function strlength($str,$minlen,$maxlen){
    $len = strlen($str);
    return ($len >= $minlen) && ($len <= $maxlen);
}
/**
 * @param $arrays 需要排序的数组
 * @param $sort_key 判断的key
 * @param int $sort_order 倒序还是顺序
 * @param int $sort_type 按什么类型排序
 * @return array|bool 二维数组排序
 */
function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return $arrays;
}

function formatDate($time){
    $rtime = date ( "m-d H:i", $time );
    $htime = date ( "H:i", $time );

    $time = time () - $time;

    if ($time < 60) {
        $str = '刚刚';
    } elseif ($time < 60 * 60) {
        $min = floor ( $time / 60 );
        $str = $min . '分钟前';
    } elseif ($time < 60 * 60 * 24) {
        $h = floor ( $time / (60 * 60) );
        $str = $h . '小时前 ';
    } elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor ( $time / (60 * 60 * 24) );
        if ($d == 1)
            $str = '昨天 ';
        else
            $str = '前天 ';
    } else {
        $str = $rtime;
    }
    return $str;
}
function getScoreConfig($key=''){
    $config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/Public/cache.txt"),true);
    if($key){
        return $config[$key];
    }else{
        return $config;
    }
}
?>