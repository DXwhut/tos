<?php
header("Access-Control-Allow-Origin: *");//允许跨域
	//连接数据库
	$con = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("logger", $con);
	mysql_query("set names 'utf8'");
	
	$sql = "select * from nagioslog,ip_node where nagioslog.IP = ip_node.ip and process in ('CPU', 'Disk', 'Memory')";
	$result = mysql_query($sql);
	$arr = array();
	while($row = mysql_fetch_row($result)){
		if($row[2] == 'CPU'){
			$row[4] = preg_match_all('/\d+/', $row[4], $matches)?(int)$matches[0][0]:0;
		}else if($row[2] == 'Memory'){
			$row[4] = preg_match_all('/\d+/', $row[4], $matches)?(100 - (int)$matches[0][1]):0;
		}else if($row[2] == 'Disk'){
			$row[4] = preg_match_all('/\d+/', $row[4], $matches)?(100 - (int)$matches[0][1]):0;
		}
		
		$arr[$row[8]][$row[2]]['status'] = $row[3];
		$arr[$row[8]][$row[2]]['value'] = $row[4];
		$arr[$row[8]][$row[2]]['time'] = $row[5];
	}
	$status = 200;
	if (count($arr) == 0) {
		$status = 404;
	}
	echo json_encode(array(status=>$status,data=>$arr));
	mysql_close($con);