<?php
header("Access-Control-Allow-Origin: *");//允许跨域
	//连接数据库
	$con = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("logger", $con);
	mysql_query("set names 'utf8'");
	
	$sql = "select * from nagioslog,program_module where nagioslog.process = program_module.name and process in ('Ftp2Redis', 'Redis2Mysql', 'ParseMain', 'Read', 'FTPGet', 'ReadConf', 'CalRegularPassengerWeekend', 'CalRegularPassengerWork', 'CalSubwayODDistribute_weekend', 'CalSubwayODDistribute_workday')";
	$result = mysql_query($sql);
	$arr = array();
	while($row = mysql_fetch_row($result)){
		if($row[3] != 'OK'){
			$arr[$row[1]][$row[2]]['status'] = "CRITICAL";
			$arr[$row[1]][$row[2]]['errorInfo'] = $row[4];
                        $arr[$row[1]][$row[2]]['time'] = $row[5];
		}else{
			$sql = "select * from `$row[2]` ORDER BY time DESC LIMIT 1";
			$result1 = mysql_query($sql);
			$arr_new = mysql_fetch_row($result1);
			if ($arr_new[1] == 1) {
				$arr[$row[1]][$row[2]]['status'] = "OK";
			}else{
				$arr[$row[1]][$row[2]]['status'] = "CRITICAL";
			}
			$arr[$row[1]][$row[2]]['errorInfo'] = $arr_new[2];
                        $arr[$row[1]][$row[2]]['time'] = $arr_new[3];
		}
		$arr[$row[1]][$row[2]]['module'] = $row[8];
	}

	$sql = "select * from nagioslog where process = 'Tomcat'";
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	$arr[$row[1]]['Subway']['status'] = $row[3];
	$arr[$row[1]]['Subway']['errorInfo'] = $row[4];
	$arr[$row[1]]['Subway']['time'] = $row[5];
	$arr[$row[1]]['Subway']['module'] = '4';

	$status = 200;
	if (count($arr) == 0) {
		$status = 404;
	}
	echo json_encode(array(status=>$status,data=>$arr));

	mysql_close($con);
