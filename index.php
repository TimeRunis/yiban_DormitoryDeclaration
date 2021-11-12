<?php
ini_set("display_errors", "On");
error_reporting(0);

if (!function_exists('curl_init')) {
  throw new Exception('YiBan needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('YiBan needs the JSON PHP extension.');
}
if (!function_exists('mcrypt_decrypt')) {
  throw new Exception('YiBan needs the mcrypt PHP extension.');
}

//以下三个变量内容需换成本应用的
$APPID = "请修改";   //在open.yiban.cn管理中心的AppID
$APPSECRET = "请修改"; //在open.yiban.cn管理中心的AppSecret
$CALLBACK = "https://oauth.yiban.cn/code/html?client_id=APPID&redirect_uri=CALLBACK&state=STATE";  //在open.yiban.cn管理中心的oauth2.0回调地址

if(isset($_GET["code"])){   //用户授权后跳转回来会带上code参数，此处code非access_token，需调用接口转化。
    $getTokenApiUrl = "https://oauth.yiban.cn/token/info?code=".$_GET['code']."&client_id={$APPID}&client_secret={$APPSECRET}&redirect_uri={$CALLBACK}";
    $res = sendRequest($getTokenApiUrl);
    if(!$res){
        throw new Exception('Get Token Error');
    }
    $userTokenInfo = json_decode($res);
    $access_token = $userTokenInfo["access_token"];
}else{
    $postStr = pack("H*", $_GET["verify_request"]);
    if(strlen($APPID) == '16') {
        $postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $APPSECRET, $postStr, MCRYPT_MODE_CBC, $APPID);
    }else {
        $postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $APPSECRET, $postStr, MCRYPT_MODE_CBC, $APPID);
    }
    $postInfo = rtrim($postInfo);
    $postArr = json_decode($postInfo, true);
    if(!$postArr['visit_oauth']){  //说明该用户未授权需跳转至授权页面
        header("Location: https://openapi.yiban.cn/oauth/authorize?client_id={$APPID}&redirect_uri={$CALLBACK}&display=web");
        die;
    }
    $access_token = $postArr['visit_oauth']['access_token'];
}

//拿到access token了，接下来我们获取当前用户的基本信息试试看，so easy!
$userInfoJsonStr = sendRequest("https://openapi.yiban.cn/user/me?access_token={$access_token}");
$userInfo = json_decode($userInfoJsonStr);

var_dump($userInfo);

function sendRequest($uri){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Yi OAuth2 v0.1');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array());
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    $response = curl_exec($ch);
    $response = json_decode($response);
    
    $userid=$response->info->yb_userid;
    $username=$response->info->yb_username;
    $usernick=$response->info->yb_usernick;
    $userhead=$response->info->yb_userhead;
    
    header("Location: http://192.168.31.28/test/main.php?userid={$userid}&username={$username}&usernick={$usernick}&userhead={$userhead}&IsQuery=0");
}

?>