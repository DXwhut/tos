<?php
header("Access-Control-Allow-Origin: *");//允许跨域
	//连接数据库
	$con = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("logger", $con);
	mysql_query("set names 'utf8'");
	$sql = "select count(*) from nagioslog where status='OK' and process in ('JournalNode', 'NameNode', 'ResourceManager', 'JobHistoryServer', 'DataNode', 'NodeManager', 'DFSZKFailoverController', 'vsftpd', 'mysqld', 'redis-server', 'UI', 'Nimbus', 'Supervisor', 'tomcat', 'QuorumPeerMain')";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$arr = array();
	$arr['OK'] = (int)$row[0];
	$arr['CRITICAL'] = 30 - $row[0];
	$status = 200;
	if (count($arr) == 0) {
		$status = 404;
	}
	echo json_encode(array(status=>$status,data=>$arr));
	mysql_close($con);
