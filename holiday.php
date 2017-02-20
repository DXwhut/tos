<?php
	$file = "./holiday.csv";
	//更新最近一年的节假日信息
	$con = mysql_connect('192.168.1.2', 'root', '123456');
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("realtime", $con);
	mysql_query("set names 'utf8'");

	//清空上一年的节假日数据
	$sql = "TRUNCATE TABLE holiday";
	mysql_query($sql);
	//添加新一年的数据
	$fp = fopen($file, 'r');
	while(!feof($fp)){
		$line = fgets($fp);
		$sql = "insert into holiday values('$line')";
		mysql_query($sql);
	}
	fclose($fp);
	mysql_close($con);