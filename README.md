# yiban_DormitoryDeclaration
<br>
功能：<br>
1.学生填写故障申报表：<br>
  宿舍位置<br>
  故障描述<br>
  联系电话<br>
2.学生查询自己的申报记录<br>
  未完成处理的申报表ID会标红<br>
  一页只显示5条数据，支持翻页<br>
3.管理按键仅admin表中存在的用户id才可见<br>
  支持翻页，页数跳转，完成申报处理<br>
<br><br>
本项目没有一键安装的脚本，需要手动修改所有PHP文件中的数据库地址，账户和密码<br>
index.php中需要填写轻应用相关的数据，请自行修改<br>
数据库需要自己手动建立<br>
admin表<br>
CREATE TABLE `admin` (<br>
  `id` int(11) NOT NULL AUTO_INCREMENT,<br>
  `AdminId` int(11) NOT NULL,<br>
  PRIMARY KEY (`id`)<br>
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8	<br>
<br>
<br>
list表<br>
CREATE TABLE `list` (<br>
  `ListId` int(11) NOT NULL AUTO_INCREMENT,<br>
  `UserId` text NOT NULL,<br>
  `UserName` text NOT NULL,<br>
  `UserNick` text NOT NULL,<br>
  `UserAddress` text NOT NULL,<br>
  `UserNumber` text NOT NULL,<br>
  `UserDemand` text NOT NULL,<br>
  `ApplyTime` int(11) NOT NULL,<br>
  `EndTime` int(11) NOT NULL DEFAULT '0',<br>
  `IsEnd` tinyint(1) NOT NULL,<br>
  PRIMARY KEY (`ListId`)<br>
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8	<br>
<br>

本项目使用了amaze ui的css
http://amazeui.shopxo.net/css/
