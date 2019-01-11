<?php  
	
Class SeedAction extends CommonAction{

	/**
	 * 我的钱包
	 */
	public function index(){
		$member=M("member");
		$user_id=$_SESSION['mid'];
		$user=$member->where(array("id"=>$user_id))->find();
		$path = './App/Conf/system.php';
		$config = include $path;
		$danjia=$config['danjia'];

        import('ORG.Util.BlockChain');
        $bc = new BlockChain();
        $user['wallet'] = $bc->findWallet($user['wallet_code'], $user['password']);

		$this->assign("danjia",$danjia);
		$this->assign("user",$user);
		$this->display();
	}
	/**
	 * 种子获取记录
	 */
	public function zzjilu(){
		$member=M("member");
		$user_id=$_SESSION['mid'];
		$seed_log=M('seed_log');
		$user=$member->where(array("id"=>$user_id))->find();
		$seed_list=$seed_log->where(array("user_id"=>$user_id))->select();
		
		
		$this->assign("list",$seed_list);
		$this->assign("user",$user);
		$this->display();
	}
	/**
	 * 种子交易
	 */
	public function zzjiaoyi(){
		$zztrans = M('zztrans');
		$transorder = M("transorder");
		$member = M('member');
		$user_id = $_SESSION['mid'];
		$id=$_GET['id'];
		$zzjy=$transorder->where(array("trans_id"=>$id))->find();
		if(!$zzjy){
			$zzt=$zztrans->where(array('id'=>$id))->find();
			$data['trans_id'] = $zzt['id'];
			$data['cuser_id'] = $zzt['user_id'];
			$data['guser_id'] = $user_id;
			$data['trans_num'] = $zzt['zz_num'];
			$data['trans_price'] = $zzt['zz_price'];
			$data['price'] = $zzt['zz_num']*$zzt['zz_price'];
			$data['addtime'] = time();
			$res=$transorder->add($data);
			if($res){
				$zztrans->where(array("id"=>$id))->save(array('trans_state'=>2));
				$info = $transorder->where(array("trans_id"=>$id))->find();
				$user = $member->where(array("id"=>$info['cuser_id']))->find();
				$this->assign("user",$user);
				$this->assign("info",$info);
			}
			else{
				$this->error("交易添加失败！");
				exit;
			}
			
		}
		else{
			$info = $transorder->where(array("trans_id"=>$id))->find();
			$user = $member->where(array("id"=>$info['cuser_id']))->find();
			$this->assign("user",$user);
			$this->assign("info",$info);
		}
		$this->assign("zzjy",$zzjy);
		$this->assign("user_id",$user_id);
		$this->display();
	}
	/**
	 * 放弃订单
	 */
	public function delzzjy(){
		$zztrans = M('zztrans');
		$transorder = M("transorder");
		$id=$_GET['id'];
		$jydd=$transorder->where(array('id'=>$id))->find();
		$res=$zztrans->where(array('id'=>$jydd['trans_id']))->save(array("trans_state"=>2));
		if($res){
			$r=$transorder->where(array('id'=>$id))->delete();
			$this->success("已放弃购入订单！",U('Index/seed/grlist'));
		}
		else{
			$this->error('放弃订单失败！');
		}
	}
	/**
	 * 订单已打款
	 */
	public function fkzzjy(){
		$zztrans = M('zztrans');
		$transorder = M("transorder");
		$id=$_GET['id'];
		$jydd=$transorder->where(array('id'=>$id))->find();
		$res=$zztrans->where(array('id'=>$jydd['trans_id']))->save(array("trans_state"=>3));
		if($res){
			$r=$transorder->where(array('id'=>$id))->save(array('state'=>2));
			$this->success("已完成我已打款操作！",U('Index/seed/grlist'));
		}
		else{
			$this->error("操作失败！");
		}
	}
	/**
	 * 购入列表
	 */
	public function grlist(){
		$zztrans = M('zztrans');
		$transorder = M("transorder");
		$member = M('member');
		$user_id = $_SESSION['mid'];
		$list=$transorder->where(array('guser_id'=>$user_id))->order("id desc")->select();
		foreach($list as $key=>$val){
			$user=$member->where(array('id'=>$user_id))->find();
			$list[$key]['zhifubao']=$user['zhifubao'];
			$list[$key]['truename']=$user['truename'];
			$list[$key]['mobile']=$user['mobile'];
			
		}
		$this->assign("list",$list);
		$this->display();
	}
	/**
	 * 出让订单
	 */
	public function crlist(){
		$zztrans = M('zztrans');
		$member = M('member');
		$transorder = M("transorder");
		$user_id=$_SESSION['mid'];
		$list=$zztrans->where(array("user_id"=>$user_id))->order("id desc")->select();
		$user=$member->where(array('id'=>$user_id))->find();
		
		$this->assign('list',$list);
		$this->assign('user',$user);
		$this->display();
	}
	/**
	 * 确认打款成功交易种子
	 */
	public function crqueren(){
		$zztrans = M('zztrans');
		$member = M('member');
		$transorder = M("transorder");
		$gyc = M("gyc");
		
		$user_id = $_SESSION['mid'];
		$id = $_GET['id'];
		$zz = $zztrans->where(array("id"=>$id))->find();
		$tran = $transorder->where(array('trans_id'=>$id))->find();

        $sxf = $tran['trans_num']*0.05;//5%手续费
        $dszz = $tran['trans_num']-$sxf;

        $from = M('member')->where(['id'=>$tran['cuser_id']])->find();
        $to = M('member')->where(['id'=>$tran['guser_id']])->find();
        import('ORG.Util.BlockChain');
        $bc = new BlockChain();
        // 先完成交易
        $bc->transaction($from['wallet_code'], $to['wallet_code'], $dszz, $from['password']);
        // 扣除手续费
        $bc->transaction($from['wallet_code'], C('chain_address'), $sxf, $from['password']);

		$zztrans->where(array('id'=>$id))->save(array('trans_state'=>4));
		$transorder->where(array('trans_id'=>$id))->save(array('state'=>3));
		$member->where(array("id"=>$tran['guser_id']))->setInc('jinbi',$dszz);
		
		$gycdt['user_id'] = 0;
		$gycdt['yield'] = $sxf;
		$gycdt['reason'] = '种子交易5%手续费';
		$gycdt['time'] = date("Y-m-d");
		$gyc->add($gycdt);
		//$member->where(array("id"=>$user_id))->setDec('jinbi',$tran['trans_num']);
		$this->success('确认成功！',U('Index/seed/crlist'));
		
	}
	/**
	 * 取消打款状态
	 */
	public function qxfkzt(){
		$zztrans = M('zztrans');
		$member = M('member');
		$transorder = M("transorder");
		$user_id = $_SESSION['mid'];
		$id = $_GET['id'];
		$zztrans->where(array('id'=>$id))->save(array('trans_state'=>2));
		$transorder->where(array('trans_id'=>$id))->save(array('state'=>1));
		$this->success('取消状态成功！',U('Index/seed/grlist'));
	}
	/**
	 * 种子赠送
	 */
	public function zzgive(){
		$member = M("member");
		$zzgive = M("zzgive");
		$user_id = $_SESSION['mid'];
		$mem_info = $member->where(array("id"=>$user_id))->find();
		if($mem_info['parent_id']){
			$where = "parent_id = ".$user_id." or id = ".$mem_info['parent_id'];
		}
		else{
			$where = "parent_id = ".$user_id;
		}
		$mem_list = $member->where($where)->select();

        import('ORG.Util.BlockChain');
        $bc = new BlockChain();
        $mem_info['wallet'] = $bc->findWallet($mem_info['wallet_code'], $mem_info['password']);

		if(IS_POST){
			$data['suser_id'] = $_POST['suser_id'];
			$data['yield'] = $_POST['num'];
			$data['user_id'] = $_POST['mid'];
			$data['state'] = 1;
			$data['time'] = date("Y-m-d H:i:s");
			$res=$zzgive->add($data);
			if($res){
				$member->where(array('id'=>$data['user_id']))->setDec('jinbi',$data['yield']);
				$this->success("赠送成功，等待对方收取！",U('index/seed/zzgive_list'));
			}
			else{
				$this->error("赠送失败！");
			}
			exit;
		}
		$this->assign("mem_list",$mem_list);
		$this->assign("mem_info",$mem_info);
		$this->display();
	}
	/**
	 * 种子赠送列表
	 */
	public function zzgive_list(){
		$member = M("member");
		$zzgive = M("zzgive");
		$user_id = $_SESSION['mid'];
		$user = $member->where(array("id"=>$user_id))->find();
		
		import('ORG.Util.Page');
		$count = $zzgive->where(array("user_id"=>$user_id))->count();
		$page = new Page($count);
		$show = $page->show();// 分页显示输出
		
		$givelist = $zzgive->where(array("user_id"=>$user_id))->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		foreach($givelist as $key=>$val){
			$suser = $member->field()->where(array("id"=>$val['suser_id']))->find();
			$givelist[$key]['user'] = $user['truename'];
			$givelist[$key]['suser'] = $suser['truename'];
		}
		$this->assign("page",$show);
		$this->assign('givelist',$givelist);
		$this->display();
	}
	/**
	 * 种子接收列表
	 */
	public function zzsgive(){
		$member = M("member");
		$zzgive = M("zzgive");
		$user_id = $_SESSION['mid'];
		$user = $member->where(array("id"=>$user_id))->find();
		
		import('ORG.Util.Page');
		$count = $zzgive->where(array("suser_id"=>$user_id))->count();
		$page = new Page($count);
		$show = $page->show();// 分页显示输出
		
		$givelist = $zzgive->where(array("suser_id"=>$user_id))->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		foreach($givelist as $key=>$val){
			$suser = $member->field()->where(array("id"=>$val['user_id']))->find();
			$givelist[$key]['suser'] = $user['truename'];
			$givelist[$key]['user'] = $suser['truename'];
		}
		$this->assign("page",$show);
		$this->assign('givelist',$givelist);
		$this->display();
	}
	/**
	 * 接收种子
	 */
	public function jsgive(){
		$member = M("member");
		$zzgive = M("zzgive");
		$user_id = $_SESSION['mid'];
		if($_GET['id']){
			$id = $_GET['id'];
			$zzinfo = $zzgive->where(array('id'=>$id))->find();

            $from = M('member')->where(['id'=>$zzinfo['user_id']])->find();
            $to = M('member')->where(['id'=>$zzinfo['suser_id']])->find();
            import('ORG.Util.BlockChain');
            $bc = new BlockChain();
            $bc->transaction($from['wallet_code'], $to['wallet_code'], $zzinfo['yield'], $from['password']);

			$res = $zzgive->where(array('id'=>$id))->save(array("state"=>2));
			if($res){
				$member->where(array('id'=>$zzinfo['suser_id']))->setInc('jinbi',$zzinfo['yield']);
				$this->success("接收成功！",U('index/seed/zzsgive'));
			}
			else{
				$this->error("接收失败！");
			}
			
		}
		else{
			$this->error("参数错误！");
		}
	}
	/**
	 * 取消未接受的种子赠送
	 */
	public function qxgive(){
		$member = M("member");
		$zzgive = M("zzgive");
		$user_id = $_SESSION['mid'];
		if($_GET['id']){
			$id = $_GET['id'];
			$zzinfo = $zzgive->where(array('id'=>$id))->find();
			if($zzinfo['state'] == 1){
				$res = $zzgive->where(array('id'=>$id))->delete();
				if($res){
					$member->where(array('id'=>$zzinfo['user_id']))->setInc('jinbi',$zzinfo['yield']);
					$this->success("取消成功！",U('index/seed/zzgive_list'));
				}
				else{
					$this->error("取消失败！");
				}
			}
			else{
				$this->error("种子已被接收不可取消！");
			}
			
		}
		else{
			$this->error("参数错误！");
		}
	}
}
?>