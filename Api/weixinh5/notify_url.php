<?php
/**
 * 微信支付通知地址
 *
 */
//
//$a=header("location: http://www.zgllsj.com/index.php/Index/Payment/h5_notifys");
//$_GET['_URL_']= Array(0=>'index',1=>'payment',2=>'h5_notifys');
//print_r($_GET);
//echo $_SERVER['SERVER_NAME'];
//require_once(dirname(__FILE__).'/../../index.php');
//header('location:../../index.php/index/payment/h5_notifys');

		header('Content-type:text/html; Charset=utf-8');		
		$testxml  = file_get_contents("php://input");
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $result = json_decode($jsonxml, true);//转成数组
        if($result){
	        $onumber = $result['out_trade_no'];
	        
	        $mysql_server_name='39.106.45.147'; //改成自己的mysql数据库服务器
			 
			$mysql_username='root'; //改成自己的mysql数据库用户名
			 
			$mysql_password='1qaz@WSX1@3'; //改成自己的mysql数据库密码
			 
			$mysql_database='gec'; //改成自己的mysql数据库名
			
			$conn=mysqli_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
			file_put_contents('1.txt',json_encode(mysql_error()));
			mysqli_query($conn,"set names 'utf8'"); //数据库输出编码
	 
			mysqli_select_db($conn,$mysql_database); //打开数据库
			
			//$sql = "update `ds_orders` set paymethod = 1 , status = 1 where onumber = ".$onumber;
			$res = mysqli_query($conn,"select * from `ds_orders` where onumber = ".$onumber);
	        while($row = mysqli_fetch_array($res))
			{
				$oinfo = $row;
			}
			if($oinfo['o_type'] >= 0){/*增加生产力 S*/
				$user_id = $oinfo['uid'];
				$yttime = 86400;//一天时间戳
				$scldata['user_id'] = $user_id;
				$scldata['scl'] = intval($oinfo['odprice']*0.15);
				$scldata['rem'] = '购买商品';
				$scldata['add_time'] = time();
				$scldata['end_time'] = time()+($yttime*365);
				if($scldata['scl'] >= 1){
					$sclhquqq->add($scldata);
					//添加生产力
					mysqli_query($conn,"INSERT INTO `ds_sclhquqq` (user_id,scl,rem,add_time,end_time) VALUES ('".$scldata['user_id']."','".$scldata['scl']."','".$scldata['rem']."','".$scldata['add_time']."','".$scldata['end_time']."')");
					//$powder_log->add(array("user_id"=>$user_id,"reason"=>"购买产品","state"=>1,"val"=>$scldata['scl'],"add_time"=>date("Y-m-d H:i:s"),"status"=>3));
					//添加生产力变化log
					mysqli_query($conn,"INSERT INTO `ds_powder_log` (user_id,reason,state,val,add_time) VALUES ('".$scldata['user_id']."','购买产品','1','".$scldata['scl']."','".date("Y-m-d H:i:s")."')");
					//$user = $member->where(array("id"=>$user_id))->find();
					//获得购买者信息
					$resuser = mysqli_query($conn,"select * from `ds_member` where id = ".$user_id);
					while($rows = mysqli_fetch_array($resuser)){
						$user = $rows;
					}
					if($user['parent_id']){
						$pscldata['user_id'] = $user['parent_id'];
						$pscldata['scl'] = intval($scldata['scl']*0.2);
						$pscldata['rem'] = '粉丝购买商品';
						$pscldata['add_time'] = time();
						$pscldata['end_time'] = time()+($yttime*365);
						if($pscldata['scl'] >= 1){
							
							mysqli_query($conn,"INSERT INTO `ds_sclhquqq` (user_id,scl,rem,add_time,end_time) VALUES ('".$pscldata['user_id']."','".$pscldata['scl']."','".$pscldata['rem']."','".$pscldata['add_time']."','".$pscldata['end_time']."')");
							//$powder_log->add(array("user_id"=>$user['parent_id'],"reason"=>"粉丝购买商品","state"=>1,"val"=>$pscldata['scl'],"add_time"=>date("Y-m-d H:i:s"),"status"=>3));
							
							mysqli_query($conn,"INSERT INTO `ds_powder_log` (user_id,reason,state,val,add_time) VALUES ('".$user['parent_id']."','粉丝购买产品','1','".$pscldata['scl']."','".date("Y-m-d H:i:s")."')");
							//$sclhquqq->add($pscldata);
						}
					}
				}
			}/*增加生产力 E*/
			else{/*计算返利 S*/
				$user_id = $oinfo['uid'];
				//获得购买者信息
				$resuser = mysqli_query($conn,"select * from `ds_member` where id = ".$user_id);
					while($rows = mysqli_fetch_array($resuser)){
						$user = $rows;
				}
				//$user = $member->where(array("id"=>$user_id))->find();
				//获取订单内vip商品信息
				$resvipg = mysqli_query($conn,"select * from `ds_vipgods` where id = ".$oinfo["goods_id"]);
				while($rows = mysqli_fetch_array($resvipg)){
						$vipg = $rows;
				}
				//$vipg = $vipgods->where(array("id"=>$oinfo["goods_id"]))->find();
				
				
				/*判断自己等级低向高调 高不降*/
				if($user['dengji'] < $vipg['g_vip']){
					mysqli_query($conn,"update `ds_member` set dengji = ".$vipg['g_vip']." where id = ".$user_id);
					//$member->where(array("id"=>$user_id))->save(array("level"=>$vipg['g_vip']));
				}
				
				/*判断有无上级*/
				if($user['parent_id']){
					//获取上级会员信息
					$respuser = mysqli_query($conn,"select * from `ds_member` where id = ".$user['parent_id']);
					while($rows = mysqli_fetch_array($respuser)){
						$puser = $rows;
					}
					//$puser = $member->where(array("id"=>$user['parent_id']))->find();
					/*判断是否有VIP等级*/
					if($puser['dengji'] > 0){
						mysqli_query($conn,"update `ds_member` set balance = balance+".$vipg["V"+$puser['dengji']]." where id = ".$user['parent_id']);
						//$member->where(array("id"=>$user['parent_id']))->setInc('balance',$vipg["V"+$puser['level']]);
					}
				}
			}/*计算返利 E*/
			//修改订单状态
	 		mysqli_query($conn,"update `ds_orders` set paymethod = 1 , status = 1 where onumber = ".$onumber);
			mysqli_close($conn); //关闭 MySQL连接
            return sprintf("<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>");
        }
		
		


?>