<?php  
	
	/**
	 * 公告留言控制器
	 */
	Class InfoAction extends CommonAction{

		//公告类别视图
		public function annType(){
			$type = M('announcetype')->select();
			$this->assign('type',$type);
			$this->display();
		}
	/*	public function vvv(){
				
			$id_arr=M('announce')->field("id")->select();
			//print_r($id_arr);
			$str_arr=array();
			foreach($id_arr as $v){
					
					$str_arr[]=$v['id'];
			}
			print_r($str_arr);
			$map=array();
			$map['news_id'] = array('in',$str_arr);
			
			echo $aa=M('announce_click')->where($map)->count();
			
			exit("##");
		}
*/
		


		//添加公告类别视图
		public function addAnnounceType(){
				
			
			$this->display();
		}

		//添加公告类别处理
		public function addAnnounceTypeHandle(){

			if (M('announcetype')->data(array('name'=>I('name')))->add()) {
				//添加日志操作
				$desc = '添加一个新的公告类别';
				write_log(session('username'),'admin',$desc);

				$this->success('添加成功!',U(GROUP_NAME.'/Info/annType'));
			}else{
				$this->error('添加失败!');
			}
		}
		
		//修改公告类别视图
		public function editAnnounceType(){
			$type = M('announcetype')->where(array('id'=>I('id')))->find();
			$this->assign('type',$type);
			$this->display();
		}

		//修改公告类别处理
		public function editAnnounceTypeHandle(){
			M('announcetype')->where(array('id'=>I('tid')))->save(array('name'=>I('name')));
			//添加日志操作
			$desc = '修改了一个公告类别';
			write_log(session('username'),'admin',$desc);

			$this->success('修改类别名称成功！',U(GROUP_NAME.'/Info/annType'));
		}

		//删除公告类别
		public function deleteAnnounceType(){
			//删除公告类别
			//先判断当前类别是否有公告
			if (M('announce')->where(array('tid'=>I('id')))->find()) {
				$this->error('对不起，该分类下面有公告！');
			}else{
				M('announcetype')->where(array('id'=>I('id')))->delete();
				//添加日志操作
				$desc = '删除公告类别';
				write_log(session('username'),'admin',$desc);

				$this->success('删除成功！');
			}	
		}

		//公告列表视图
		public function announce(){
			$ann = D('AnnounceRelation')->relation(true)->select();
			$this->assign('ann',$ann);
			$this->display();
		}

		//添加公告视图
		public function addAnnounce(){
		
			//获取公告类别
			$type = M('announcetype')->select();
			$this->assign('type',$type);
			$this->assign('time',time());
			$this->display();
		}

		//添加公告处理
		public function addAnnounceHandle(){

			$_POST['addtime'] = strtotime($_POST['addtime']);
			$_POST['edittime'] = $_POST['addtime'];
			if (M('announce')->data($_POST)->add()) {
				//添加日志操作
				$desc = '发布公告';
				write_log(session('username'),'admin',$desc);

				$this->success('添加成功!',U(GROUP_NAME.'/Info/announce'));
			}else{
				$this->error('添加失败！');
			}
		}

		//修改公告视图
		public function editAnnounce(){
			$type = M('announcetype')->select();
			$ann = D('AnnounceRelation')->where(array('id'=>I('id')))->relation(true)->find();
			$ann['content'] = stripslashes($ann['content']);
			$this->assign('ann',$ann);
			$this->assign('type',$type);
			$this->display();
		}

		//修改公告处理
		public function editAnnounceHandle(){
			$_POST['edittime'] = time();
			$id = I('aid');
			unset($_POST['aid']);

			M('announce')->where(array('id'=>$id))->data($_POST)->save();
			//添加日志操作
			$desc = '修改公告';
			write_log(session('username'),'admin',$desc);

			$this->success('修改成功',U(GROUP_NAME.'/Info/announce'));
		}

		//删除公告
		public function deleteAnnounce(){
			if (M('announce')->where(array('id'=>I('id')))->delete()) {
				//添加日志操作
				$desc = '删除公告';
				write_log(session('username'),'admin',$desc);
				//同时删除 对应用户新闻表
				M("announce_click")->where(array('news_id'=>I('id')))->delete();
				
				
				$this->success('删除成功!');
			}else{
				$this->error('删除失败!');
			}
			
		}

		//收件箱
		public function msgReceive(){
			import("@.ORG.Util.Page");// 导入分页类
			
			$count      = M('message')->alias('a')->where(array('a.to'=>''))->count();// 查询满足要求的总记录数
	        $Page       = new Page($count,30);// 实例化分页类 传入总记录数
			
			$msg = M('message')->alias('a')
			->field('a.*,b.id as user_id')
			->join('ds_member as b on a.from = b.username')
			->where(array('a.to'=>''))->order('a.sendtime desc')->limit($Page ->firstRow.','.$Page -> listRows)->select();
			$show       = $Page->show();// 分页显示输出
			$this->assign('msg',$msg);
			$this->assign('page',$show);// 赋值分页输出
			$this->display();
		}

		//AJAX返回查询到的留言信息
		public function ajaxMsgReceive(){
			//判断是否异步提交
			IS_AJAX or halt('对不起，页面不存在');

			$message = M('message'); 
			$data = $message->where(array('id'=>I('id')))->find();
			$message->where(array('id'=>I('id')))->setField('hasview',1);
			$this->ajaxReturn($data,'JSON');
		}

		//异步回复留言
		public function replyMessage(){
			IS_AJAX or halt('对不起，页面不存在');
			$data['reply'] = I('post.reply');
			$data['status'] = '已回復';
			$data['writetime'] = time();
			if (M('message')->where(array('id'=>I('post.id')))->data($data)->save()) {
			 	echo true;
			 }else{
			 	echo false;
			 }
		}

		//删除留言
		public function deleteMessage(){
			if (M('message')->where(array('id'=>I('id')))->delete()) {
				//添加日志操作
				$desc = '删除留言';
				write_log(session('username'),'admin',$desc);

				$this->success('删除成功',U(GROUP_NAME.'/Info/msgReceive'));
			}else{
				$this->error('删除失败');
			}
		}

		//发件箱
		public function msgSend(){
			$condition['from'] = 'admin';
			$condition['reply'] = array('NEQ',''); 
			$condition['_logic'] = 'OR';
			$message = M('message')->where($condition)->select();
			$this->assign('msg',$message);
			$this->display();
		}
		/**
		 * 广告位置
		 */
		public function adv_pos(){
			import("@.ORG.Util.Page");// 导入分页类
			$advpos=M("advpos");
			$count      = $advpos->where()->count();// 查询满足要求的总记录数
	        $Page       = new Page($count,10);// 实例化分页类 传入总记录数
			$pos=$advpos->where()->limit($Page ->firstRow.','.$Page -> listRows)->select();
			$show       = $Page->show();// 分页显示输出
			$this->assign("pos",$pos);
			$this->assign('page',$show);// 赋值分页输出
			$this->display();
		}
		/**
		 * 广告位置添加
		 */
		public function addpos(){
			$advpos=M("advpos");
			if(IS_POST){
				$data['position']=$_POST['position'];
				$data['pos_rem']=$_POST['pos_rem'];
				$res=$advpos->add($data);
				if($res){
					$this->success("添加成功！",U('Info/adv_pos'));
				}
				else{
					$this->error("添加失败！");
				}
				exit;
			}
			
			$this->display();
		}
		/**
		 * 广告位置删除
		 */
		public function posdel(){
			$advpos=M("advpos");
			$id=$_GET['id'];
			$res=$advpos->where(array("id"=>$id))->delete();
			if($res){
				$this->success("删除成功！",U("info/adv_pos"));
			}
			else{
				$this->error("删除失败！");
			}
		}
		/**
		 * 广告管理
		 */
		public function adv(){
			$adv=M("adv");
			$advpos=M("advpos");
			import("@.ORG.Util.Page");// 导入分页类
			$count      = $adv->where()->count();// 查询满足要求的总记录数
	        $Page       = new Page($count,5);// 实例化分页类 传入总记录数
			$adv_lst=$adv->where()->limit($Page ->firstRow.','.$Page -> listRows)->select();
			foreach($adv_lst as $key=>$val){
				$pos=$advpos->where(array("id"=>$val['pos_id']))->find();
				$adv_lst[$key]['pos_name']=$pos['position'];
			}
			$show       = $Page->show();// 分页显示输出
			
			$this->assign("adv_list",$adv_lst);
			$this->assign("page",$show);
			$this->display();
		}
		/**
		 * 广告添加
		 */
		public function addadv(){
			$adv=M("adv");
			$advpos=M("advpos");
			if(IS_POST){
				$data['adv_name']=$_POST['adv_name'];
				$data['adv_url']=$_POST['adv_url'];
				$data['adv_rem']=$_POST['adv_rem'];
				$data['pos_id']=$_POST['pos_id'];
				//print_r($_FILES);
				$dest_folder   = './Public/Adv/'.date('Ymd',time()).'/';   //上传图片保存的路径
				
				$goodsimg = array();//定义多图存放数组
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
				if($_FILES['adv_pic']['error']==0){
					 $tmp_namey = $_FILES["adv_pic"]["tmp_name"];
	                 $ay=explode(".",$_FILES["adv_pic"]["name"]);  //截取文件名跟后缀
	                 $prename = $ay[0];
	                 $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                 $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                 move_uploaded_file($tmp_namey, $uploadfiley);
	                 $goodsimg=ltrim($uploadfiley,'.');
				}
				$data['adv_pic']=$goodsimg;
				$res=$adv->add($data);
				if($res){
					$this->success("添加成功！",U('Info/adv'));
				}
				else{
					$this->error("添加失败！");
				}
				exit;
			}
			$pos=$advpos->select();
			$this->assign("pos",$pos);
			$this->display();
			
		}
		/**
		 * 广告删除
		 */
		public function advdel(){
			$adv=M("adv");
			$id=$_GET['id'];
			$res=$adv->where(array("id"=>$id))->delete();
			if($res){
				$this->success("删除成功！",U("info/adv"));
			}
			else{
				$this->error("删除失败！");
			}
		}
		/**
		 * 种子收取广告
		 */
		public function zz_adv(){
			$zz_adv = M("zzadv");
			import("@.ORG.Util.Page");// 导入分页类
			$count      = $zz_adv->where()->count();// 查询满足要求的总记录数
	        $Page       = new Page($count,10);// 实例化分页类 传入总记录数
			$adv_lst=$zz_adv->where()->limit($Page ->firstRow.','.$Page -> listRows)->order("id desc")->select();
			$show       = $Page->show();// 分页显示输出
			$this->assign("adv_list",$adv_lst);
			$this->assign("page",$show);
			$this->display();
		}
		/**
		 * 种子广告添加
		 */
		public function add_zzadv(){
			
			$zz_adv=M("zzadv");
			if(IS_POST){
				$data['title']=$_POST['title'];
				$data['val']=$_POST['val'];
				$data['add_time']=date("Y-m-d H:i:s");
				//print_r($_FILES);
				$dest_folder   = './Public/Adv/'.date('Ymd',time()).'/';   //上传图片保存的路径
				$goodsimg = array();//定义多图存放数组
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				}
				if($_FILES['pic']['error']==0){
					 $tmp_namey = $_FILES["pic"]["tmp_name"];
	                 $ay=explode(".",$_FILES["pic"]["name"]);  //截取文件名跟后缀
	                 $prename = $ay[0];
	                 $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                 $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                 move_uploaded_file($tmp_namey, $uploadfiley);
	                 $goodsimg=ltrim($uploadfiley,'.');
				}
				$data['pic']=$goodsimg;
				$res=$zz_adv->add($data);
				if($res){
					$this->success("添加成功！",U('Info/zz_adv'));
				}
				else{
					$this->error("添加失败！");
				}
				exit;
			}
			$this->display();
		}
		/**
		 * 种子广告修改
		 */
		public function edit_zzadv(){
			$id = $_GET['id'];
			$zz_adv = M("zzadv");
			$adv_info = $zz_adv->where(array("id"=>$id))->find();
			if(IS_POST){
				$data['title']=$_POST['title'];
				$data['val']=$_POST['val'];
				$data['add_time']=date("Y-m-d H:i:s");
				//print_r($_FILES);
				$dest_folder   = './Public/Adv/'.date('Ymd',time()).'/';   //上传图片保存的路径
				$goodsimg = $_POST['pic'];
				
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
				  
				if($_FILES['pic']['error']===0){
					 $tmp_namey = $_FILES["pic"]["tmp_name"];
	                 $ay=explode(".",$_FILES["pic"]["name"]);  //截取文件名跟后缀
	                 $prename = $ay[0];
	                 $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                 $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                 move_uploaded_file($tmp_namey, $uploadfiley);
	                 $goodsimg=ltrim($uploadfiley,'.');
				}
				$data['pic']=$goodsimg;
				$res=$zz_adv->where(array("id"=>$_POST['id']))->save($data);
				if($res){
					$this->success("修改成功！",U('Info/zz_adv'));
				}
				else{
					$this->error("修改失败！");
				}
				exit;
			}
			$this->assign("info",$adv_info);
			$this->display();
		}
		/**
		 * 种子广告删除
		 */
		public function zzadvdel(){
			$id = $_GET['id'];
			$zz_adv = M("zzadv");
			$res = $zz_adv->where(array("id"=>$id))->delete();
			if($res){
				$this->success("删除成功！",U('Info/zz_adv'));
			}
			else{
				$this->error("删除失败！");
			}
		}
	}
?>