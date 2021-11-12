<?php
    include"Mysql.php";
    //从mian获取到用户基本信息
    $userid=$_GET['userid'];//易班ID
    $username=$_GET['username'];//易班名字
    $usernick=$_GET['usernick'];//易班昵称
    $userhead=$_GET['userhead'];//头像地址
    
    $EndListId=$_GET['EndListId'];//此处为要完成的记录ListId
    $Page=$_GET['Page'];
        
    //链接数据库
    $connect=new Mysql("localhost:3306", "StudentApply", "StudentApply", "utf-8","studentapply");
    
    //开始核对是否是管理员    
    $AdminList=$connect->from("admin")->where("AdminId='{$userid}'")->field('AdminId')->getAll();
    foreach($AdminList as $value){
        if($value['AdminId']==$userid){
	       	break;
	    }else {
	        echo "<script>alert('对不起，你不是管理员！');history.back()</script>";
	    }
    }
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
    } //输入行名和行内容并记录，可多次调用形成多行表格,可以使用16进制颜色代码
        
    public function showTable(){
        echo "<table class='mailTable' width='350' cellspacing='0' cellpadding='0'>";
        foreach ($this->Table as $key => $value){
            echo "<tr>";
	         echo "<td class='column'>"."<font color='{$value['color']}'>".$value['LineName']."</font>"."</td>";
	        echo "<td>"."<font color='{$value['color']}'>".$value['LineContent']."</font>";
	        echo "</tr>";
        }
         echo "</table>";
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
    
    public function showJumpPage($EndPage){
        $nowPage=$_GET['Page'];
        echo "
        <center>
        <table>
        <tr><td>
        <input type='text' id='jumpPage' class='am-form-field am-round' style='width:240px' placeholder='页码,当前第{$nowPage}页,共{$EndPage}页'></input>
        </td><td>
        <input type='button' onclick='jump2Page()' class='am-btn am-btn-default am-round' value='跳转'>
        </td>
        </table>
        </form>
        </center>
        ";
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
<title>湖南人文科技学院学生公寓故障申报</title>
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
	<h1><br>湖南人文科技学院<br>学生公寓故障申报系统</h1>
			</div>
		<button onclick='backMain()' style='width:100%;padding:5px,auto;' class='am-btn am-btn-primary am-btn-sm am-fl'>返回查询页面</button>
	    <br><br>
		<div style="width:90%" align="left">
			
			<blockquote>
			    <img src=<?php echo $userhead;?>><br>
			    <strong>你好，<?php echo $username;?> <br>欢迎来到管理后台</strong>
			</blockquote>
		</div>
		<div style="width:90%" align="left"><br><br>
		
		    
		<?php
		    //优先处理记录
		    if($EndListId != 0){
                $WillEndList = $connect->from('list')->where("ListID='{$EndListId}'")->getOne();
                $WillEndList['EndTime'] = time();
                $WillEndList['IsEnd'] = 1;
                $FinishApply = $connect->update('list',$WillEndList,"ListID='{$EndListId}'");
            }
            
            $Limit=($Page-1)*5;
            $UserData=$connect->from("list")->group("IsEnd,ListId")->limit("{$Limit},5")->getAll();
            $EndPage=ceil(($connect->field('count(*)')->from("list")->getValue())/5);
		    $tempTable = new QueryTable();
		    if(!$UserData){
		        echo "未查询到与你相关的记录";
		    }
		    foreach ($UserData as $key => $value){
		        if($value['EndTime']==0){
		            $value['EndTime']="尚未处理";
		            $value['ApplyTime']=date("Y-m-d H:i",$value['ApplyTime']);
    		        $tempTable->addLine("ID",$value['ListId'],"#FF0000");
    		        $tempTable->addLine("用户ID",$value['UserId']);
    	            $tempTable->addLine("用户名",$value['UserName']);
    		        $tempTable->addLine("宿舍地址",$value['UserAddress']);
    		        $tempTable->addLine("发生的问题",$value['UserDemand']);
    		        $tempTable->addLine("联系电话",$value['UserNumber']);
    		        $tempTable->addLine("申请时间",$value['ApplyTime']);
    		        $tempTable->addLine("结束时间",$value['EndTime']);
    		        $tempTable->showTable();
        		    echo "<button style='width:100%;padding:5px,auto;' onClick='toEnd({$value['ListId']})' class='am-btn am-btn-primary am-btn-sm am-fl am-round'>完成处理</button><br><br><br>";
		        }else {
	            $value['EndTime']=date("Y-m-d H:i",$value['EndTime']);
	            $value['ApplyTime']=date("Y-m-d H:i",$value['ApplyTime']);
		        $tempTable->addLine("ID",$value['ListId']);
		        $tempTable->addLine("用户ID",$value['UserId']);
	            $tempTable->addLine("用户名",$value['UserName']);
		        $tempTable->addLine("宿舍地址",$value['UserAddress']);
		        $tempTable->addLine("发生的问题",$value['UserDemand']);
		        $tempTable->addLine("联系电话",$value['UserNumber']);
		        $tempTable->addLine("申请时间",$value['ApplyTime']);
		        $tempTable->addLine("结束时间",$value['EndTime']);
		        $tempTable->showTable();
		        echo "<br><br>";
		        }
		    }
		    $PageList=new PageList();
		    $PageList->showJumpPage($EndPage);
		    echo "<br>";
    		$PageList->showMobliePage($Page,5,5,"50%");
    		echo "<br><br>";
		?>
		
		</div>
		
		</center>
	
</body>
</html>
<script>
    function toEnd(ListId,page){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/admin.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead
        +"&IsQuery="+"1"
        +"&Page="+"1"
        +"&EndListId="+ListId;
    }
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
    function toPage(page){
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/admin.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead
        +"&IsQuery="+"1"
        +"&Page="+page;
    }
    function jump2Page(){
        var page=document.getElementById("jumpPage").value;
        if(!page){return}
        var userid = "<?php echo $userid?>";
        var username = "<?php echo $username?>";
        var usernick = "<?php echo $usernick?>";
        var userhead = "<?php echo $userhead?>";
        window.location="http://192.168.31.28/test/admin.php?"
        +"&userid="+userid
        +"&username="+username
        +"&usernick="+usernick
        +"&userhead="+userhead
        +"&IsQuery="+"1"
        +"&Page="+page;
    }
</script>