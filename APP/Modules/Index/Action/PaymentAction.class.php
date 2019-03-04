<?php
Class PaymentAction extends CommonAction {
	public function h5wxpay(){
		$userip = $this->get_client_ip();     //获得用户设备IP
        $appid  = "wx25753f30873c0ae1";                  //应用APPID
        $mch_id = "1521578571";                  //微信支付商户号
        $key    = "nlsjfmq0co401px44h1gcyqmf44uwoit";                 //微信商户API密钥
        
        $data = $_POST;
        $out_trade_no = $data['onumber'];//平台内部订单号
        $data['total_amount']=floatval($data['total_amount']);
        $money= $data['total_amount']*100;                     //充值金额 微信支付单位为分
        $nonce_str = $this->createNoncestr(); //随机字符串
        $body = "购买商品：".$data['subject'];//内容
        $total_fee = $money; //金额
        $spbill_create_ip = $userip; //IP
        //$notify_url = "http://www.zgllsj.com/index.php/index/payment/h5_notifys"; //回调地址
        $notify_url = "http://www.zgllsj.com/Api/weixinh5/notify_url.php";
        //$notify_url="http://www.zgllsj.com/index.php/Index/Payment/h5_notifys";

        $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍
        $scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://ddsm.site","wap_name":"微信支付"}}';//场景信息 必要参数
        $signA ="appid=$appid&attach=$out_trade_no&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
        $strSignTmp = $signA . "&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确
        $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
        $post_data = "<xml>
                    <appid>$appid</appid>
                    <mch_id>$mch_id</mch_id>
                    <body>$body</body>
                    <out_trade_no>$out_trade_no</out_trade_no>
                    <total_fee>$total_fee</total_fee>
                    <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                    <notify_url>$notify_url</notify_url>
                    <trade_type>$trade_type</trade_type>
                    <scene_info>$scene_info</scene_info>
                    <attach>$out_trade_no</attach>
                    <nonce_str>$nonce_str</nonce_str>
                    <sign>$sign</sign>
            </xml>";//拼接成XML 格式
        $nto_html = urlencode("http://www.zgllsj.com/index.php/Index/Payment/chenggongym/onumber/".$out_trade_no);
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
        $dataxml = $this->postXmlCurl($post_data,$url); //后台POST微信传参地址  同时取得微信返回的参数
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        echo "<script>location.href='".$objectxml['mweb_url']."&redirect_url=".$nto_html."'</script>";
        //header($objectxml['mweb_url']);
	}
	public function zyh5wxpay(){
		$userip = $this->get_client_ip();     //获得用户设备IP
        $appid  = "wx25753f30873c0ae1";                  //应用APPID
        $mch_id = "1521578571";                  //微信支付商户号
        $key    = "nlsjfmq0co401px44h1gcyqmf44uwoit";                 //微信商户API密钥
        
        $data = $_POST;
		if($data['total_amount'] == 0){
        	$jhorder = M("jhorder");
        	$res = $jhorder->where(array('jho_number'=>$data['onumber']))->save(array('paymethod'=>1,'status'=>1));
        	echo "<script>location.href='http://www.zgllsj.com/index.php/Index/Payment/zychenggongym/onumber/".$data['onumber']."'</script>";
        }else{
			$out_trade_no = $data['onumber'];//平台内部订单号
			$data['total_amount']=floatval($data['total_amount']);
			$money= $data['total_amount']*100;                     //充值金额 微信支付单位为分
			$nonce_str = $this->createNoncestr(); //随机字符串
			$body = "兑换商品".$data['subject'];//内容
			$total_fee = $money; //金额
			$spbill_create_ip = $userip; //IP
			//$notify_url = "http://www.zgllsj.com/index.php/index/payment/h5_notifys"; //回调地址
			$notify_url = "http://www.zgllsj.com/Api/weixinh5/zynotify_url.php";
			//$notify_url="http://www.zgllsj.com/index.php/Index/Payment/h5_notifys";

			$trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍
			$scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://ddsm.site","wap_name":"微信支付"}}';//场景信息 必要参数
			$signA ="appid=$appid&attach=$out_trade_no&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
			$strSignTmp = $signA . "&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确
			$sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
			$post_data = "<xml>
						<appid>$appid</appid>
						<mch_id>$mch_id</mch_id>
						<body>$body</body>
						<out_trade_no>$out_trade_no</out_trade_no>
						<total_fee>$total_fee</total_fee>
						<spbill_create_ip>$spbill_create_ip</spbill_create_ip>
						<notify_url>$notify_url</notify_url>
						<trade_type>$trade_type</trade_type>
						<scene_info>$scene_info</scene_info>
						<attach>$out_trade_no</attach>
						<nonce_str>$nonce_str</nonce_str>
						<sign>$sign</sign>
				</xml>";//拼接成XML 格式
			$nto_html = urlencode("http://www.zgllsj.com/index.php/Index/Payment/zychenggongym/onumber/".$out_trade_no);
			$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
			$dataxml = $this->postXmlCurl($post_data,$url); //后台POST微信传参地址  同时取得微信返回的参数
			$objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
			echo "<script>location.href='".$objectxml['mweb_url']."&redirect_url=".$nto_html."'</script>";
			//header($objectxml['mweb_url']);
		}
	}
	/**
	 * 开通钱包支付
	 * Enter description here ...
	 */
	public function ktqianbao(){
		$userip = $this->get_client_ip();     //获得用户设备IP
        $appid  = "wx25753f30873c0ae1";                  //应用APPID
        $mch_id = "1521578571";                  //微信支付商户号
        $key    = "nlsjfmq0co401px44h1gcyqmf44uwoit";                 //微信商户API密钥
        
        $data = $_POST;
        $out_trade_no = $_SESSION['username'];//$_SESSION['username'];//平台内部订单号
       // $data['total_amount']=floatval($data['total_amount']);
        $money= 2*100;                     //充值金额 微信支付单位为分
        $nonce_str = $this->createNoncestr(); //随机字符串
        $body = "开通钱包";//内容
        $total_fee = $money; //金额
        $spbill_create_ip = $userip; //IP
        //$notify_url = "http://www.zgllsj.com/index.php/index/payment/h5_notifys"; //回调地址
        $notify_url = "http://www.zgllsj.com/Api/weixinh5/qianbao_url.php";
        //$notify_url="http://www.zgllsj.com/index.php/Index/Payment/h5_notifys";

        $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍
        $scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://www.zgllsj.com","wap_name":"微信支付"}}';//场景信息 必要参数
        $signA ="appid=$appid&attach=$out_trade_no&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
        $strSignTmp = $signA . "&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确
        
        $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
        
        $post_data = "<xml>
                    <appid>$appid</appid>
                    <mch_id>$mch_id</mch_id>
                    <body>$body</body>
                    <out_trade_no>$out_trade_no</out_trade_no>
                    <total_fee>$total_fee</total_fee>
                    <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                    <notify_url>$notify_url</notify_url>
                    <trade_type>$trade_type</trade_type>
                    <scene_info>$scene_info</scene_info>
                    <attach>$out_trade_no</attach>
                    <nonce_str>$nonce_str</nonce_str>
                    <sign>$sign</sign>
            </xml>";//拼接成XML 格式
        $nto_html = urlencode("http://www.zgllsj.com/index.php/Index/Payment/qianbao/id/".$out_trade_no);
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
        $dataxml = $this->postXmlCurl($post_data,$url); //后台POST微信传参地址同时取得微信返回的参数
        
        
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        echo "<script>location.href='".$objectxml['mweb_url']."&redirect_url=".$nto_html."'</script>";
	}
    public function h5wxpays(){
    	/*$orders = M("orders");
    	$id = $_GET['id'];
    	$data = $orders->where(array("oid"=>$id))->find();*/
    	$data = $_POST;
        $appid  = "wx25753f30873c0ae1";                  //应用APPID
        $mch_id = "1521578571";                  //微信支付商户号
        $key    = "nlsjfmq0co401px44h1gcyqmf44uwoit";                 //微信商户API密钥
    	$data['total_amount']=floatval($data['total_amount']);
     	$subject = $data['subject'];//商品描述
     	$total_amount = $data['total_amount']*100;
//　		$total_amount = 123; //金额
//		$additional = $data['additional']; ////附加数据
        $order_id = $data['onumber']; ////订单号
        $nonce_str = $this->createNoncestr(); //随机字符串
        $spbill_create_ip = $this->get_client_ip(); //终端ip
        //以上参数接收不必纠结，按照正常接收就行，相信大家都看得懂
        //$spbill_create_ip = '118.144.37.98'; //终端ip测试
		$trade_type = 'MWEB'; //交易类型 具体看API 里面有详细介绍
        $notify_url = 'http://www.zgllsj.com/Api/wexinh5/notify_url.php'; //回调地址
        $scene_info = '{"h5_info":{"type":"Wap","wap_url":"http://www.zgllsj.com","wap_name":"微信支付"}}'; //场景信息
        //对参数按照key=value的格式，并按照参数名ASCII字典序排序生成字符串
        $signA = "appid=$appid&attach=$order_id&body=$subject&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$order_id&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_amount&trade_type=$trade_type";
        $strSignTmp = $signA . "&key=$key"; //拼接字符串
        $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
        
        $post_data = "<xml>
                       <appid>$appid</appid>
                       <body>$subject</body>
                       <mch_id>$mch_id</mch_id>
                       <nonce_str>$nonce_str</nonce_str>
                       <notify_url>$notify_url</notify_url>
                       <out_trade_no>$order_id</out_trade_no>
                       <scene_info>$scene_info</scene_info>
                       <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                       <total_fee>$total_amount</total_fee>
                       <trade_type>$trade_type</trade_type>
                       <sign>$sign</sign>
                   </xml>"; //拼接成XML 格式
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder"; //微信传参地址
        $dataxml = $this->postXmlCurl($post_data,$url); //后台POST微信传参地址  同时取得微信返回的参数
        //$dataxml = $this->http_post($url, $post_data); //后台POST微信传参地址  同时取得微信返回的参数，http_post方法请看下文
        print_R($dataxml);exit;
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        
        if ($objectxml['return_code'] == 'SUCCESS') {
            if ($objectxml['result_code'] == 'SUCCESS') { //如果这两个都为此状态则返回mweb_url，详情看‘统一下单’接口文档
                return $objectxml['mweb_url']; //mweb_url是微信返回的支付连接要把这个连接分配到前台
            }
            if ($objectxml['result_code'] == 'FAIL') {
            	return $err_code_des = $objectxml['err_code_des'];
            }
        }
    }
    
	function http_post($url, $data) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_HEADER,0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $res = curl_exec($ch);
	    curl_close($ch);
	    return $res;
	}
