<?php
    include"Mysql.php";
    //从mian获取到用户基本信息
    $userid=$_GET['userid'];//易班ID
    $username=$_GET['username'];//易班名字
    $usernick=$_GET['usernick'];//易班昵称
    $userhead=$_GET['userhead'];//头像地址
    
    //链接数据库
    $connect=new Mysql("localhost:3306", "StudentApply", "StudentApply", "utf-8","studentapply");
    
    //如果有POST传值代表提交了表单，此处为有表单提交的处理
    $FormUserAddress=$_POST['FormUserAddress'];
    $FormUserDemand=$_POST['FormUserDemand'];
    $FormUserNumber=$_POST['FormUserNumber'];
    if($FormUserAddress && $FormUserDemand && $FormUserAddress){
        $ApplyTime=time();//获取当前时间戳
        
        $temp=array(
            'Userid'=>$userid,
            'Username'=>$username,
            'Usernick'=>$usernick,
            'UserAddress'=>$FormUserAddress,
            'UserNumber'=>$FormUserNumber,
            'UserDemand'=>$FormUserDemand,
            'ApplyTime'=>$ApplyTime);
        $connect->insert('list',$temp);
        header("location:http://192.168.31.28/test/");
    }
    
    
?>

<html lang="zh-CN">
<head>
<meta charset="UTF-8">
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="yes" name="apple-touch-fullscreen">
        <meta content="320" name="MobileOptimized">
	<link rel="stylesheet" href="css/amazeui.css"/>
	
<meta charset="utf-8">
<title>学生公寓故障申报表</title>
<style>
    	
//input美化
input[type=search] {
    float: center;
    background-color: #FFF;
    border: 1px solid #A9A9A9;
    padding: 10px;
    border-radius: 2px;
    width: 280px;
    outline: none;
    margin-right: 7px;
    transition: all .2s;
}
input[type=search]:focus {
    width: 300px;
}
::-webkit-input-placeholder {
    font-family:'Open Sans', sans-serif;
}
input[type=search]:focus {
    border: 1px solid #000;
    padding-left: 30px;
}
</style>
</head>
<body>
    <center>
        <h1>学生公寓故障申报表</h1>
        <button onclick='backMain()' style='width:100%;padding:5px,auto;' class='am-btn am-btn-primary am-btn-sm am-fl'>返回查询页面</button><br><br>
        <div style="width:90%" align="left">
        <blockquote>
            <img src=<?php echo $userhead;?>><br>
			<strong>你好，<?php echo $username;?> <br>您正在填写故障申报表</strong>
		</blockquote>
        </div>
        
        <div style="width:90%">
            
            <form method="post" action="form.php?<?php echo "userid={$userid}&username={$username}&usernick={$usernick}&userhead={$userhead}";?>">
                
        <blockquote>
            <strong>请填写你宿舍的位置</strong>
        </blockquote>
        <input type="search" name="FormUserAddress" required="true" placeholder="请确保地址清晰" style="height:40px">
        
        <blockquote>
            <strong>请填写需要解决的问题</strong>
        </blockquote>
        <input type="search" name="FormUserDemand" required="true" placeholder="请确保描述清晰" style="height:40px">
        
        <blockquote>
            <strong>请填写你的联系电话</strong>
        </blockquote>
        <input type="search" name="FormUserNumber" required="true" placeholder="请确保真实有效" style="height:40px">
        <br><br>
        
        <input type="submit" required="true"  class="am-btn am-btn-primary am-btn-sm am-fl am-round" style="width:100%;padding:5px,auto;" value="提交">
            </form>
        </div>
    </center>
    
    
</body>

</html>
<script>
    function backMain(){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/main.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead;
    }
</script>