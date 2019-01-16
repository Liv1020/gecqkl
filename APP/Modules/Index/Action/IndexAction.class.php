<?php  
	
	Class IndexAction extends CommonAction{

		public function index(){
			$member = M('member');
			$username = session('username');
			$minfo = $member->where(array('username'=>$username,'id'=>$_SESSION['mid']))->find();
            $starttime = strtotime(date("Y-m-d",NOW_TIME));//今日时间戳	
			$endtime = $starttime + 86400;

			import('ORG.Util.BlockChain');
			$bc = new BlockChain();
            $minfo['wallet'] = $bc->findWallet($minfo['wallet_code'], $minfo['password']);

			$this->assign('minfo',$minfo);
			$ann = M('announce')->where(array('tid'=>3))->order('addtime desc')->limit(12)->select();
			$this->assign('ann',$ann);

        
			//新闻未读
			$user_id=session('mid');
			//已读
			$a_read=M("announce_click")->where("user_id = {$user_id}")->count();
			//总数
			$total_news=M("announce")->count();
			$w_read=$total_news-$a_read;
			
			//取出最新的新闻
			
			
			$news_info=M("announce")->where("id >0")->order("addtime desc")->find();
			
			
			$bzjstatus = $minfo['bzjstatus'];
			
			$this->assign('bzjstatus',$bzjstatus);
			$this->assign('news_info',$news_info);
			$this->assign('w_read',$w_read);
			$this->assign('list',$info);
			$this->display();
		}

		//身份等级
		public function showLevelIntro(){
			$this->display();
		}
		/**
		 * 我的好友
		 */
		public function haoyou(){
			
		}

		//获取游戏账号总数
		public function getGroupInfo(){
			if (session('verify') != I('yzm','','md5')) {
				echo '<script>alert("請輸入正确的安全碼！");window.history.back(-1);</script>';
				die;
			}
            //直属账号
            $member = M('member')->where(array('username'=>  session('username')))->find();
            $this->assign('parentcount',$member['parentcount']);
            //游戏账号总数gamecount
            $this->assign('gamecount',$member['gamecount']);
            //游戏账号有效数validgamecount
            $this->assign('validgamecount',$member['validgamecount']);
			$this->display();
		}

		//游戏账号总数前的验证
		public function getCheck(){
			$this->display();
		}

		/**
		 * 生成验证码
		*/
		public function verify(){
			ob_clean();
			import('ORG.Util.Image');
			Image::buildImageVerify(4,1,'png',55,25);
		}

		//AJAX返回查询到的新闻
		public function ajaxAnn(){
			//判断是否异步提交
			IS_AJAX or halt('对不起，页面不存在');
			$ann = M('announce'); 
			$data = $ann->where(array('id'=>I('id')))->find();
			$html = htmlModel($data['title'],$data['content']);
			echo $html;
		}

		//退出系统
		public function logout(){
			//添加日志
			$desc = '会员'. session('account') .'登出';
			write_log(session('account'),'member',$desc);

			//销毁session
			//session('[destroy]');
			session('mid',null);
			session('username',null);
			session('member',null);
			session('usersecondlogin',null);
			$this->redirect(GROUP_NAME.'/Login/index');
			//$this->redirect(U('Index/Login/index'));
		}

		//会员充值
		public function recharge(){
			
		if(C('recharge_is')==0){
				
				$this->error('充值已经关闭');	
		}
		  $recharge_type=C('recharge_type');
		  $type=I('get.type',0,'intval');	
			
		  import('ORG.Util.Page');
		  $count = M('member_recharge')->where("user_id = ".session('mid'))->count();
		  $page = new Page($count);
		  $show = $page->show();// 分页显示输出
		  $list = M('member_recharge')->where("user_id = ".session('mid'))->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();

		  $this->assign('list',$list);
		  $this->assign('page',$show);
		  $this->assign('type',$type);
		  $this->assign('recharge_type',$recharge_type);
		 $this->display('recharge_user');
				
		}



	public function recharge_post(){
		
			$pay_type=I('post.pay_type','','intval');
			$fkzh=I('post.fkzh','','intval');
			$rmb=I('post.rmb',0,'floatval');
			$note=I('post.note','','trim');
			if(empty($pay_type)){
				$this->ajaxReturn(array('result'=>0,'info'=>'请选择充值方式'));
				//$this->error("请选择充值方式");
					
			}
			if(empty($fkzh)){
				$this->ajaxReturn(array('result'=>0,'info'=>'请填写充值账号'));
				//$this->error("请填写充值账号");
					
			}
			if(empty($rmb)){
				$this->ajaxReturn(array('result'=>0,'info'=>'请填写充值金额'));
				//$this->error("请填写充值金额");	
			}
			
			//查看之前是否有充值改金额并且没有 支付的记录
			$user_id=session('mid');
			$is_rocde=M("member_recharge")->where("user_id = {$user_id} and rmb = {$rmb} and status = 0")->count();
			if(!empty($is_rocde)){
				$this->ajaxReturn(array('result'=>0,'info'=>'没有支付？请扫码支付后等待审核'));
			}
			
			$recharge_min=C('recharge_min');
			$recharge_max=C('recharge_max');
			$recharge_proportion=C('recharge_proportion');
			
			if($rmb < $recharge_min || $rmb > $recharge_max){
				$this->ajaxReturn(array('result'=>0,'info'=>'充值金额在'.$recharge_min.'-'.$recharge_max.'之间'));	
				//$this->error('充值金额在'.$recharge_min.'-'.$recharge_max.'之间');	
					
			}
			$data=array();
			$data['user_id']=session('mid');
			$data['order_sn']=date('YmdHis').rand(100,999).session('mid');
			$data['fkzh']=$fkzh;
			$data['rmb']=$rmb;
			$data['gbc']=$rmb*$recharge_proportion;
			$data['bili']=$recharge_proportion;
			$data['note']=$note;
			$data['pay_type']=$pay_type;
			$data['add_time']=time();
			$result=M('member_recharge')->add($data);
			
			if(!empty($result)){
				//在线支付调用
				/*$parter =1808;  //商家Id
				$key = '1b4186de07a34089a5aeaff73d6fe12d'; //商家密钥
				$type = $pay_type;   //商家密钥
				$value = $rmb;    //提交金额
				//$value = 0.1;    //提交金额
				$orderid = $data['order_sn'];   //订单Id号
				$callbackurl = "http://".$_SERVER['SERVER_NAME']."/index/sem/notify";//异步回调地址	//U('Index/Sem/notify'); //下行url地址
				$hrefbackurl = "http://".$_SERVER['SERVER_NAME']."/index/index/recharge";//U('Index/Index/recharge'); //下行url地址
				
				$url = "parter=". $parter ."&type=". $type ."&value=". $value. "&orderid=". $orderid ."&callbackurl=". $callbackurl;
				//签名
				$sign	= md5($url. $key);	
				//最终url
				$url	= "http://api.cindnn.cn/bank/?" . $url . "&sign=" .$sign. "&hrefbackurl=". $hrefbackurl;*/
				//header("location:" .$url);	
				//exit;
					
				$this->ajaxReturn(array('result'=>1,'info'=>'提交成功','url'=>U('Index/Index/recharge',array('type'=>$pay_type))));		
			}else{
				$this->ajaxReturn(array('result'=>0,'info'=>'充值提交失败！'));
				//$this->error("充值提交失败");			
			}
			
			
			
			
			
			
			
				
	}


	//会员充值记录
	  public function recharge_list(){
		  	
			
		  
		  import('ORG.Util.Page');
		  $count = M('member_recharge')->where("user_id = ".session('mid'))->count();
		  $page = new Page($count);
		  $show = $page->show();// 分页显示输出
		  $list = M('member_recharge')->where("user_id = ".session('mid'))->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		  $this->assign('list',$list);
		  $this->assign('page',$show);
		  $this->display();
			  
	  }
	public function production(){
		$user_id=$_SESSION['mid'];
		$member=M("member");
		$user=$member->where(array('id'=>$user_id))->find();
		
		$this->assign("user",$user);
		$this->display();
	}



	public function jiesuan(){
		
			
			$id=I('post.id',0,'intval');
			$user_id=session('mid');
			$username=session('username');
			if(empty($id)){
				$this->ajaxReturn(array('result'=>0,'info'=>'参数丢失！'));			
			}
			
			$order=M('order')->where("id = {$id} and zt = 1 and user_id = {$user_id}")->find();
			
			if(empty($order)){
				$this->ajaxReturn(array('result'=>0,'info'=>'矿机不存在！'));			
			}
			//判断与上次结算时间有没有达到24小时
			
			$jiesuan_time=C('jiesuan_time');
			if(empty($jiesuan_time)){
					$jiesuan_time=24;
			}
			
			if(time()-$order['UG_getTime'] < $jiesuan_time*3600){
				$this->ajaxReturn(array('result'=>0,'info'=>'结算间隔不到'.$jiesuan_time.'小时！'));		
			}
			//算出已经结算的时间
			$a_time=$order['UG_getTime']-strtotime($order['addtime']);
			
			//本次将要结算的时间 
			$n_time=time()-$order['UG_getTime'];
			
			$time=0;//参加计算的时间；
			$data=array();
			$data['UG_getTime']=time();
			$is_over=1;
			if($a_time+$n_time > $order['yxzq']*3600){
				
					$time=($order['yxzq']*3600)-$a_time;
					$data['zt']=2;
					//扣除我的算力
					M('member')->where(array('id'=>$user_id))->setDec("mygonglv",$order['lixi']);
					$is_over=0;
				
			}else{
				    $time=$n_time;	
			}  
			
			$shouyi=($time/3600)*$order['kjsl'];//本次收益
			
			M('order')->where("id = {$id} and zt = 1 and user_id = {$user_id}")->setInc('already_profit',$shouyi);
			M('order')->where("id = {$id} and zt = 1 and user_id = {$user_id}")->save($data);
			
			M('member')->where("id = {$user_id}")->setInc("jinbi",$shouyi);
			account_log($username,$shouyi,'矿机结算收益',1,1,1,$order['id']);
			//5代数收益  C('tjj_1');
			$p_id=M('member')->where("id = {$user_id}")->getField('parent_id');
			
			if(!empty($p_id)){
				for($i=1;$i<=6;$i++){
					$p_userinfo=M('member')->where("id = {$p_id}")->find();
					if(!empty($p_userinfo)){//$p_userinfo['level']
						$group=M("member_group")->where(array("level"=>$p_userinfo['level']))->find();
						if($group['ldj'] >=$i){//判断是否可以分到代数
							$fl_bi=C('tjj_'.$i);
							$p_shouyi=$shouyi*$fl_bi;
							M('member')->where("id = {$p_id}")->setInc("jinbi",$p_shouyi);
							if(empty($is_over)){
								M('member')->where("id = {$p_id}")->setDec("teamgonglv",$order['lixi']);	
							}
							account_log($p_userinfo['username'],$p_shouyi,$i.'/'.$user_id,1,2);
						}
						
						$p_id=$p_userinfo['parent_id'];
						if(empty($p_id)){
							break;		
						}
					}else{
						break;	
					}
				}
			}
			$this->ajaxReturn(array('result'=>1,'info'=>'结算成功！'));			
	}

	/**
	 * 收货地址添加
	 */
	public function address(){
		$address=M("address");
		$member=M("member");
		$sclhquqq=M("sclhquqq");
		$user_id=session('mid');
		if(IS_POST){
			$data['user_id']=$user_id;
			$data['name']=$_POST['name'];
			$data['tel']=$_POST['tel'];
			$data['area']=$_POST['area'];
			$data['address']=$_POST['address'];
			$data['if_default']=1;
			$address->where(array("user_id"=>$user_id))->save(array("if_default"=>0));
			//30天时间戳
			$endtm = 86400*30;
			$res=$address->add($data);
			if($res){
				$user=$member->where(array("id"=>$user_id))->find();
				if($user['area_scl'] == 0){
					$member->where(array("id"=>$user_id))->save(array("area_scl"=>5,"area_time"=>time()));
					$scl['user_id'] = $user_id;
					$scl['scl'] = 5;
					$scl['rem'] = '添加地址';
					$scl['add_time'] = time();
					$scl['end_time'] = time()+$endtm;
					$sclhquqq->add($scl);
				}
				$this->success("添加成功！",U("Index/index/address_list"));
				exit;
			}
			else{
				$this->error("添加失败！");
				exit;
			}
		}
		
		$this->display();
	}
	/**
	 * 收货地址列表
	 */
	public function address_list(){
		$user_id=session('mid');
		$address=M("address");
		$adr=$address->where(array("user_id"=>$user_id))->select();
		$this->assign("list",$adr);
		$this->display();
	}
	/**
	 * 商学院
	 */
	public function shangxy(){
		$user_id=$_SESSION['mid'];
		$member=M('member');
		$sclhquqq=M("sclhquqq");
		$user=$member->where(array("id"=>$user_id))->find();
		//30天时间戳
		$endtm = 86400*30;
		if($user['sxy_scl'] == 0){
			$member->where(array("id"=>$user_id))->save(array('sxy_scl'=>5,'sxy_time'=>time()));
			$scl['user_id'] = $user_id;
			$scl['scl'] = 5;
			$scl['rem'] = '商学院';
			$scl['add_time'] = time();
			$scl['end_time'] = time()+$endtm;
			$sclhquqq->add($scl);
		}
	}
	/**
	 * 操作指南
	 */
	public function caozuo(){
		$user_id=$_SESSION['mid'];
		$member=M('member');
		$sclhquqq=M("sclhquqq");
		$user=$member->where(array("id"=>$user_id))->find();
		//30天时间戳
		$endtm = 86400*30;
		if($user['cz_scl'] == 0){
			$member->where(array("id"=>$user_id))->save(array('cz_scl'=>5,'cz_time'=>time()));
			$scl['user_id'] = $user_id;
			$scl['scl'] = 5;
			$scl['rem'] = '操作指南';
			$scl['add_time'] = time();
			$scl['end_time'] = time()+$endtm;
			$sclhquqq->add($scl);
			
		}
	}
	/**
	 * 生产力记录
	 */
	public function scljilu(){
		$user_id=$_SESSION['mid'];
		$member=M('member');
		$announce=M('announce');
		$sclhquqq = M("sclhquqq");
		
		$user_id = $_SESSION['mid'];
		$info=$announce->where(array("id"=>136))->find();
		
		$user = $member->where(array("id"=>$user_id))->find();
		$scl = $sclhquqq->where(array("user_id"=>$user_id,"end_time"=>array("gt",time())))->field("sum(scl) as zcl")->find();
		$nl = $scl['zcl']+$user['shengchanli'];
		
		$this->assign('nl',$nl);
		$this->assign("info",$info);
		$this->display();
	}
	/**
	 * 记录详情
	 */
	public function powder(){
		$powder_log=M("powder_log");
		$user_id=$_SESSION['mid'];
		if($_GET['status'] != ''){
			$status=$_GET['status'];
			$list=$powder_log->where(array("user_id"=>$user_id,'status'=>$status))->select();
			$this->assign("list",$list);
		}
		else{
			$this->error("参数错误！");
			exit;
		}
		$this->display();
	}
	/**
	 * VIP商品
	 */
	public function vipgods(){
		$vipgods = M("vipgods");
		$adv = M("adv");
		$gods_list = $vipgods->where()->select();
		foreach($gods_list as $key=>$val){
			$gpic=explode(",",$val['g_pic']);
			$gods_list[$key]['gpic'] = $gpic[0];
		}
		
		$adv_list = $adv->where(array('pos_id'=>2))->order("id desc")->select();
		$this->assign('adv_list',$adv_list);
		$this->assign("gods_list",$gods_list);
		$this->display();
	}
	/**
	 * VIP商品详情
	 */
	public function vipgods_info(){
		$id = $_GET['id'];
		$vipgods = M("vipgods");
		$grade = M("grade");
		$info = $vipgods->where(array("id"=>$id))->find();
		
		$sxw_goodsPic = explode(',', $info['g_pic']);
		$this->assign("info",$info);
		$this->assign('sxw_goodsPic',$sxw_goodsPic);
		$this->display();
	}
	/**
	 *加入订单前
	 */
	public function vip_jrdd(){
		$vipgods=M("vipgods");
		$user_id=$_SESSION['mid'];
		
		$goods_id=$_GET['goods_id'];
		$num=$_GET['qty_item_1'];
		$gds=$vipgods->where(array("id"=>$goods_id))->find();
		
		$gds['odprice']=$gds['g_price']*$num;
		$sxw_goodsPic = explode(',', $gds['g_pic']);
		$gds['pic']=$sxw_goodsPic[0];
		$address=M("address");
		$adr=$address->where(array("user_id"=>$user_id,"if_default"=>1))->find();
		$this->assign("adr",$adr);
		$this->assign("gds",$gds);
		$this->assign("num",$num);
		$this->display();
	}
	/**
	 * 加入订单
	 */
	public function vip_order(){
		$goods=M("vipgods");
		$orders=M("orders");
		$member=M("member");
		//print_r($_POST);
		if($_GET['oid']){
			$info=$orders->where(array("oid"=>$_GET['oid']))->find();
			$this->assign('info',$info);
		}
		if(IS_POST){
			$data['goods_id'] = $_POST['goods_id'];
			$data['goods_num'] = $_POST['goods_num'];
			$data['shangname'] = $_POST['shangname'];
			$data['paymethod'] = 0;
			$data['goods_price'] = $_POST['goods_price'];
			$data['order_price'] = $_POST['order_price'];
			$data['otime'] = date("Y-m-d H:i:s");
			$data['name'] = $_POST['name'];
			$data['photo'] = $_POST['photo'];
			$data['onumber'] = date("YmdHis",time()).rand(9,100);
			$data['deliveryaddress'] = $_POST['deliveryaddress'];
			$data['uid'] = $_SESSION['mid'];
			$data['username'] = 0;
			$data['o_type'] = 1;
			$res=$orders->add($data);
			if($res){
				$info=$orders->where(array("oid"=>$res))->find();
				$this->assign('info',$info);
			}
			else{
				$this->error("下单失败！");
				exit;
			}
		}
		$this->display();
	}
	/**
	 * 订单列表
	 */
	public function order_list(){
		$member=M("member");
		$vipgods=M("vipgods");
		$orders=M("orders");
		$user_id=$_SESSION['mid'];
		$where['uid'] = $user_id;
		$where['o_type'] = 1;
		if($_GET['state'] != ''){
			$where['status'] = $_GET['state'];
		}
		
		$olist = $orders->where($where)->order("oid desc")->select();
		foreach($olist as $key=>$val){
			$ginfo = $vipgods->where(array("id"=>$val['goods_id']))->find();
			$sxw_goodsPic = explode(',', $ginfo['g_pic']);
			$olist[$key]['gpic'] = $sxw_goodsPic[0];
			$olist[$key]['g_vip'] = $ginfo['g_vip'];
		}
		$this->assign('state',$_GET['state']);
		$this->assign('list',$olist);
		$this->display();
	}
	/**
	 * 订单详情
	 */
	public function order_info(){
		$member=M("member");
		$vipgods=M("vipgods");
		$orders=M("orders");
		$address=M("address");
		$user_id=$_SESSION['mid'];
		$oid=$_GET['oid'];
		$info=$orders->where(array("oid"=>$oid))->find();
		$ginfo=$vipgods->where(array("id"=>$info['goods_id']))->find();
		
		$sxw_goodsPic = explode(',', $ginfo['g_pic']);
		$info['pic']=$sxw_goodsPic[0];
		$info['g_vip'] = $ginfo['g_vip'];
		//$info['songnl']=intval($gds['order_price']/3);
		$adr=$address->where(array("user_id"=>$user_id,"if_default"=>1))->find();
		$this->assign("info",$info);
		$this->assign("adr",$adr);
		$this->display();
	}
	/**
	 * VIP商品分享
	 */
	public function vip_fx(){
		header ( "Content-type: text/html; charset=utf-8");
		
		$e_keyid=encrypt(session('mid'),'E','xyb8888');
		
		$e_keyid=str_replace('/','AAABBB',$e_keyid);
		
		$tuiguangma = "http://".$_SERVER['SERVER_NAME'].U('Index/Sem/regSem',array('u'=>$e_keyid));
		$erwei = M("member")->where(array('username'=>session('username')))->getField("erwei");
		
		if(!$erwei){
			Vendor('phpqrcode.phpqrcode');
			//生成二维码图片
			$object = new QRcode;
			$level=3;
			$size=6;
			$errorCorrectionLevel =intval($level) ;//容错级别
			$matrixPointSize = intval($size);//生成图片大小
			$path = "Public/erwei/";
			// 生成的文件名
			$fileName = $path.session('username').'.png';
			$object->png($tuiguangma,$fileName, $errorCorrectionLevel, $matrixPointSize, 2);
			import('ORG.Util.Image');
			$Image = new Image();		
			
			define('THINKIMAGE_WATER_CENTER', 5);
			$Image->water(PUBLIC_PATH.'/encard.jpg',$fileName,$fileName,100,array(240,350));	
            $erwei = '/'.$fileName;		
			M("member")->where(array('username'=>session('username')))->setField("erwei",$erwei);
		}
		$id = $_GET['id'];
		$vipgods = M("vipgods");
		$info = $vipgods->where(array("id"=>$id))->find();
		$gpic=explode(",",$info['g_pic']);
		$info['gpic'] = $gpic[0];
		$this->assign("info",$info);
        $this->assign('erwei',$erwei);//二维码路径
		$this->assign('tuiguangma',$tuiguangma);
//		$adurl=C('adurl');
//		$adurl2=str_replace('[adurl]',$tuiguangma,$adurl);
//		$user=M("member")->where(array("id"=>session('mid')))->find();
//		$this->assign("username",$user['username']);
//		$this->assign('tuiguangma',$tuiguangma);
//		$this->assign('adurl2',$adurl2);
		$this->display();
	}
	/**
	 * 公益池
	 */
	public function gongyichi(){
		$gyc = M("gyc");
		$member = M("member");
		$sumzz = $gyc->field("sum(yield) as zshu")->find();//公益池总量
		import('ORG.Util.Page');
		$count = $gyc->where("user_id > 0")->count();
		$page = new Page($count);
		$show = $page->show();// 分页显示输出
		$list = $gyc->where("user_id > 0")->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		foreach($list as $key=>$val){
			$user = $member->where("id = ".$val['user_id'])->find();
			$list[$key]['name'] = $user['truename'];
			
		}
		$this->assign("zshu",round($sumzz['zshu'],4));
		$this->assign("page",$show);
		$this->assign("list",$list);
		$this->display();
	}
	/**
	 * 现金
	 */
	public function money(){
		$user_id = $_SESSION['mid'];
		$member = M("member");
		$user = $member->where(array("id"=>$user_id))->find();
		
		$this->assign("user",$user);
		$this->display();
	}
	/**
	 * 现金体现
	 */
	public function money_tx(){
		$user_id = $_SESSION['mid'];
		$member = M("member");
		if(IS_POST){
			$tqcash = M("tqcash");
			$data['account'] = $_POST['account'];
			$data['balance'] = $_POST['balance'];
			$data['user_id'] = $user_id;
			$data['add_time'] = date("Y-m-d H:i:s");
			$res = $tqcash->add($data);
			if($res){
				$member->where(array("id"=>$user_id))->setDec("balance",$data['balance']);
				$this->success("提现申请成功！",U("Index/Index/money"));
			}
			else{
				$this->error("提现申请失败！");
			}
			exit;
		}
		$user = $member->where(array("id"=>$user_id))->find();
		$this->assign("user",$user);
		$this->display();
	}
}
?>