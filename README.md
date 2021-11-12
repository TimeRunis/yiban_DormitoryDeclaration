# yiban_DormitoryDeclaration

功能：
1.学生填写故障申报表：
  宿舍位置
  故障描述
  联系电话
2.学生查询自己的申报记录
  未完成处理的申报表ID会标红
  一页只显示5条数据，支持翻页
3.管理按键仅admin表中存在的用户id才可见
  支持翻页，页数跳转，完成申报处理

本项目没有一键安装的脚本，需要手动修改所有PHP文件中的数据库地址，账户和密码
数据库需要自己手动建立
admin表
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `AdminId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8	


list表
CREATE TABLE `list` (
  `ListId` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` text NOT NULL,
  `UserName` text NOT NULL,
  `UserNick` text NOT NULL,
  `UserAddress` text NOT NULL,
  `UserNumber` text NOT NULL,
  `UserDemand` text NOT NULL,
  `ApplyTime` int(11) NOT NULL,
  `EndTime` int(11) NOT NULL DEFAULT '0',
  `IsEnd` tinyint(1) NOT NULL,
  PRIMARY KEY (`ListId`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8	
