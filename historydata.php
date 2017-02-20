<?php

//测试代码
	$con = mysql_connect("172.20.104.157", "root", "324426");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("realtime", $con);
	mysql_query("set names 'utf8'");

// 	$sql = "select * from test22 where num = 1";
// 	$result = mysql_query($sql);
// 	while($row = mysql_fetch_row($result)){
// 		$sql = "insert into historytradenum values(null,$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],22,'$row[12]')";
// echo $sql;
// 		mysql_query($sql);
// 	}

	// 每次从excel导入历史数据到数据库，都需要执行以下代码，把时间格式转为标准，比如7:00:00转为07:00:00
	$sql = "select time from historytradescale";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_row($result)) {
		$time = $row[0];
		if (strlen($row[0]) == 7) {
			$time = '0'.$row[0];
		}
		$sql = "update historytradescale set time='$time' where time='$row[0]'";
		mysql_query($sql);
	}


	mysql_close($con);
