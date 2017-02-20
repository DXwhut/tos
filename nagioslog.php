<?php
// */1 * * * * php /var/www/html/nagioslog.php
ini_set('date.timezone','Asia/Shanghai');
	header("Content-Type: text/html;charset=utf-8");//中文显示
	// $con = mysql_connect("172.20.104.157", "root", "324426");
	// $con = mysql_connect("172.20.104.138", "root", "");
	$con = mysql_connect("192.168.1.5", "root", "123456");
	if(!$con){
		die('Could not connect:' . mysql_error());
	}
	mysql_select_db("logger", $con);
	mysql_query("set names 'utf8'");

	$file="/usr/local/nagios/var/service-perfdata.out";  
	$lines = tail($file, 100);
	for ($i=0; $i < 100; $i++) { 
		$arr = explode("	", $lines[$i]);
		$time = date('Y-m-d H:i:s', $arr[0]);
		$sql = "update nagioslog set time='$time',status='$arr[3]',infomation='$arr[8]' where IP='$arr[1]' and process='$arr[2]'";
		mysql_query($sql);
	}
	//取出文件的最后10行数据
	function tail($file,$num){  
	    $fp = fopen($file,"r");  
	    $pos = -2;  
	    $eof = "";  
	    $head = false;   //当总行数小于Num时，判断是否到第一行了  
	    $lines = array();  
	    while($num>0){  
	        while($eof != "\n"){  
	            if(fseek($fp, $pos, SEEK_END)==0){    //fseek成功返回0，失败返回-1  
	                $eof = fgetc($fp);  
	                $pos--;  
	            }else{                               //当到达第一行，行首时，设置$pos失败  
	                fseek($fp,0,SEEK_SET);  
	                $head = true;                   //到达文件头部，开关打开  
	                break;  
	            }  
	        }  
	        array_unshift($lines,fgets($fp));  
	        if($head){ break; }                 //这一句，只能放上一句后，因为到文件头后，把第一行读取出来再跳出整个循环  
	        $eof = "";  
	        $num--;  
	    }  
	    fclose($fp);  
	    return $lines;  
	}  

	// $fp = fopen("/usr/local/nagios/var/nagios.log", "r");
	// for($i = 0; $i < 61; $i++){
	// 	//第一次读取日志
	// 	// $str = fgets($fp);
	// 	// $arr = explode(";", $str);
	// 	// if(count($arr) == 6){
	// 	// 	$time = date('Y-m-d H:i:s', substr($arr[0], 1, 10));
	// 	// 	$ip = substr(end(explode(":", $arr[0])), 1);
	// 	// 	if (strstr($arr[5], "'")) {
	// 	// 		$info = str_replace("'", " ", $arr[5]);
	// 	// 	}else{
	// 	// 		$info = $arr[5];
	// 	// 	}
	// 	// 	$sql = "insert into nagioslog values(null,'$ip','$arr[1]','$arr[2]','$info','$time')";
	// 	// 	mysql_query($sql);
	// 	// }else{
	// 	// 	continue;
	// 	// }
	// 	//第2次读取
	// 	fgets($fp);
	// }
	//第2次读取
	// 以下是所有状态都在一个表中
	// while (!feof($fp)) {
	// 	$str = fgets($fp);
	// 	$arr = explode(";", $str);
	// 	if(count($arr) == 6){
	// 		$time = date('Y-m-d H:i:s', substr($arr[0], 1, 10));
	// 		if (strstr($arr[5], "'")) {
	// 			$info = str_replace("'", " ", $arr[5]);
	// 		}else{
	// 			$info = $arr[5];
	// 		}
	// 		$sql = "update nagioslog set time='$time',status='$arr[3]',infomation='$info' where IP='$arr[1]' and process='$arr[2]'";
	// 		mysql_query($sql);
	// 	}
	// }

	//以下是建了多个数据表的情况
	// function operateSQL($arr, $service){
	// 	if($arr[3] == 'OK'){
	// 		$sql = "update $service set time='$time' where IP='$arr[1]' and proc='$arr[2]'";
	// 	}else{
	// 		$sql = "insert into $service values(null, '$arr[1]', '$arr[2]', '$arr[3]','$arr[5]' '$time')";
	// 	}
	// 	return $sql;
	// }
	// while (!feof($fp)) {
	// 	$str = fgets($fp);
	// 	$arr = explode(";", $str);
	// 	if(count($arr) == 6){
	// 		$time = date('Y-m-d H:i:s', substr($arr[0], 1, 10));
	// 		if($arr[2] == 'CPU'){
	// 			$arr[5] = preg_match('/\d+/', $arr[5], $matches)?(int)$matches[0]:0;
	// 			$sql = operateSQL($arr, 'hardware');
	// 		}else if($arr[2] == 'Memory'){
	// 			$arr[5] = preg_match('/\d+/', $arr[5], $matches)?(100 - (int)$matches[1]):0;
	// 			$sql = operateSQL($arr, 'hardware');
	// 		}else if($arr[2] == 'Disk'){
	// 			$arr[5] = preg_match('/\d+/', $arr[5], $matches)?(100 - (int)$matches[1]):0;
	// 			$sql = operateSQL($arr, 'hardware');
	// 		}else if(in_array($arr[2], array('JournalNode', 'NameNode', 'ResourceManager', 'JobHistoryServer', 'DataNode', 'NodeManager', 'DFSZKFailoverController'))){
	// 			$sql = operateSQL($arr, 'hadoop');
	// 		}else if(in_array($arr[2], array('UI', 'Nimbus', 'Supervisor'))){
	// 			$sql = operateSQL($arr, 'storm');
	// 		}else if(in_array($arr[2], array('QuorumPeerMain'))){
	// 			$sql = operateSQL($arr, 'zookeeper');
	// 		}else if(in_array($arr[2], array('Mysql'))){
	// 			$sql = operateSQL($arr, 'mysql');
	// 		}else if(in_array($arr[2], array('Redis'))){
	// 			$sql = operateSQL($arr, 'redis');
	// 		}else if(in_array($arr[2], array('ParseMain', 'Read', 'FTPGet', 'Ftp2Redis', 'Redis2Mysql'))){
	// 			$sql = operateSQL($arr, 'java');
	// 		}else if($arr[2] == 'tomcat'){
	// 			$sql = operateSQL($arr, 'tomcat');
	// 		}else if($arr[2] == 'vsftpd'){
	// 			$sql = operateSQL($arr, 'ftp');
	// 		}
	// 		mysql_query($sql);
	// 	}else{
	// 		continue;
	// 	}
	// }
	mysql_close($con);
	// fclose($fp);