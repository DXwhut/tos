<?php
header("Access-Control-Allow-Origin: *");//允许跨域
	$username = $_POST['username'];
	$password = $_POST['password'];
	//连接数据库
	$con = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("logger", $con);
	mysql_query("set names 'utf8'");
	$sql = "select * from admin where username='{$username}' and password='{$password}'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	if($row){
		$_SESSION['adminName'] = $row['username'];
		$_SESSION['adminId'] = $row['id'];
		echo json_encode(array(status=>200, mes=>'success'));
	}else{
		echo json_encode(array(status=>404, mes=>'fail'));
	}
	mysql_close($con);
