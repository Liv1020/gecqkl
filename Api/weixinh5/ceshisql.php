<?php
	$mysql_server_name='39.106.45.147:3306'; //改成自己的mysql数据库服务器
	$mysql_username='root'; //改成自己的mysql数据库用户名
			 
	$mysql_password='1qaz@WSX1@3'; //改成自己的mysql数据库密码
			 
	$mysql_database='gec'; //改成自己的mysql数据库名
		
	$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
	print_R($conn);
			
?>