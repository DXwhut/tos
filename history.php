<?php
	// 月底执行的脚本，生成历史平均数据 59 23 28 * * php /var/www/html/history.php
	$con = mysql_connect("192.168.1.2", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("realtime", $con);
	mysql_query("set names 'utf8'");


	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade01 WHERE isHoliday=0 AND num=1 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradenum set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where  isHoliday=0 AND trade_type=1 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade21 WHERE isHoliday=0 AND num=1 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradenum set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=0 AND trade_type=21 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade22 WHERE isHoliday=0 AND num=1 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradenum set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=0 AND trade_type=22 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade01 WHERE isHoliday=0 AND num=4 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradescale set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=0 AND trade_type=1 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade21 WHERE isHoliday=0 AND num=4 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradescale set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=0 AND trade_type=21 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade22 WHERE isHoliday=0 AND num=4 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradescale set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=0 AND trade_type=22 and time='$row[9]'";
		mysql_query($sql);
	}


	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade01 WHERE isHoliday=1 AND num=1 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradenum set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where  isHoliday=1 AND trade_type=1 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade21 WHERE isHoliday=1 AND num=1 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradenum set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=1 AND trade_type=21 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade22 WHERE isHoliday=1 AND num=1 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradenum set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=1 AND trade_type=22 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade01 WHERE isHoliday=1 AND num=4 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradescale set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=1 AND trade_type=1 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade21 WHERE isHoliday=1 AND num=4 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradescale set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=1 AND trade_type=21 and time='$row[9]'";
		mysql_query($sql);
	}

	$sql = "SELECT AVG(全网线),AVG(11号线),AVG(1号线),AVG(2号线),AVG(3号线),AVG(4号线),AVG(5号线),AVG(7号线),AVG(9号线),insert_time from trade22 WHERE isHoliday=1 AND num=4 AND error = 0 GROUP BY insert_time";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$sql = "update historytradescale set 全网线='$row[0]',11号线='$row[1]',1号线='$row[2]',2号线='$row[3]',3号线='$row[4]',4号线='$row[5]',5号线='$row[6]',7号线='$row[7]',9号线='$row[8]' where isHoliday=1 AND trade_type=22 and time='$row[9]'";
		mysql_query($sql);
	}

	// 清空数据
	$sql = "TRUNCATE TABLE trade01";
	mysql_query($sql);
	$sql = "TRUNCATE TABLE trade21";
	mysql_query($sql);
	$sql = "TRUNCATE TABLE trade22";
	mysql_query($sql);

	mysql_close($con);
