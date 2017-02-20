<?php
header("Access-Control-Allow-Origin: *");//允许跨域
	$service = $_GET['service'];

	//连接数据库
	// $con = mysql_connect("172.20.104.138", "root", "");
	$con = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("logger", $con);
	mysql_query("set names 'utf8'");
	
	switch ($service) {
		case 'ftp':
			$process = "'vsftpd'";
			break;
		case 'hadoop':
			$process = "'JournalNode', 'NameNode', 'ResourceManager', 'JobHistoryServer', 'DataNode', 'NodeManager', 'DFSZKFailoverController'";
			break;
		case 'mysql':
			$process = "'mysqld'";
			break;
		case 'redis':
			$process = "'redis-server'";
			break;
		case 'storm':
			$process = "'UI', 'Nimbus', 'Supervisor'";
			break;
		case 'tomcat':
			$process = "'tomcat'";
			break;
		case 'zookeeper':
			$process = "'QuorumPeerMain'";
			break;
		default:
			break;
	}
	$sql = "select nagioslog.IP,process,status,infomation,time,node from nagioslog,ip_node where nagioslog.IP = ip_node.ip and process in ($process)";
	$result = mysql_query($sql);
	$arr = array();
	while($row = mysql_fetch_row($result)){
		
		$arr[$row[0]]['IP'] = $row[0];
		$arr[$row[0]]['node'] = $row[5];
		$arr[$row[0]]['data'][] = array('proc'=>$row[1],
							 		'status'=>$row[2],
							 		'errorInfo'=>$row[3],
							 		'time'=>$row[4]);
	}
	
	$status = 200;
	if (count($arr) == 0) {
		$status = 404;
	}
	echo json_encode(array(status=>$status,data=>array_values($arr)));
	mysql_close($con);


