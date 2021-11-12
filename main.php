<?php
    include"Mysql.php";
    //从API获取到用户基本信息
    $userid=$_GET['userid'];//易班ID
    $username=$_GET['username'];//易班名字
    $usernick=$_GET['usernick'];//易班昵称
    $userhead=$_GET['userhead'];//头像地址
    
    $IsQuery=$_GET['IsQuery'];
    $Page=$_GET['Page'];
    
    //链接数据库
    $connect=new Mysql("localhost:3306", "StudentApply", "StudentApply", "utf-8","studentapply");
?>


<?php
//这里是自写表类
class QueryTable{
    private $LineNum=0;
    public $Table;
   
    public function addLine($_LineName,$_LineContent,$_color='#000000'){
    $this->Table[$this->LineNum]= array(
        'LineName' => $_LineName,
        'LineContent' => $_LineContent,
        'color' => $_color
        );
    $this->LineNum++;
    } //输入行名和行内容并记录，可多次调用形成多行表格,重载可以加16进制颜色代码
        
    public function showTable(){
        echo "<table class='mailTable' width='350' cellspacing='0' cellpadding='0'>";
        foreach ($this->Table as $key => $value){
            echo "<tr>";
	         echo "<td class='column'>"."<font color='{$value['color']}'>".$value['LineName']."</font>"."</td>";
	        echo "<td>"."<font color='{$value['color']}'>".$value['LineContent']."</font>";
	        echo "</tr>";
        }
         echo "</table><br>";
        $this->Table = array();
    } //输出表格并清空表格数据，下次调用需要重新使用addLine()添加行
}

//自写分页类
class PageList{
    public function showMobliePage($nowPage,$EndPage,$size,$width){
        $prePage=$nowPage-1;
        $nextPage=$nowPage+1;
        if($prePage>0){echo "<button style='width:{$width}' onclick='toPage({$prePage})' class='am-btn am-btn-default am-round'><font size='{$size}'>".上一页."</font></button>";}else{
            echo "<button style='width:{$width}' onclick='toPage({$prePage})' class='am-btn am-btn-default am-round' disabled='disabled'><font size='{$size}'>".上一页."</font></button>";
        }
        if($nextPage<=$EndPage){echo "<button style='width:{$width}' onclick='toPage({$nextPage})' class='am-btn am-btn-default am-round'><font size='{$size}'>".下一页."</font></button>";}else {
            echo "<button style='width:{$width}' onclick='toPage({$nextPage})' class='am-btn am-btn-default am-round' disabled='disabled'><font size='{$size}'>".下一页."</font></button>";
        }//参数从左至右为所处页号，末尾页号，按钮字体大小，按钮宽度
    }
}
?>

<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="yes" name="apple-touch-fullscreen">
        <meta content="320" name="MobileOptimized">
	<link rel="stylesheet" href="css/amazeui.css"/>
	
<meta charset="utf-8">
<title>学生公寓故障申报</title>
<style>
    .mailTable{
    width: 100%;
    border-top: 1px solid #E6EAEE;
    border-left: 1px solid #E6EAEE;
}
.mailTable tr td{
    width: 200px;
    height: 35px;
    line-height: 35px;
    box-sizing: border-box;
    padding: 0 10px;
    border-bottom: 1px solid #E6EAEE;
    border-right: 1px solid #E6EAEE;
}
.mailTable tr td.column {
    background-color: #EFF3F6;
    color: #393C3E;
    width: 30%;
}
</style>
</head>

<body>
	<center>
	<h1><br>学生公寓故障申报系统</h1>
			</div>
	<?php 
	    $AdminList=$connect->from("admin")->where("AdminId='{$userid}'")->field('AdminId')->getAll();
	    
	    foreach($AdminList as $value){
            if($value['AdminId']==$userid){
    	       	echo "<button onclick='toAdmin()' style='width:100%;padding:5px,auto;' class='am-btn am-btn-primary am-btn-sm am-fl'>管理</button>";
	        }
        }
	?>
	    <br><br>
		<div style="width:90%" align="left">
			
			<blockquote>
			    <img src=<?php echo $userhead;?>><br>
			    <strong>你好，<?php echo $username;?> <br>欢迎使用本系统<br><font color="#FF0000">未完成的申请ID已标红</font></strong>
			</blockquote>
			<button style="width:100%;padding:5px,auto;" class="am-btn am-btn-primary am-btn-sm am-fl am-round" onclick="toForm()">前往填写申报表</button><br><br>
			<button style="width:100%;padding:5px,auto;" class="am-btn am-btn-primary am-btn-sm am-fl am-round" onclick="toQuery()">查询申报记录</button>
			 
		</div>
		<div style="width:90%" align="left"><br><br>
		
		    
		<?php
		    if($IsQuery=="1")
		    {
		        $Limit=($Page-1)*5;
		        $UserData=$connect->from("list")->where("Userid='{$userid}'")->group("IsEnd,ListId desc")->limit("{$Limit},5")->getAll();
    		    $tempTable = new QueryTable();
    		    if(!$UserData){
    		        echo "未查询到与你相关的记录<br>";
    		    }
    		    foreach ($UserData as $key => $value){
    		        if($value['EndTime']==0){
    		            $value['EndTime']="尚未处理";
    		            $value['ApplyTime']=date("Y-m-d H:i",$value['ApplyTime']);
        		        $tempTable->addLine("ID",$value['ListId'],"#FF0000");
        		        $tempTable->addLine("宿舍地址",$value['UserAddress']);
        		        $tempTable->addLine("发生的问题",$value['UserDemand']);
        		        $tempTable->addLine("联系电话",$value['UserNumber']);
        		        $tempTable->addLine("申请时间",$value['ApplyTime']);
        		        $tempTable->addLine("结束时间",$value['EndTime']);
        		        
    		        }else {
    		            $value['EndTime']=date("Y-m-d H:i",$value['EndTime']);
    		            $value['ApplyTime']=date("Y-m-d H:i",$value['ApplyTime']);
        		        $tempTable->addLine("ID",$value['ListId']);
        		        $tempTable->addLine("宿舍地址",$value['UserAddress']);
        		        $tempTable->addLine("发生的问题",$value['UserDemand']);
        		        $tempTable->addLine("联系电话",$value['UserNumber']);
        		        $tempTable->addLine("申请时间",$value['ApplyTime']);
        		        $tempTable->addLine("结束时间",$value['EndTime']);
    		        }
    		        $tempTable->showTable();
    		    }
        		$PageList=new PageList();
        		$PageList->showMobliePage($Page,5,5,"50%");
        		echo "<br><br>";
		    }
		    
		?>
		
		</div>
		
		</center>
	
</body>
</html>
<script>
    //使用js进行页面跳转并把php变量转化成js变量传值
    function toForm(){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/form.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead;
    }
    
    function toQuery(){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/main.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead
        +"&IsQuery="+"1"
        +"&Page="+"1";
    }
    
    function toAdmin(){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/admin.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead
        +"&Page="+"1";
    }
    function toPage(page){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/main.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead
        +"&IsQuery="+"1"
        +"&Page="+page;
    }

</script>    