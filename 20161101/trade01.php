<?php
// 每20分钟执行一次的脚本 */20 7-23 * * * php /var/www/html/20161101/trade01.php
	ini_set('date.timezone','Asia/Shanghai');
	$con = mysql_connect("192.168.1.2", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("realtime", $con);
	mysql_query("set names 'utf8'", $con);

	$con4 = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con4){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("realtime", $con4);
	mysql_query("set names 'utf8'", $con4);
	
	$start = floor(time()/100).'00';
	$date = date('Y-m-d', $start);
	$sql = "select * from holiday where date like '$date%'";
	$result = mysql_query($sql, $con);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		$isHoliday = 0;
	}else{
		$isHoliday = 1;
	}
	$time0 = date('Y-m-d', $start).'T'.date('H:i:s', $start).'.000Z';
	$time = date('Y-m-d', $start).'T'.date('H:i:s', $start - 2*60*60).'.000Z';
	$time2 = date('Y-m-d', $start).'T'.'06:00:00.000Z';
	$time4 = date('Y-m-d H:i:s', $start - 20*60);
	$time5 = date('Y-m-d H:i:s', $start);
	$time6 = strtotime(date('Y-m-d', $start).' 06:00:00');//获取当天6点00的时间戳，用来计算当天累计数据接入量

	//历史平均（交易数据接入量）
	$time7 = date('H:i:s', $start);
	if($isHoliday == 0){
		$sql = "select * from historytradenum where isHoliday=0 and trade_type = 1 and time = '$time7'";
	}else{
		$sql = "select * from historytradenum where isHoliday=1 and trade_type = 1 and time = '$time7'";
	}
	
	$result = mysql_query($sql, $con4);
	$arr_history_num = mysql_fetch_array($result);
	//新建一个数据用来存放各条线路的偏差
	$arr_offset = array();

	//实时接入量（线网）
	$sql = "select count(*) from realt where RECEIVE_DATE > '$time4' and RECEIVE_DATE <= '$time5' and TRADE_DATE > '$time' and TRADE_DATE <= '$time0' and TRADE_TYPE = 01";
	$result = mysql_query($sql, $con);
	$row = mysql_fetch_array($result);
	$all = $row[0];
	$arr_offset[] = abs($arr_history_num['全网线'] - $all) / $arr_history_num['全网线'];//全网线的偏差

	//各线路
	$sql = "select station.line_name,count(*) from realt,station where realt.TRADE_ADDRESS = station.station_id and RECEIVE_DATE > '$time4' and RECEIVE_DATE <= '$time5' and TRADE_DATE > '$time' and TRADE_DATE <= '$time0' and TRADE_TYPE = 01 GROUP BY station.line_name";
	$result = mysql_query($sql, $con);
	$arr = array('11号线'=>0,'1号线'=>0,'2号线'=>0,'3号线'=>0,'4号线'=>0,'5号线'=>0,'7号线'=>0,'9号线'=>0);
	while ($row = mysql_fetch_array($result)) {
		$arr[$row[0]] = $row[1];
		$arr_offset[] = abs($arr_history_num[$row[0]] - $row[1]) / ($arr_history_num[$row[0]] + 0.1);//各条线路的偏差
	}
	//判断数据是否异常
	$error = 0;
	sort($arr_offset);
	if ($arr_offset[count($arr_offset) - 1] > 0.5) {
		$error = 1;
	}
	if (array_sum($arr_offset) / count($arr_offset) > 0.2) {
		$error = 1;
	}
	//插入
	$tmp = $arr['11号线'].','.$arr['1号线'].','.$arr['2号线'].','.$arr['3号线'].','.$arr['4号线'].','.$arr['5号线'].','.$arr['7号线'].','.$arr['9号线'];
	$new_sql = "insert into trade01 values(null,$start,$all,$tmp,1,'$time7',$error,$isHoliday)";
	mysql_query($new_sql, $con4);

	//历史平均（延时大于20）
	if($isHoliday == 0){
		$sql = "select * from historytradescale where isHoliday=0 and trade_type=1 and time='$time7'";
	}else{
		$sql = "select * from historytradescale where isHoliday=1 and trade_type=1 and time='$time7'";
	}
	$result = mysql_query($sql, $con4);
	$arr_history_num = mysql_fetch_array($result);
	//新建一个数据用来存放各条线路的偏差
	$arr_offset = array();

	//平均延时（线网）
	$sql3 = "select TRADE_DATE,RECEIVE_DATE from realt where RECEIVE_DATE > '$time4' and RECEIVE_DATE <= '$time5' and TRADE_DATE > '$time2' and TRADE_TYPE = 01";
	$result = mysql_query($sql3, $con);
	$arr = array();
	while ($row = mysql_fetch_array($result)) {
		$trade_time = strtotime(substr($row[0], 0, 10).' '.substr($row[0], 11, 8));
		$receive_time = strtotime($row[1]);
		$arr[] = $receive_time - $trade_time;
	}
	//数据延时在20分钟以上的数据量
	$N = count($arr);
	$n = 0;
	for ($i=0; $i < $N; $i++) { 
		if ($arr[$i] > 1200) {
			$n++;
		}
	}
	$all2 = round($n/$N, 2);
	$arr_offset[] = abs($arr_history_num['全网线'] - $all2) / $arr_history_num['全网线'];//全网线的偏差
	$all = round(array_sum($arr) / $N, 2);
	//各线路
	$sql3 = "select station.line_name,TRADE_DATE,RECEIVE_DATE from realt,station where realt.TRADE_ADDRESS = station.station_id and RECEIVE_DATE > '$time4' and RECEIVE_DATE <= '$time5' and TRADE_DATE > '$time2' and TRADE_TYPE = 01";
	$result = mysql_query($sql3, $con);
	$arr = array('11号线'=>array(),'1号线'=>array(),'2号线'=>array(),'3号线'=>array(),'4号线'=>array(),'5号线'=>array(),'7号线'=>array(),'9号线'=>array());
	while ($row = mysql_fetch_array($result)) {
		$trade_time = strtotime(substr($row[1], 0, 10).' '.substr($row[1], 11, 8));
		$receive_time = strtotime($row[2]);
		$arr[$row[0]][] = $receive_time - $trade_time;
	}
	//对于11号线
	$N = count($arr['11号线']);
	$n = 0;
	for ($i=0; $i < $N; $i++) { 
		if ($arr['11号线'][$i] > 1200) {
			$n++;
		}
	}
	$a11 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['11号线'] - $a11) / $arr_history_num['11号线'];//11号线的偏差
	//对于1号线
	$n = 0;
	$N = count($arr['1号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['1号线'][$i] > 1200) {
			$n++;
		}
	}
	$a1 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['1号线'] - $a1) / $arr_history_num['1号线'];//1号线的偏差
	//对于2号线
	$n = 0;
	$N = count($arr['2号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['2号线'][$i] > 1200) {
			$n++;
		}
	}
	$a2 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['2号线'] - $a2) / $arr_history_num['2号线'];//2号线的偏差
	//对于3号线
	$n = 0;
	$N = count($arr['3号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['3号线'][$i] > 1200) {
			$n++;
		}
	}
	$a3 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['3号线'] - $a3) / $arr_history_num['3号线'];//3号线的偏差
	//对于4号线
	$n = 0;
	$N = count($arr['4号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['4号线'][$i] > 1200) {
			$n++;
		}
	}
	$a4 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['4号线'] - $a4) / $arr_history_num['4号线'];//4号线的偏差
	//对于5号线
	$n = 0;
	$N = count($arr['5号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['5号线'][$i] > 1200) {
			$n++;
		}
	}
	$a5 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['5号线'] - $a5) / $arr_history_num['5号线'];//5号线的偏差
	//对于7号线
	$n = 0;
	$N = count($arr['7号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['7号线'][$i] > 1200) {
			$n++;
		}
	}
	$a7 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['7号线'] - $a7) / $arr_history_num['7号线'];//7号线的偏差
	//对于9号线
	$n = 0;
	$N = count($arr['9号线']);
	for ($i=0; $i < $N; $i++) { 
		if ($arr['9号线'][$i] > 1200) {
			$n++;
		}
	}
	$a9 = round($n/$N ,2);
	$arr_offset[] = abs($arr_history_num['9号线'] - $a9) / $arr_history_num['9号线'];//9号线的偏差
	//插入
	$tmp = round(array_sum($arr['11号线']) / count($arr['11号线']), 2).','.
		   round(array_sum($arr['1号线']) / count($arr['1号线']), 2).','.
		   round(array_sum($arr['2号线']) / count($arr['2号线']), 2).','.
		   round(array_sum($arr['3号线']) / count($arr['3号线']), 2).','.
		   round(array_sum($arr['4号线']) / count($arr['4号线']), 2).','.
		   round(array_sum($arr['5号线']) / count($arr['5号线']), 2).','.
		   round(array_sum($arr['7号线']) / count($arr['7号线']), 2).','.
		   round(array_sum($arr['9号线']) / count($arr['9号线']), 2);
	$new_sql = "insert into trade01 values(null,$start,$all,$tmp,3,'$time7',null,$isHoliday)";
	mysql_query($new_sql, $con4);
	$tmp = $a11.','.$a1.','.$a2.','.$a3.','.$a4.','.$a5.','.$a7.','.$a9;
	// 延时大于20min的交易占比
	//判断数据是否异常
	sort($arr_offset);
	if ($arr_offset[count($arr_offset) - 1] > 0.5) {
		$error = 1;
	}
	if (array_sum($arr_offset) / count($arr_offset) > 0.2) {
		$error = 1;
	}
	if ($error = 1) {
		//获取上一个时间段的数据是否异常，每异常一次叠加1，不异常后归零
		$sql_error_num_old = "select error from trade01 where num = 4 and time >= '$time6' ORDER BY time DESC limit 1";
		$result = mysql_query($sql_error_num_old, $con4);
		if ($row = mysql_fetch_row($result)) {
			$error = $row[0] + 1;
		}
	}
	$new_sql = "insert into trade01 values(null,$start,$all2,$tmp,4,'$time7',$error,$isHoliday)";
	mysql_query($new_sql, $con4);

	//累计数据接入量
	$sql = "select SUM(全网线),SUM(11号线),SUM(1号线),SUM(2号线),SUM(3号线),SUM(4号线),SUM(5号线),SUM(7号线),SUM(9号线) from trade01 where time>='$time6' and time<='$start' and num=1";
	$result = mysql_query($sql, $con4);
	$row = mysql_fetch_array($result);
	//插入
	$tmp = $row[0].','.$row[1].','.$row[2].','.$row[3].','.$row[4].','.$row[5].','.$row[6].','.$row[7].','.$row[8];
	$new_sql = "insert into trade01 values(null,$start,$tmp,5,'$time7',null,$isHoliday)";
	mysql_query($new_sql, $con4);

	// 判断当天的数据是否异常（给代号用）
	$sql_error_num = "select error from trade01 where num =4 and time >= '$time6' order by error desc limit 1";
	$result = mysql_query($sql_error_num, $con4);
	$row = mysql_fetch_row($result);
	//异常时间段连续出现5次 判断当天数据为异常
	if ($row[0] >= 5) {
		mysql_select_db("subway", $con);
		$result = mysql_query("select * from dayinfo where date='$date'", $con);
		$num = mysql_num_rows($result);
		if ($num == 0) {
			mysql_query("insert into dayinfo values(null, null, null, '$date', null, 1)", $con);
		}
	}
	
	mysql_close($con);
	mysql_close($con4);