public function createNoncestr( $length = 32 ){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    public function postXmlCurl($xml,$url,$second = 30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }else{
            $error = curl_errno($ch);
            curl_close($ch);
            echo "curl出错，错误码:$error"."<br>";
        }
    }
    /**
     * 获取当前IP地址
     * Enter description here ...
     */
	public function get_client_ip(){
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {

            $ip = getenv('HTTP_CLIENT_IP');

        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {

            $ip = getenv('HTTP_X_FORWARDED_FOR');

        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {

            $ip = getenv('REMOTE_ADDR');

        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {

            $ip = $_SERVER['REMOTE_ADDR'];

        }

        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }
    /**
     * 微信支付回调
     */
    public function h5_notifys(){
    	$orders = M("orders");
    	$data['paymethod'] = 1;
        $data['status'] = 1;
      	$res = $orders->where(array("oid"=>57))->save($data);exit;
        
        $testxml  = file_get_contents("php://input");
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $result = json_decode($jsonxml, true);//转成数组
    	if($result) {
            //如果成功返回了
            $onumber = $result['out_trade_no'];
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $orders = M("orders");
                $data['paymethod'] = 1;
                $data['status'] = 1;
                
				$member = M("member");
				$sclhquqq=M("sclhquqq");
				$powder_log=M("powder_log");
				$vipgods=M("vipgods");
				$oinfo = $orders->where(array("onumber"=>$onumber))->find();
				if($oinfo['o_type'] == 0){/*增加生产力 S*/
					$user_id = $oinfo['uid'];
					$yttime = 86400;//一天时间戳
					$scldata['user_id'] = $user_id;
					$scldata['scl'] = intval($oinfo['odprice']*0.15);
					$scldata['rem'] = '购买商品';
					$scldata['add_time'] = time();
					$scldata['end_time'] = time()+($yttime*365);
					if($scldata['scl'] >= 1){
						$sclhquqq->add($scldata);
						$powder_log->add(array("user_id"=>$user_id,"reason"=>"购买产品","state"=>1,"val"=>$scldata['scl'],"add_time"=>date("Y-m-d H:i:s"),"status"=>3));
						$user = $member->where(array("id"=>$user_id))->find();
						if($user['parent_id']){
							$pscldata['user_id'] = $user['parent_id'];
							$pscldata['scl'] = intval($scldata['scl']*0.2);
							$pscldata['rem'] = '粉丝购买商品';
							$pscldata['add_time'] = time();
							$pscldata['end_time'] = time()+($yttime*365);
							if($pscldata['scl'] >= 1){
								$powder_log->add(array("user_id"=>$user['parent_id'],"reason"=>"粉丝购买商品","state"=>1,"val"=>$pscldata['scl'],"add_time"=>date("Y-m-d H:i:s"),"status"=>3));
								$sclhquqq->add($pscldata);
							}
						}
					}
				}/*增加生产力 E*/
				else{/*计算返利 S*/
					$user_id = $oinfo['uid'];
					$user = $member->where(array("id"=>$user_id))->find();
					$vipg = $vipgods->where(array("id"=>$oinfo["goods_id"]))->find();
					
					
					/*判断自己等级低向高调 高不降*/
					if($user['level'] < $vipg['g_vip']){
						$member->where(array("id"=>$user_id))->save(array("level"=>$vipg['g_vip']));
					}
					
					/*判断有无上级*/
					if($user['parent_id']){
						$puser = $member->where(array("id"=>$user['parent_id']))->find();
						/*判断是否有VIP等级*/
						if($puser['level'] > 0){
							$member->where(array("id"=>$user['parent_id']))->setInc('balance',$vipg["V"+$puser['level']]);
						}
					}
				}/*计算返利 E*/
				
                $res = $orders->where(array("id"=>84))->save($data);
                return sprintf("<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>");
        	}
        }
    }
    /**
     * 回调页面
     */
    public function chenggongym(){
    	$onumber = $_GET['onumber'];
    	$orders = M("orders");
    	$info = $orders->where(array("onumber"=>$onumber))->find();
    	$this->assign("info",$info);
    	$this->display();
    }
    /**
     * ajax查询订单
     */
    public function ajax_zf(){
    	$oid = $_POST['oid'];
    	$orders = M("orders");
    	$od = $orders->where(array("oid"=>$oid))->find();
    	if($od){
    		if($od['paymethod'] == 1 && $od['status'] > 0){
	    		$res['msg'] = 3;
	    		$res['url'] = U("Index/Shop/order_info",array("oid"=>$od['oid']));
    		}
    		else{
	    		$res['msg'] = 2;
	    		$res['error'] = '请先确认付款成功！';
	    		$res['url'] = U("Index/Shop/order_info",array("oid"=>$od['oid']));
    		}
    	}
    	else{
    		$res['msg'] = 1;
    		$res['error'] = '参数错误！';
    	}
    	echo json_encode($res);
    }
    /**
     * 庄园商品支付回调页面
     */
    public function zychenggongym(){
    	$onumber = $_GET['onumber'];
    	$jhorder = M("jhorder");
    	$info = $jhorder->where(array("jho_number"=>$onumber))->find();
    	$this->assign("info",$info);
    	$this->display();
    }
    /**
     * 庄园回调ajax查询订单
     */
    public function zyajax_zf(){
    	$id = $_POST['id'];
    	$jhorder = M("jhorder");
    	$od = $jhorder->where(array("id"=>$id))->find();
    	if($od){
    		if($od['paymethod'] == 1 && $od['status'] > 0){
	    		$res['msg'] = 3;
	    		$res['url'] = U("Index/Shop/zyorder_info",array("oid"=>$od['oid']));
    		}
    		else{
	    		$res['msg'] = 2;
	    		$res['error'] = '请先确认付款成功！';
	    		$res['url'] = U("Index/Shop/zyorder_info",array("oid"=>$od['oid']));
    		}
    	}
    	else{
    		$res['msg'] = 1;
    		$res['error'] = '参数错误！';
    	}
    	echo json_encode($res);
    }
	/**
     * 钱包开通支付回调页面
     */
    public function qianbao(){
    	$id = $_GET['id'];
    	$member = M("member");
    	$info = $member->where(array("username"=>$id))->find();
    	$this->assign("info",$info);
    	$this->display();
    }
    /**
     * 钱包支付回调ajax查询
     */
    public function qianbao_zf(){
    	$id = $_POST['id'];
    	$member = M("member");
    	$od = $member->where(array("id"=>$id))->find();
    	if($od){
    		if($od['wallet_state'] == 1){
	    		$res['msg'] = 3;
	    		$res['url'] = U("Index/Index/index");
    		}
    		else{
	    		$res['msg'] = 2;
	    		$res['error'] = '请先确认付款成功！';
	    		$res['url'] = U("Index/Financial/wallet_kt");
    		}
    	}
    	else{
    		$res['msg'] = 1;
    		$res['error'] = '参数错误！';
    	}
    	echo json_encode($res);
    }
    
}
?>
