<?php
header("Content-Type: text/html;charset=utf-8");//中文显示
header("Access-Control-Allow-Origin: *");
	$line = $_GET['line'];
	$trade_type = $_GET['trade_type'];
	$time = strtotime($_GET['time']);
	$time2 = $time + 24*3600;
	//测试
	// $line = '1号线';
	// $trade_type = '21';
	// $time = '1478151868';
	//连接数据库
	$con = mysql_connect("192.168.1.2", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("realtime", $con);
	mysql_query("set names 'utf8'");

	$date = $_GET['time'];
	$sql = "select * from holiday where date like '$date%'";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		$isHoliday = 0;
	}else{
		$isHoliday = 1;
	}

	$sql = "select line_name from station where line_id = $line";
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	if($row){
		$line = $row[0];
	}else{
		$line = '全网线';
	}
	$arr = array();
	if($trade_type == 'singleTicket'){
		$sql = "select insert_time,$line from trade01 where time > '$time' and time < '$time2' and num = 4";
		$sql2 = "select time,$line from historytradescale where trade_type = 1 and isHoliday=$isHoliday";
		$result = mysql_query($sql);
		$result2 = mysql_query($sql2);
		while ($row = mysql_fetch_row($result)) {
			$arr[$row[0]]['time'] = $row[0];
			$arr[$row[0]]['now'] = $row[1];
		}
		while ($row = mysql_fetch_row($result2)) {
			$arr[$row[0]]['history'] = $row[1];
		}
	}else if($trade_type == 'stationIn'){
		$sql = "select insert_time,$line from trade21 where time > '$time' and time < '$time2' and num = 4";
		$sql2 = "select time,$line from historytradescale where trade_type = 21 and isHoliday=$isHoliday";
		$result = mysql_query($sql);
		$result2 = mysql_query($sql2);
		while ($row = mysql_fetch_row($result)) {
			$arr[$row[0]]['time'] = $row[0];
			$arr[$row[0]]['now'] = $row[1];
		}
		while ($row = mysql_fetch_row($result2)) {
			$arr[$row[0]]['history'] = $row[1];
		}
	}else{
		$sql = "select insert_time,$line from trade22 where time > '$time' and time < '$time2' and num = 4";
		$sql2 = "select time,$line from historytradescale where trade_type = 22 and isHoliday=$isHoliday";
		$result = mysql_query($sql);
		$result2 = mysql_query($sql2);
		while ($row = mysql_fetch_row($result)) {
			$arr[$row[0]]['time'] = $row[0];
			$arr[$row[0]]['now'] = $row[1];
		}
		while ($row = mysql_fetch_row($result2)) {
			$arr[$row[0]]['history'] = $row[1];
		}
	}
	$status = 200;
	if (count($arr) == 0) {
		$status = 404;
	}
	echo json_encode(array(time=>$_GET['time'],status=>$status,data=>array_values($arr)));
	mysql_close($con);
