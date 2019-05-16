<?php  
//账号管理控制器
Class  ShopAction extends CommonAction{

    //商品列表
    public function plist(){
		$type = M("type");
		$adv = M('adv');
		$product = M("product");
		if($_GET['id']){	
	        $where = "tid={$_GET['id']}";
        }else{
			$where = "1=1";
		}		   
		$where.=" and is_on = 0" ;   
		$where.=" and id != 1"; 
		import('ORG.Util.Page');
		$count = $product -> where($map)->count();
		$Page  = new Page($count,5);
		$show = $Page -> show();
		$typeData = $product -> where($where) ->order("id asc") -> limit($Page ->firstRow.','.$Page -> listRows) -> select();
					
		//遍历主栏目
		$type =M('type');
		$adv_list = $adv->where(array('pos_id'=>4))->order("id desc")->select();
		$this->assign('adv_list',$adv_list);
		$data = $type -> where("pid=0") -> select();
		$this ->assign("page",$show);
		$this ->assign("types",$data);
		$this->assign("typeData",$typeData);
        $this->display();
    }
	//购物商城 
    public function shop(){
        $f = M('Goods');
        $goods = $f -> order('gissuetime asc')->select();
        foreach ($goods as $key => $value) {   
			$pics = explode(',', $value['gpic']);
			$goods[$key]['gpic'] = $pics[0];
		}
		$banner_list=M('shopbanner')->order('sort DESC')->select();
        $this -> assign('banner_list',$banner_list);
        $this -> assign('goods',$goods);
        $this -> display();
    }
	/**
	 * 金种子庄园
	 */
	public function jzzgoods(){
		$jzzgoods=M("jzzgoods"); 
		$adv = M('adv');
		import('ORG.Util.Page');
		$count = $jzzgoods ->count();
		$Page  = new Page($count,10);
		$show = $Page -> show();
		$jzz_glist=$jzzgoods -> limit($Page ->firstRow.','.$Page -> listRows)->select();
		foreach($jzz_glist as $key=>$val){
			$gpic=explode(",",$val['gpic']);
			$jzz_glist[$key]["gpic"]=$gpic[0];
		}
		
		$adv_list = $adv->where(array('pos_id'=>6))->order("id desc")->select();
		$this->assign('adv_list',$adv_list);
		$this->assign('goods_list',$jzz_glist);
		$this->assign("page",$show);
		$this->display();
	}
	/**
	 * 金种子商城
	 */
	public function jzzshop(){
		$classify = M('classify');
		$goods_model = M('goods');
		$adv = M('adv');
		import('ORG.Util.Page');
		$class_list=$classify->where("parentid = 0")->select();
		
		$count=$goods_model->where(array("is_sy"=>0))->count();
		$Page  = new Page($count,50);
		$show = $Page -> show();
		$goods=$goods_model->where(array("is_sy"=>0))->limit($Page ->firstRow.','.$Page -> listRows)->select();
		foreach($goods as $k=>$v){
			$gpic=explode(",",$v['gpic']);
			$goods[$k]["gpic"]=$gpic[0];
			$goods[$k]['songnl']=intval($v['zsscl']);//0.15
		}
		$goods_list[]=$goods;
		foreach($class_list as $key=>$val){
			$count=$goods_model->where(array("pid1"=>$val['cid'],"is_sy"=>0))->count();
			/*$Page  = new Page($count,10);
			$show[$key] = $Page -> show();*/
			$goods=$goods_model->where(array("pid1"=>$val['cid'],"is_sy"=>0))->select();//limit($Page ->firstRow.','.$Page -> listRows)->
			//print_r($goods);
			foreach($goods as $k=>$v){
				$gpic=explode(",",$v['gpic']);
				$goods[$k]["gpic"]=$gpic[0];
				$goods[$k]['songnl']=$v['zsscl'];//0.15
			}
			$goods_list[]=$goods;
		}
		//print_R($goods_list);
		
		$adv_list = $adv->where(array('pos_id'=>3))->order("id desc")->find();
		$this->assign('adv_list',$adv_list);
		$this->assign("page",$show);
		$this->assign("goods_list",$goods_list);
		$this->assign("class_list",$class_list);
		$this->display();
	}
	/**
	 * 商城商品
	 */
	public function scgoodsinfo(){
		$items = M('goods');
		$gao = C('max_danjia');
		$rmb_hl = C('rmb_hl');
		$itemsdata = $items->find($_GET['gid']);
		$area = "";
		if($itemsdata['is_sy']){
			$address = $this->regeo($itemsdata['longitude'],$itemsdata['dimension']);
			$area = $address['regeocode']['formatted_address'];
			
			if(is_array($area)){
				$area = "无法获取地理位置";
			}
		}
		$this->assign("area",$area);
		$itemsdata['songnl']=intval($itemsdata['zsscl']);//0.15
		// 商品的缩略图
		$sxw_goodsPic = explode(',', $itemsdata['gpic']);
		// 实例化评价表
		$list2 = M('goodsreview');
		// 计算一共有多少个
		$total = $list2->field('integral')->where($_GET)->select();
		$count = $list2->where($_GET)->count();
		$sum = 0;
		$sum1 = 0;
		// 计算评价的等级
		foreach ($total as $value) {
			$sum +=5;
			$sum1 += $value['integral'];
		}
		// 将等级发送前台
		$nums  = ($sum1/$sum)*100;
		// 遍历用户评论
		$reviewlist = M();
		$reviewdata = $reviewlist->table('sx_member m,sx_goodsreview r')->field('m.uname,r.integral,r.content')->where('r.uid=m.uid and r.gid='.$_GET['gid'])->order('r.rid desc')->limit(20)->select();
		// 遍历同品牌的商品
		$branddata = $items->where('bid='.$itemsdata['bid'].' and gid!='.$_GET['gid'])->field('gid,gpic,gname,goldprice')->order('gsellnums desc')->limit(5)->select();
		// 循环修改图片
		foreach ($branddata as $key => $value) {
			$branddata[$key]['gpic'] = explode(',', $value['gpic'])[0];
		}
		
		//检查该产品是否已经存在购物车中
		$gouwuche = S('gouwuche_' . $_SESSION['mid']);
		$flag = false;
		
		if(!empty($gouwuche)){
			foreach($gouwuche as $key => $val){
				if($_GET['gid'] == $val['gid']) $flag = true;
			}
		}
		
		$this->assign('flag', $flag);
		$this->assign('branddata',$branddata);
		//$this->assign('spjiage',$spjiage);
		$this->assign('reviewdata',$reviewdata);
		$this->assign('nums',$nums);
		$this->assign('count',$count);
		$this->assign('line',$line);
		// 商品的基本信息
		// 将商品数据放往前台
		$this->assign('item',$itemsdata);
		$this->assign('title',$itemsdata['gname']);
		// 将图片路径发往前台
		$this->assign('sxw_goodsPic',$sxw_goodsPic);
		$this->display();
	}
	/**
	 * 溯源商品
	 */
	public function sy_goods(){
		$goods_model = M('goods');
		import('ORG.Util.Page');
		
		$count=$goods_model->where(array("is_sy"=>1))->count();
		$Page  = new Page($count,10);
		$show = $Page -> show();
		$goods=$goods_model->where(array("is_sy"=>1))->limit($Page ->firstRow.','.$Page -> listRows)->select();
		foreach($goods as $k=>$v){
			$gpic=explode(",",$v['gpic']);
			$goods[$k]["gpic"]=$gpic[0];
			$goods[$k]['songnl']=$v['zsscl'];//0.15
		}
		$goods_list=$goods;
		
		$this->assign("page",$show);
		//$this->assign("pages",$shows);
		$this->assign("goods_list",$goods_list);
		$this->display();
	}
	/**
	 * 商城产品购买
	 */
	public function jrgwc(){
		$goods=M("goods");
		$user_id=$_SESSION['mid'];
		
		$goods_id=$_GET['goods_id'];
		$num=$_GET['qty_item_1'];
		$gds=$goods->where(array("gid"=>$goods_id))->find();
		$gds['odprice']=$gds['goldprice']*$num;
		$gds['songnl']=intval($gds['zsscl']);//0.15
		$sxw_goodsPic = explode(',', $gds['gpic']);
		$gds['pic']=$sxw_goodsPic[0];
		$address=M("address");
		$adr=$address->where(array("user_id"=>$user_id,"if_default"=>1))->find();
		$this->assign("adr",$adr);
		$this->assign("gds",$gds);
		$this->assign("num",$num);
		$this->display();
	}
	/**
	 * 提交订单
	 */
	public function scorder(){
		$goods=M("goods");
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
			$gds = $goods->where(array("gid"=>$_POST['goods_id']))->find();
			$data['username'] = $gds['username'];
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
	 * 订单确认收货
	 */
	public function shorder(){
		$oid = $_GET['oid'];
		$orders=M("orders");
		if($oid){
			$res=$orders->where(array("oid"=>$_GET['oid']))->update(array("status"=>3));
			if($res){
				$this->success("确认收货成功！",Url("Shop/order_info",array("oid"=>$oid)));
			}
			else{
				$this->error("确认收货失败！");
			}
		}
		else{
			$this->error("订单参数错误！");
		}
	}
	/**
	 * 订单添加
	 */
	public function order_add(){
		$oid = $_GET['id'];
		$user_id = $_SESSION['mid'];
		$member = M("member");
		$orders = M("orders");
		$sclhquqq=M("sclhquqq");
		$oinfo = $orders->where(array("oid"=>$oid))->find();
		$yttime = 86400;//一天时间戳
		$scldata['user_id'] = $user_id;
		$scldata['scl'] = intval($oinfo['odprice']*1);//0.15
		$scldata['rem'] = '购买商品';
		$scldata['add_time'] = time();
		$scldata['end_time'] = time()+($yttime*365);
		$sclhquqq->add($scldata);
		$user = $member->where(array("id"=>$user_id))->find();
		if($user['parent_id']){
			$pscldata['user_id'] = $user_id;
			$pscldata['scl'] = intval($scldata['scl']*0.2);
			$pscldata['rem'] = '粉丝购买商品';
			$pscldata['add_time'] = time();
			$pscldata['end_time'] = time()+($yttime*365);
			if($pscldata['scl'] >= 1){
				$sclhquqq->add($pscldata);
			}
		}
		
		$res=$orders->where(array("oid"=>$oid))->save(array("status"=>1,"paymethod"=>2));
		if($res){
			$this->success("添加成功！",U("Index/shop/order_list"));
			
		}
		else{
			$this->error("添加失败");
		}
		
	}
	/**
	 * 订单列表
	 */
	public function order_list(){
		$member=M("member");
		$goods=M("goods");
		$orders=M("orders");
		$user_id=$_SESSION['mid'];
		$where['uid'] = $user_id;
		$where['o_type'] = 0;
		if($_GET['state'] != ''){
			$where['status'] = $_GET['state'];
		}
		
		$olist=$orders->where($where)->order("oid desc")->select();
		foreach($olist as $key=>$val){
			$ginfo=$goods->where(array("gid"=>$val['goods_id']))->find();
			$sxw_goodsPic = explode(',', $ginfo['gpic']);
			$olist[$key]['gpic']=$sxw_goodsPic[0];
			$olist[$key]['goods_spec']=$ginfo['goods_spec'];
			$olist[$key]['songnl']=intval($ginfo['zsscl']*$val['goods_num']);//0.15
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
		$goods=M("goods");
		$orders=M("orders");
		$address=M("address");
		$user_id=$_SESSION['mid'];
		$oid=$_GET['oid'];
		$info=$orders->where(array("oid"=>$oid))->find();
		$ginfo=$goods->where(array("gid"=>$info['goods_id']))->find();
		
		$area = "";
		if($ginfo['is_sy']){
			$addres = $this->regeo($ginfo['longitude'],$ginfo['dimension']);
			$area = $addres['regeocode']['formatted_address'];
			if(is_array($area)){
				$area = "无法获取地理位置";
			}
		}
		$this->assign("area",$area);
		
		$mem = $member->where(array('username'=>$ginfo['username']))->field('mobile')->find();
		
		$info['songnl']=intval($ginfo['zsscl']*$info['goods_num']);//0.15
		$sxw_goodsPic = explode(',', $ginfo['gpic']);
		$info['pic']=$sxw_goodsPic[0];
		$info['gpic']=$ginfo['gpic'];
		$info['postage']=$ginfo['postage'];
		$info['goods_spec']=$ginfo['goods_spec'];
		//$info['songnl']=intval($gds['order_price']/3);
		$adr=$address->where(array("user_id"=>$user_id,"if_default"=>1))->find();
		$this->assign("ginfo",$ginfo);
		$this->assign("info",$info);
		$this->assign("adr",$adr);
		$this->assign("mem",$mem);
		$this->display();
	}
	/**
	 * 庄园商品
	 */
	public function zygoodsinfo(){
		$uid = $_SESSION['mid'];
		$member = M("Member");
		$items = M('jzzgoods');
		$itemsdata = $items->find($_GET['gid']);
		
		if($itemsdata['if_tg'] > 0){
			$tjrs = $member->where(array("parent_id"=>$uid))->count();
			if($tjrs < $itemsdata['if_tg']){
				$this->error("购买此产品需要推广人数达到".$itemsdata['if_tg']."人才可以购买开心兑产品",U("Index/shop/jzzgoods"));
			}
		}
		//$spjiage = sprintf("%.2f", $itemsdata['goldprice'] / $rmb_hl / $gao);
		// 商品的缩略图
		$sxw_goodsPic = explode(',', $itemsdata['gpic']);
		
		// 商品的基本信息
		// 将商品数据放往前台
		$this->assign('item',$itemsdata);
		// 将图片路径发往前台
		$this->assign('sxw_goodsPic',$sxw_goodsPic);
		$this->display();
	}
	/**
	 * 兑换商品购买
	 */
	public function dhgoods(){
		$user_id=$_SESSION['mid'];
		$jzzgoods=M('jzzgoods');
		$address=M("address");
		
		$goods_id=$_GET['goods_id'];//庄园产品id
		$num=$_GET['qty_item_1'];//购买数量
		$goods=$jzzgoods->where(array("gid"=>$goods_id))->find();
		
		$sxw_goodsPic = explode(',', $goods['gpic']);
		$goods['pic']=$sxw_goodsPic[0];
		
		$adr=$address->where(array("user_id"=>$user_id,"if_default"=>1))->find();
		
		$this->assign("gds",$goods);
		$this->assign("adr",$adr);
		$this->display();
		
	}
	/**
	 * 兑换商品提交订单
	 */
	public function zyorder(){
		$jhorder=M("jhorder");
		$member=M("member");
		//print_r($_POST);
		if($_GET['oid']){
			$info=$jhorder->where(array("id"=>$_GET['oid']))->find();
			$user=$member->where(array("id"=>$_SESSION['mid']))->find();

            import('ORG.Util.BlockChain');
            $bc = new BlockChain();
            $user['wallet'] = $bc->findWallet($user['wallet_code'], $user['wallet_pows']);

			$this->assign("user",$user);
			$this->assign('info',$info);
		}
		if(IS_POST){
			$data['jzzg_id'] = $_POST['jzzg_id'];
			$data['jzzg_num'] = $_POST['jzzg_num'];
			$data['jzzg_name'] = $_POST['jzzg_name'];
			$data['paymethod'] = 0;
			$data['jzzg_price'] = $_POST['jzzg_price'];
			$data['order_price'] = $_POST['order_price'];
			$data['jho_time'] = date("Y-m-d H:i:s");
			$data['name'] = $_POST['name'];
			$data['photo'] = $_POST['photo'];
			$data['jho_number'] = date("YmdHis",time()).rand(100,999);
			$data['deliveryaddress'] = $_POST['deliveryaddress'];
			$data['user_id'] = $_SESSION['mid'];
			$data['username'] = $_SESSION['username'];
			$data['add_time'] = time();
			$res=$jhorder->add($data);
			if($res){
				$info=$jhorder->where(array("id"=>$res))->find();
				$member->where(array("id"=>$_SESSION['mid']))->setDec('jinbi',$info['jzzg_price']);
				$user=$member->where(array("id"=>$_SESSION['mid']))->find();

                import('ORG.Util.BlockChain');
                $bc = new BlockChain();
                $user['wallet'] = $bc->findWallet($user['wallet_code'], $user['wallet_pows']);

				$this->assign("user",$user);
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
	 * 兑换订单列表
	 */
	public function zyorder_list(){
		$member=M("member");
		$jzzgoods=M("jzzgoods");
		$jhorder=M("jhorder");
		$user_id=$_SESSION['mid'];
		$where['user_id']=$user_id;
		if($_GET['state'] != ''){
			$where['status']=$_GET['state'];
		}
		$olist=$jhorder->where($where)->order("id desc")->select();
		foreach($olist as $key=>$val){
			$ginfo=$jzzgoods->where(array("gid"=>$val['jzzg_id']))->find();
			$olist[$key]['gpic']=explode(',', $ginfo['gpic'])[0];
			$olist[$key]['goods_spec']=$ginfo['goods_spec'];
		}
		$this->assign('state',$_GET['state']);
		$this->assign('list',$olist);
		$this->display();
	}
	/**
	 * 兑换订单详情
	 */
	public function zyorder_info(){
		$member=M("member");
		$jzzgoods=M("jzzgoods");
		$jhorder=M("jhorder");
		$address=M("address");
		$user_id=$_SESSION['mid'];
		$oid=$_GET['id'];
		$info=$jhorder->where(array("id"=>$oid))->find();
		$ginfo=$jzzgoods->where(array("gid"=>$info['jzzg_id']))->find();
		
		//$info['songnl']=intval($info['order_price']/3);
		$sxw_goodsPic = explode(',', $ginfo['gpic']);
		$info['pic']=$sxw_goodsPic[0];
		$info['gpic']=$ginfo['gpic'];
		$info['goods_spec']=$ginfo['goods_spec'];
		//$adr=$address->where(array("user_id"=>$user_id,"if_default"=>1))->find();
		
		$this->assign("info",$info);
		//$this->assign("adr",$adr);
		$this->display();
	}
	//购物商城分类页
    public function search(){
		//S('gouwuche_' . $_SESSION['mid'],null);
		$cid = (int)I('get.cid');
		
		$classifydata = M('classify');
		$classdata = $classifydata->field('cid,cname')->where('parentid=0')->order('cid desc')->select();
		$children = $classifydata->field('cid')->where('parentid = '.$cid)->select();
		$chil_arr = [];
		foreach($children as $key => $val){
			array_push($chil_arr, $val['cid'] );
		}
		array_push($chil_arr, $cid );

		$gao = C('max_danjia');
		$rmb_hl = C('rmb_hl');
		
		$goods_model = M('goods');
		
		if(isset($cid) && !empty($cid)){
			$where = ['gclassification' => ['in',$chil_arr]];
		}else{
			$where = "1 = 1";
		}

		$goods_list = $goods_model->field('gid,gclassification cid,gname,gpic,goldprice,gsellnums,gattribute attr')->where($where)->order('gid DESC')->select();
		foreach ($goods_list as $key => $value) {
            $pics = explode(',', $value['gpic']);
            $goods_list[$key]['gpic'] = $pics[0];
        }
		foreach($goods_list as $key => &$r){
			$r['spjiage']=sprintf("%.2f", $r['goldprice'] / $rmb_hl / $gao);
		}
        $this->goods_list = $goods_list;
		$this->assign('classdata',$classdata);
		$this->cid = $cid;
        $this -> display();
    }
    
	//产品详情页
	public function index(){
		$items = M('goods');
		$gao = C('max_danjia');
		$rmb_hl = C('rmb_hl');
		$itemsdata = $items->find($_GET['gid']);
		$spjiage = sprintf("%.2f", $itemsdata['goldprice'] / $rmb_hl / $gao);
		// 商品的缩略图
		$sxw_goodsPic = explode(',', $itemsdata['gpic']);
		// 实例化评价表
		$list2 = M('goodsreview');
		// 计算一共有多少个
		$total = $list2->field('integral')->where($_GET)->select();
		$count = $list2->where($_GET)->count();
		$sum = 0;
		$sum1 = 0;
		// 计算评价的等级
		foreach ($total as $value) {
			$sum +=5;
			$sum1 += $value['integral'];
		}
		// 将等级发送前台
		$nums  = ($sum1/$sum)*100;
		// 遍历用户评论
		$reviewlist = M();
		$reviewdata = $reviewlist->table('sx_member m,sx_goodsreview r')->field('m.uname,r.integral,r.content')->where('r.uid=m.uid and r.gid='.$_GET['gid'])->order('r.rid desc')->limit(20)->select();
		// 遍历同品牌的商品
		$branddata = $items->where('bid='.$itemsdata['bid'].' and gid!='.$_GET['gid'])->field('gid,gpic,gname,goldprice')->order('gsellnums desc')->limit(5)->select();
		// 循环修改图片
		foreach ($branddata as $key => $value) {
			$branddata[$key]['gpic'] = explode(',', $value['gpic'])[0];
		}
		
		//检查该产品是否已经存在购物车中
		$gouwuche = S('gouwuche_' . $_SESSION['mid']);
		$flag = false;
		
		if(!empty($gouwuche)){
			foreach($gouwuche as $key => $val){
				if($_GET['gid'] == $val['gid']) $flag = true;
			}
		}
		
		$this->assign('flag', $flag);
		$this->assign('branddata',$branddata);
		$this->assign('spjiage',$spjiage);
		$this->assign('reviewdata',$reviewdata);
		$this->assign('nums',$nums);
		$this->assign('count',$count);
		$this->assign('line',$line);
		// 商品的基本信息
		// 将商品数据放往前台
		$this->assign('item',$itemsdata);
		$this->assign('title',$itemsdata['gname']);
		// 将图片路径发往前台
		$this->assign('sxw_goodsPic',$sxw_goodsPic);
		$this->display('item');
	}
	
	//超市
	public function shopmarket(){
		$goods_model = M('goods');
		$goods_list = $goods_model->field('gid,gclassification cid,gname,gpic,goldprice,gsellnums,gattribute attr')->order('gid DESC')->select();
		foreach ($goods_list as $key => $value) {
            $pics = explode(',', $value['gpic']);
            $goods_list[$key]['gpic'] = $pics[0];
        }
        $this->goods_list = $goods_list;
		$this->display();
	}       

	//加入购物车
	public function addgouwuche(){
		$gid = (int)I('post.gid');
		$goods_model = M('goods');
		$mid = $_SESSION['mid'];
		
		if(isset($gid) && !empty($gid)){
			$goods_info = $goods_model->where(['gid'=>$gid])->find();
			
			if(!empty($goods_info)){
				$data = [
					'gid' => $gid, // 商品ID
					'mid' => $mid, // 用户ID
					'nums'=> 1, // 数量
					'price' => $goods_info ['goldprice'] //单价
				];
				
				//购物车是否已经有产品
				$gouwuche = S('gouwuche_' . $mid);
				
				if(!empty($gouwuche)){
					array_push($gouwuche, $data);
					S('gouwuche_' . $mid, $gouwuche);
				}else{
					$gouwuche['0'] = $data;
					S('gouwuche_' . $mid, $gouwuche);
				}
				
				$ajax_data = [
					'status'  => 1,
					'message' => '加入购物车成功！'
				];
			}else{
				$ajax_data = [
					'status'  => -1,
					'message' => '商品不存在！'
				];				
			}
		}else{
			$ajax_data = [
				'status'  => -1,
				'message' => '参数错误！'
			];
		}
		
		$this->ajaxReturn($ajax_data);
	}
	
	//购物车
    public function gouwuche(){
		$gid = (int)I('get.gid');
		
		$gao = C('max_danjia');
		$rmb_hl = C('rmb_hl');
		
		if(isset($gid) && !empty($gid)){
			
			$goods_info = M('goods')->where(['gid'=>$gid])->select();
			foreach($goods_info as $key => &$r){
				$r['spjiage']=sprintf("%.2f", $r['goldprice'] / $rmb_hl / $gao);
			}
			$jiage = M('goods')->where(['gid'=>$gid]) ->field('goldprice,gname,gid') ->find();
				

			$spjiage=sprintf("%.2f", $jiage['goldprice'] / $rmb_hl / $gao);
			$gid1 = $jiage['gid'];
			$jiages = $jiage['goldprice'];
			$gname = $jiage['gname'];
			if(!empty($goods_info)){
				foreach ($goods_info as $key => $value) {
		            $pics = explode(',', $value['gpic']);
		            $goods_info[$key]['gpic'] = $pics[0];
		            $goods_info[$key]['nums'] = 1;
		        }				
				$this->cartdata = $goods_info;

				$this->assign('spjiage',$spjiage);
				$this->assign('gid1',$gid1);
				$this->assign('jiages',$jiages);
				$this->assign('gname',$gname);
				$this->display();
			}else{
				die('商品不存在！');
			}
			
		}else{
			die('缺少参数！');
		}
    }
	/**
	*却结算按钮判断
	*/
	public function checkButton(){
		if (session('mid')) {
			echo 1;
			return;
		}
		echo 0;	
	}    
	//我的订单
    public function dingdan(){
		$orders = M('orders');
		$uid = session('mid');
		$orders_info=$orders->where(array('uid'=>$uid))->order('otime desc')->select();
		$this -> assign('orders_info',$orders_info);
        $this -> display();
    }
	//待发货
    public function daifa(){
		$orders = M('orders');
		$uid = session('mid');
		$orders_info=$orders->where(array('uid'=>$uid,'status'=>0))->order('otime desc')->select();
		$this -> assign('orders_info',$orders_info);
        $this -> display();
    }
	//待收货
    public function daishou(){
		$orders = M('orders');
		$uid = session('mid');
		$orders_info=$orders->where(array('uid'=>$uid,'status'=>1))->order('otime desc')->select();
		$this -> assign('orders_info',$orders_info);
        $this -> display();
    }
	//已完成订单
    public function wancheng(){
		$orders = M('orders');
		$uid = session('mid');
		$orders_info=$orders->where(array('uid'=>$uid,'status'=>2))->order('otime desc')->select();
		$this -> assign('orders_info',$orders_info);
        $this -> display();
    }
	
	//取消订单
	public function deldingdan(){
		$onumber = I('onumber');
		$orders = M("orders");
		$username = session('username');
		$orders_info = $orders->where(array('onumber'=>$onumber))->find();
		
		$inc = M('member') -> where(array('id'=>$orders_info['uid']))->setInc('jinbi',$orders_info['total']);
		account_log($username,$orders_info['total'],'商品买家取消订单返回',1);	
		
		$dec = M('member') -> where(array('id'=>$orders_info['uid']))->setDec('qjinbi',$orders_info['total']);
		account_log4($username,$orders_info['total'],'商品买家取消订单扣除',0);	
			
		$map['onumber'] = array('eq',$onumber);

		if($orders -> where($map) -> delete()){

			alert('取消订单成功',U('Index/Shop/dingdan'));
		}else{
			alert('取消订单失败',U('Index/Shop/dingdan'));
		}
	}
	//确认收货
	public function shouhuo(){
		$onumber = I('onumber');
		$orders = M("orders");
		$username = session('username');
		$orders_info = $orders->where(array('onumber'=>$onumber))->find();
		
		$obs = M('member')->where(array('id'=>$orders_info['uid']))->setDec('qjinbi',$orders_info['total']);
		account_log4($username,$orders_info['total'],'商品交易订单完成扣除',0);
		
		$shangjia = M('member')->where(array('username'=>$orders_info['username']))->find();
		
		$sxf = M("shop_group")->where(array("level"=>$shangjia['shoplevel']))->getField("shouxu");
		$lkb = $orders_info['total'] - $orders_info['total'] *$sxf/100;
		
		$oob = M('member')->where(array('username'=>$orders_info['username']))->setInc('jinbi',$lkb);	
		account_log($orders_info['username'],$lkb,'商品交易订单完成获得',1);	
		
		$map['onumber'] = array('eq',$onumber);
		$shou['status'] =2;
		if($orders -> where($map) -> save($shou)){

			alert('确认收货成功',U('Index/Shop/dingdan'));
		}else{
			alert('确认收货失败',U('Index/Shop/dingdan'));
		}
	}
	//商城加盟
    public function jiameng(){
    	$adv = M("adv");
		$member = M('member')->where(array('username'=>session('username')))->field('username,jinbi,truename,mobile,shopstatus,shopname,shoplevel')->find();
		$shop_group = M('shop_group')->select();
		//$jinbi = $member['jinbi'];
        $this -> assign('member',$member);

		$adv_list = $adv->where(array('pos_id'=>5))->order("id desc")->select();
		$this->assign('adv_list',$adv_list);
        $this -> assign('shop_group',$shop_group);
        $this -> display();
    }

	//提交商城加盟
    public function tijiaojiameng(){
		if(IS_POST){
			$data['shopname']     = I('post.shopname','','htmlspecialchars');
			$data['shoplevel']     = I('post.shoplevel','','htmlspecialchars');
			$password2   = I('post.password2','','md5');
			if(empty($data['shopname'])){
				alert('请输入商户名称',U('Index/Shop/jiameng'));
            }	
			if(empty($data['shoplevel'])){
				alert('请输入加盟等级',U('Index/Shop/jiameng'));
            }	
			$members = M('member');
			/*if (!$members->where(array('username'=>session('username'),'password2'=>$password2))->getField('id')) {
				alert('对不起!二级密码不正确!',U('Index/Shop/jiameng'));				
			}	*/	
			$shop_group = M('shop_group')->where(array('level'=>$data['shoplevel']))->getField('price');
			
			$username = session('username');
			$member = M('member')->where(array('username'=>$username))->field('id,level,jinbi')->find();
			
			if($member['level']==0){
				alert('请先完善个人资料提交系统审核！',U('personal_set/myInfo'));	
			} 
			/*if($member['jinbi'] < $shop_group){
				alert('您的余额不足',U('Index/Index/index'));
			
			}else{*/
				$data['shopstatus'] = 3;
				$data['uname'] = session('username');
				$data['uid'] = $member['id'];
				$xiu=M('member')->where(array('username'=>$username))->save($data);
				if(empty($xiu)){
					alert('入驻失败，请重新提交',U('Index/Shop/jiameng'));
				}else{
				//	M('member')->where(array('username'=>$username))->setDec('jinbi',$shop_group);
					shangcheng_log($username,$shop_group,'开通商城',0);		
				}
			//}
			alert('加盟成功，农链有你更精彩',U('Index/index/index'));
		}
    }
	
	//加盟商等级升级
    public function shengji(){
		$member = M('member')->where(array('username'=>session('username')))->field('username,jinbi,truename,mobile,shopstatus,shopname,shoplevel')->find();
		$dengji = $member['shoplevel'];
		$shop_group = M('shop_group')->where("level > {$dengji}")->select();

        $this -> assign('member',$member);

        $this -> assign('shop_group',$shop_group);
		$this->display();
    }
	//提交升级请求
    public function tijiaoshengji(){
		
		if(IS_POST){
			$data['shoplevel']     = I('post.shoplevel','','htmlspecialchars');
			$password2   = I('post.password2','','md5');
			
			if(empty($data['shoplevel'])){
				alert('请输入要升级的等级',U('Index/Shop/shengji'));
            }	
			$members = M('member');
			if (!$members->where(array('username'=>session('username'),'password2'=>$password2))->getField('id')) {
				alert('对不起!二级密码不正确!',U('Index/Shop/shengji'));				
			}		
			$shop_group = M('shop_group')->where(array('level'=>$data['shoplevel']))->getField('price');
			
			$username = session('username');
			$member = M('member')->where(array('username'=>$username))->field('id,level,jinbi,shoplevel')->find();
			$shop_group1 = M('shop_group')->where(array('level'=>$member['shoplevel']))->getField('price');//已加盟的价格
			
			$shop_group2 = $shop_group - $shop_group1;
			
			if($member['level']==0){
				alert('请先完善个人资料提交系统审核！',U('personal_set/myInfo'));	
			} 
			if($member['jinbi'] < $shop_group2){
				alert('您的余额不足，请前往商城购买MHC币',U('Index/Emoney/index'));
			
			}else{
				$data['shopstatus'] = 1;
				$data['uname'] = session('username');
				$data['uid'] = $member['id'];
				$xiu=M('member')->where(array('username'=>$username))->save($data);
				if(empty($xiu)){
					alert('升级失败，请重新提交',U('Index/Shop/shengji'));
				}else{
					M('member')->where(array('username'=>$username))->setDec('jinbi',$shop_group2);
					shangcheng_log2($username,$shop_group2,'升级加盟等级',0);		
				}
			}
			alert('升级成功，养生链有你更精彩',U('Index/Shop/jiameng'));
		}
    }
	//平台签到奖励
	public function qiandao(){

			 
			$s_time=strtotime(date("Y-m-d 00:00:01"));
			$o_time=strtotime(date("Y-m-d 23:59:59"));
			$user_id = session('mid');
			$username = session('username');
			$jiangli = C('qdjiangli');
			$qdzs = C('qdzs');
			$info = '签到奖励';
			
			$todayData = M('members_sign')->where("stime > {$s_time} and stime < {$o_time}")->count();    
			$grtodayData = M('members_sign')->where("stime > {$s_time} and stime < {$o_time} and user_id  = {$user_id} ")->count();    //个人签到与否
			

			if($todayData < $qdzs){   
			
				if($grtodayData == 1){      
					alert('您今日已经签过到了,快去推广吧!',U('Index/Emoney/shouye'));					
				}else{      
				     
					$map['user_id'] = session('mid');
					$map['username'] = session('username');
					$map['jiangli'] = C('qdjiangli');
					$map['stime'] = time();     
					$map['desc'] = $info;     
					$id = M('members_sign')->add($map);    
				
					if($id){    
						M('member') -> where(array('id'=>session('mid')))->setInc('jinbi',$jiangli);
						
						qiandao_log($user_id,$username,$jiangli,$info);
 						alert('签到成功,获得'. $jiangli .'个币的签到奖励,快去推广吧!',U('Index/Emoney/shouye'));	
						}else{      
						alert('签到失败,请刷新重试!',U('Index/Emoney/shouye'));	
						} 		
				}
			}else{
					alert('每天最多签到'. $qdzs .'人次!',U('Index/Emoney/shouye'));	
				}    
 
			

	}
	//商品详情
	
	public function pcontent(){
		
		$id =  I('get.id',0,'intval');
		$type = M('type');
		$product = M("product");
		
	   
		$data = $product -> find($id);
        if(empty($data)){
			
			alert('信息不存在',U('Index/Shop/plist'));
		}
		$this -> assign('product',$data);			
		
		$this->display();
	}
   //订单提交页面
   public function tijiaodingdan(){
		if(IS_POST){
			$data['jiage']     = I('post.jiage','','htmlspecialchars');
			$data['gid']     = I('post.gid','','htmlspecialchars');
			$data['gname']     = I('post.gname','','htmlspecialchars');
			$data['name']     = I('post.name','','htmlspecialchars');
			$data['photo']     = I('post.photo','','htmlspecialchars');
			$data['remarks']     = I('post.remarks','','htmlspecialchars');
			$data['address']     = I('post.address','','htmlspecialchars');
			$password2    = I('post.password2','','md5');

		
			if(empty($data['name'])){
				echo '<script>alert("请输入收货人姓名！");window.history.back(-1);</script>';
				die;
            }	
			if(empty($data['photo'])){
				echo '<script>alert("请输入收货人电话！");window.history.back(-1);</script>';
				die;
            }	
			if(empty($data['address'])){
				echo '<script>alert("请输入收货人地址！");window.history.back(-1);</script>';
				die;
            }	
			if(empty($password2)){
				echo '<script>alert("请输入交易密码！");window.history.back(-1);</script>';
				die;
            }	
			$members = M('member');
			if (!$members->where(array('username'=>session('username'),'password2'=>$password2))->getField('id')) {	
				echo '<script>alert("对不起!二级密码不正确!");window.history.back(-1);</script>';
				die;				
			}	
			
	      $userinfo = M("member")->where(array("username"=>session("username")))->find();
			if($userinfo['level']==0){
				alert('请先完善个人资料提交系统审核！',U('personal_set/myInfo'));
			}
		  
			if($userinfo['checkstatus']==2){
				alert('账户信息审核失败,请先完善个人资料提交系统审核！',U('personal_set/myInfo'));
			}
		  
		  
			if($userinfo['checkstatus']!=3){
				alert('资料信息正在审核！',U('personal_set/myInfo'));
			}
		  
			 $jinbi = getMemberField('jinbi');			 
			 if($jinbi < $data['jiage']){
				echo '<script>alert("账户余额不足！");window.history.back(-1);</script>';
				die;
			 }	

			$username = M('goods')->where(array('gid'=>$data['gid']))->getField('username');

			// 读取当前登陆用户
			$uid = $_SESSION['mid'];
			// 重新构造post用于保存至订单表
			$_POST['onumber'] = date('YmdHis',time()).rand(10,99);//订单号
			$_POST['username'] = $username;//收货人id
			$_POST['uid'] = $uid;//收货人id
			$_POST['name'] = $data['name'];//收货人姓名
			$_POST['photo'] = $data['photo'];//收货人手机号
			$_POST['total'] = $data['jiage'] ;//价格
			$_POST['shangname'] = $data['gname'];//商品名称
			$_POST['remarks'] = $data['remarks'] ;//备注信息
			$_POST['otime'] = date('Y-m-d',time());//订单时间
			$_POST['paymethod'] = 1;//支付状态
			$_POST['deliveryaddress'] = $data['address'];//订单收货人地址
		
			// 实例化订单表
			$orderlist = M('orders');
			// 插入数据，返回插入数据的订单号
			$oid = $orderlist->add($_POST);
			if ($oid) {
				M("member")->where(array('username'=>session('username')))->setDec('jinbi',$data['jiage']);
				account_log(session('username'),$data['jiage'],'购买商品'.$data['gname'],0);
				M("member")->where(array('username'=>session('username')))->setInc('qjinbi',$data['jiage']);
				account_log4(session('username'),$data['jiage'],'冻结购买商品'.$data['gname'],1);		
				alert('购买成功，农链有你更精彩',U('Index/Shop/dingdan'));
			}
		}
   }
   //订单提交页面
   public function buy(){
	   
	      $userinfo = M("member")->where(array("username"=>session("username")))->find();
		  /*if($userinfo['level']==0){
			  alert('请先完善个人资料提交系统审核！',U('personal_set/myInfo'));
		  }
		  
		   if($userinfo['checkstatus']==2){
			  alert('账户信息审核失败,请先完善个人资料提交系统审核！',U('personal_set/myInfo'));
		  }
		  
		  
		   if($userinfo['checkstatus']!=3){
			  alert('资料信息正在审核！',U('personal_set/myInfo'));
		  }*/
		  
		  
	      $product = M("product");

		  $id =  I('get.id',0,'intval');
		  //查询农田信息
		  $data = $product -> find($id);
		  if(empty($data)){
			  alert('信息不存在',U('Shop/plist'));
		  }		
		  /*$suanli = $userinfo['mygonglv'] + $data['gonglv'];
		  $mysuanli = M("member_group")->where(array("level"=>$userinfo['level']))->getField("mysuanli");
		  
		  if($suanli>$mysuanli){
			  alert('超过您的最大可拥有算力'.$mysuanli.',不能购买',U('Shop/plist'));
			  
		  }*/
		  
		  //判断 是否已经达到限购数量
		  
		  $my_gounum=M("order")->where(array("user"=>session('username'),"sid"=>$id,"end_time"=>array("gt",time())))->count();
		  if($my_gounum >=$data['xiangou']){
			    echo '<script>alert("已经达到你购买本农田上线！");window.history.back(-1);</script>';
				die;
				  
		  }  //统计是否有符合数量的免费合约机
	   	 $zs_count = M("order")->where(array("user"=>session('username'),"sid"=>1))->count();
	   	 $zs_counts = M("order")->where(array("sid"=>1))->count();
		 /*if($zs_counts >= C("z_num")){
				echo '<script>alert("此类型免费农田已赠送完毕！");window.history.back(-1);</script>';
				die;					

			}*/
			
		 if($zs_count >= C('zs_num') && $id==1){
				echo '<script>alert("你已经拥有足够数量的免费农田！");window.history.back(-1);</script>';
				die;
		 }else{
             $from = M("member")->where(array('username'=>session('username')))->find();

             import('ORG.Util.BlockChain');
             $bc = new BlockChain();
             try{
                 $bc->transaction($from['wallet_code'], C('chain_address'), $data['price'], $from['wallet_pows']);
             }catch (\Exception $e){
                 echo '<script>alert('.$e->getMessage().');window.history.back(-1);</script>';
                 die;
             }

/*              if($id==1){
				if($zs_count >= C("z_num")){
					echo '<script>alert("此类型合约机已达上限！");window.history.back(-1);</script>';
					die;					

				}
			 } */	
			 
            account_log(session('username'),$data['price'],'购买'.$data['title'],0);
		 }
		  
	  
		 $map = array();
         // $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');		  
         $ytime=86400;
          $map['kjbh'] = 'S' . date('d') . substr(time(), -5) . sprintf('%02d', rand(0, 99));
		  $map['user'] = session('username');
		  $map['user_id'] = session('mid');
		  $map['project']= $data['title'];
		  $map['sid'] = $data['id'];
		  $map['yxzq'] = $data['yszq'];		
          $map['sumprice'] = $data['price'];
		  $map['addtime'] = date('Y-m-d H:i:s');	
          $map['imagepath'] = $data['thumb'];
		  $map['lixi']	= $data['gonglv'];
		  $map['kjsl'] =  $data['shouyi'];
          $map['zt'] =  1;	
          $map['UG_getTime'] =  time();		
          $map['end_time'] =  time()+($ytime*$data['yszq']);		  
		  M('order')->add($map);		
		  $product->where(array("id"=>$id))->setDec("stock");
		  //写入上级团队算力
				$parentpath = M("member")->where(array("username"=>session('username')))->getField("parentpath");
				$path2 = explode('|', $parentpath);
		        array_pop($path2);
			    $parentpath = array_reverse($path2);
	            foreach($parentpath as $k=>$v){
					 M("member")->where(array('id'=>$v))->setInc("teamgonglv",$map['lixi']);
                }	
		   //写入个人算力
		  M("member")->where(array("username"=>session('username')))->setInc("mygonglv",$map['lixi']);
          //updateLevel();				
	      alert('农田购买成功',U('Index/Emoney/ntlist'));
   }
 
   	//订单列表
	public function orderlist(){
		import('ORG.Util.Page');
		$count = M('order') ->where(array('user'=>session('username')))->count();
		$Page  = new Page($count,10);
		$Page->setConfig('theme', '%first% %upPage% %linkPage% %downPage% %end%');
		$show = $Page -> show();		
	 
        $list = M('order')->where(array('user'=>session('username')))->order('id desc') -> limit($Page ->firstRow.','.$Page -> listRows)->select();
		$this ->assign("page",$show);		 
        $this->assign('list',$list);		
		$this->display();
	}  
	
	public function wakuang(){
		
		$id= I("get.id",0,"intval");
		$result = M('order')->where(array('id'=>$id,"user"=>session('username')))->find();
		if(!$result){
					echo '<script>alert("农田不存在！");window.history.back(-1);</script>';
					die;					
		}
		

			
		//计算预计总收益

		$time = $result['UG_getTime'];
	    $time1= NOW_TIME;
		$cha = $time1-$time;
		
		//$jrsy= $result['kjsl']/3600;
		$jrsy= 0;
		$jrsy=number_format($jrsy,8);//每秒收益
		
		$yjzsy = $cha * $jrsy;//矿车预计总收益
		$zsy=number_format($yjzsy,8);
		$kcmc = $result['project'];
		$status=$result['zt'];

		$qwsl=$result['qwsl'];
		$qwsljs=M('shop_project')->sum('kjsl');
		//每秒受益
		//$mmsy=$result['kjsl']/86400;
		$mmsy=$result['kjsl']/3600;
		$mmsy=number_format($mmsy,8);
		$this->assign('mmsy',$mmsy);
		//dump($qwsljs);die();
		$ckzqwsl=M('order')->where(array('zt'=>1))->sum('lixi');
		$ckzqwsl=number_format($ckzqwsl,2);
		
		
		$sl=M('slkz')->order('id desc')->find();
		$xssl=$ckzqwsl+$sl['num'];
		$xssl=number_format($xssl,2);
		//dump($xssl);die();
		$down_time=time()-strtotime($result['addtime']);
		
		if($down_time > $result['yxzq']*3600){
			$down_time=$result['yxzq']*3600;
		}
		
		
		$data_b_total=M("jinbidetail")->where("type = 1")->sum('adds');
		
		
		$total_sy=$down_time*$mmsy;
		$this->assign('total_sy',$total_sy);
		$this->assign('data_b_total',$data_b_total);
		$this->assign('kcmc',$kcmc);
		$this->assign('status',$status);
		$this->assign('yjzsy',$zsy);
		$this->assign('gonglv',$result['lixi']);
		$this->assign('qwsl',$ckzqwsl);
		$this->assign('jrsy',$jrsy);
		$this->display();
	}
	
	//支付
	public function ordePay(){
		die;
		$member = M('member');
		$id = I('get.id',0,'intval');
		$jinbi = getMemberField('jinbi');
		if($id==0){
			alert('订单参数出错！',-1);
		}
		$orderinfo = M('order')->where(array('member'=>session('username'),'id'=>$id))->find();
		if($orderinfo['status']>0){
			echo '<script>alert("订单已支付，不可操作！");window.history.back(-1);</script>';
			die;
		}
		$money = $orderinfo['money'];
        if (!$orderinfo) {
			echo '<script>alert("对不起，支付信息不正确！");window.history.back(-1);</script>';
			die;				
        }	

		//扣除并写入明细
		if($jinbi < $money){
				echo '<script>alert("电子币余额不足，请确认！");window.history.back(-1);</script>';
				die;			
		}			

		$member->where(array('username'=>session('username')))->setDec('jinbi',$money);
		account_log(session('username'),$money,'支付商品:('.$orderinfo['stitle'].'),数量 '.$orderinfo['num'].'件。',0);				
	    $parent = $member->where(array('username'=>session('username')))->getField('parent');
		   if(!empty($parent)){
			      //重消奖 
				  $tjj  = cxmoney($money * 0.01 * C("LINGSHOU"));  
				  $member->where(array('username'=>$parent))->setInc('jinbi',$tjj[0]);
				  account_log($parent,$tjj[0],'重消奖,来自会员:'.session('username'),1,5);	
				  $member->where(array('username'=>$parent))->setInc('point',$tjj[1]);
				  account_log3($parent,$tjj[1],'重消奖,来自会员:'.session('username'),1,5);					  
		    }
		//更新订单状态
		M('order')->where(array('member'=>session('username'),'id'=>$id))->save(array('status'=>1,'pay_time'=>NOW_TIME));


        alert('支付成功',U('Index/Shop/orderlist'));		
	}
	
	
		public function payList(){
			$data = M('paydetail')->where(array('member'=>session('username')))->order('id desc')->select();
			$this->assign('data',$data);
			$this->display();
		}
		/**
		 * [会员电子货币充值]
		 * @return [type] [description]
		 */
		public function pay(){
			
			if (IS_POST) {
				$db = M('paydetail');
				$member = M('member');
				$money = I('post.money',0,'intval');
				$password2   = I('post.password2','','md5');
				$data['type'] = I('post.type',0,'strval');
				$data['account'] = I('post.account',0,'strval');
				$data['name'] = I('post.name',0,'strval');
				$data['content'] = I('post.content',0,'strval');
				if(empty($data['type']) || empty($data['account']) || empty($data['name']) || empty($data['content'])){
					
					$this->ajaxReturn(array('info'=>'请完善零售信息！'));
				}
				$money == 0 and  $this->ajaxReturn(array('info'=>'提现金额不能为0！'));
				//验证二级密码是否正确
				if (!$member->where(array('username'=>session('username'),'password2'=>$password2))->getField('id')) {
					$this->ajaxReturn(array('info'=>'对不起!二级密码不正确!'));					
				}		
				$money = intval($money);
				$shoukuan = $member->where(array('member'=>session('username')))->find();
				

				$data['addtime'] = time();
				$data['member'] = session('username');
	            $data['amount'] =  $money;
					if ($db->data($data)->add()) {

						$this->ajaxReturn(array('info'=>'提交成功，等待审核！','url'=>U('Index/shop/pay')));
					}else{
						$this->ajaxReturn(array('info'=>'提交失败！','url'=>U('Index/shop/pay')));
					}				

            }      
			$member = M('member')->field("jinbi")->where(array('id'=>session('mid')))->find();
			
			$status = C("WITHDRAW_STATUS");
			$this->assign('status',$status);
			$this->assign('v',$member);
			$this->display();
		}
/**
		 * 幸运兑
		 */
		public function xyd(){
			$xyd=M("xyd"); 
			$adv = M('adv');
			import('ORG.Util.Page');
			$zt = $_GET['zt'];
			$where = "1 = 1";
			if($zt == 1){
				$where .= " and xyd_addtime < ".time()." and xyd_endtime > ".time();
			}
			elseif($zt == 2){
				$where .= " and xyd_endtime < ".time();
			}
			else{
				$where .= " and xyd_addtime < ".time()." and xyd_endtime > ".time();
			}
			$count = $xyd->where($where)->count();
			$Page  = new Page($count,10);
			$show = $Page -> show();
			$xyd_list=$xyd ->where($where)->limit($Page ->firstRow.','.$Page -> listRows)->select();
			foreach($xyd_list as $key=>$val){
				$gpic=explode(",",$val['xyd_pic']);
				$xyd_list[$key]["xyd_pic"]=$gpic[0];
			}
			
			$adv_list = $adv->where(array('pos_id'=>7))->order("id desc")->select();
			$this->assign('adv_list',$adv_list);
			$this->assign('goods_list',$xyd_list);
			$this->assign("page",$show);
			$this->display();
		}
		/**
		 * 幸运兑详情
		 */
		public function xydinfo(){
			$id = $_GET['id'];
			$user_id = $_SESSION['mid'];
			if($id){
				$xyd = M("xyd");
				$xydod = M("xydod");
				$info = $xyd->where(array("id"=>$id))->find();
				$gpic=explode(",",$info['xyd_pic']);
				$odinfo = $xydod->where(array("xyd_id"=>$id,"user_id"=>$user_id))->find();
				if($odinfo){
					$od = 1;
				}
				else{
					$od = 2;
				}
				$this->assign("od",$od);
				$this->assign("gpic",$gpic);
				$this->assign("info",$info);
				$this->display();
			}
			else{
				$this->error("参数错误！");
			}
		}
		/**
		 * 幸运兑参加
		 */
		public function xydod(){
			$id = $_GET['id'];
			$member = M("member");
			$xyd = M("xyd");
			$xydod = M("xydod");
			$user_id = $_SESSION['mid'];
			if(IS_POST){
				$xyd_id = $_POST['xyd_id'];
				$info = $xyd->where(array("id"=>$xyd_id))->find();
				$user = $xyd->where(array("id"=>$id))->find();
				$data['xyd_id'] = $xyd_id;
				$data["state"] = 1;
				$data['xyd_price'] = $info['xyd_price'];
				$data['add_time'] = date("Y-m-d H:i:s");
				$data['user_id'] = $user_id;
				$res = $xydod->add($data);
				if($res){
					$member->where(array("id"=>$user_id))->setDec("jinbi",$data['xyd_price']);
					$this->success("参加成功，请等待开奖结果！",U('index/shop/xyd'));
				}
				else{
					$this->error("参失败！");
				}
				exit;
			}
			if($id){
				$user = $member->where(array("id"=>$user_id))->find();
				$info = $xyd->where(array("id"=>$id))->find();

                import('ORG.Util.BlockChain');
                $bc = new BlockChain();
                $user['wallet'] = $bc->findWallet($user['wallet_code'], $user['wallet_pows']);

				$this->assign("user",$user);
				$this->assign("info",$info);
				$this->display();
			}
			else{
				$this->error("参数错误！");
			}
		}
		/**
		 * 幸运兑订单列表
		 */
		public function xydodlist(){
			$xyd = M("xyd");
			$xydod = M("xydod");
			$user_id = $_SESSION['mid'];
			$odlist = $xydod->where(array("user_id"=>$user_id))->select();
			foreach($odlist as $key=>$val){
				$xydin = $xyd->where(array("id"=>$val['xyd_id']))->find();
				$gpic=explode(",",$xydin['xyd_pic']);
				$odlist[$key]['conut'] = $xydod->where(array("xyd_id"=>$val['xyd_id']))->count();
				$odlist[$key]['xyd_name'] = $xydin['xyd_name'];
				$odlist[$key]['end_time'] = date("Y-m-d H:i:s",$xydin['xyd_endtime']);
				$odlist[$key]['xyd_pic'] = $gpic[0];
			}
			$this->assign("odlist",$odlist);
			$this->display();
		}
	/**
     * 根据经纬度获取地理位置-高德地图
     * @param string $lon 经度
     * @param string $lat 纬度
     * @return array
     */
    public function regeo($lon, $lat)
    {
        // Key 是高德Web服务 Key。详细可以参考上方的请求参数说明。
        // location(116.310003,39.991957) 是所需要转换的坐标点经纬度，经度在前，纬度在后，经纬度间以“,”分割
        $location = $lon . "," . $lat;
        /**
         * url:https://restapi.amap.com/v3/geocode/regeo?output=xml&location=116.310003,39.991957&key=<用户的key>&radius=1000&extensions=all
         * radius（1000）为返回的附近POI的范围，单位：米
         * extensions 参数默认取值是 base，也就是返回基本地址信息
         * extensions 参数取值为 all 时会返回基本地址信息、附近 POI 内容、道路信息以及道路交叉口信息。
         * output（XML/JSON）用于指定返回数据的格式
         */
        $url = "https://restapi.amap.com/v3/geocode/regeo?output=JSON&location={$location}&key=11e5f66de0df1b6e62a430effa370da9&radius=1000&extensions=base";
		
        // 执行请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($data, true);

        return $result;
    }
}



?>