<?php  

	/**
	* 会员管理控制器
	*/
	class GoodsclassAction extends CommonAction{
		
		
		public function index(){
			$classify=M('classify');
			
			$class_list=$classify->where("parentid = 0")->select();
			$this->assign("class_list",$class_list);
			$this->display();
		}
		
		
		/**
		 * ajax 查询商品分类下级分类
		 */
		public function ajaxclass(){
			$classify=M('classify');
			$cid=$_POST['cid'];
			$class_list=$classify->where("parentid = $cid")->select();
			echo json_encode($class_list);
			exit;
		}
		/**
		 * 分类添加
		 */
		public function addclass(){
			$classify=M('classify');
			$class_list=$classify->where("parentid = 0")->select();
			if(IS_POST){
				$data['cname']=$_POST['cname'];
				$data['parentid']=$_POST['parentid'];
				$data['pid1']=$_POST['pid1'];
				$data['pid2']=$_POST['pid2'];
				$res=$classify->add($data);
				if($res){
					$this->success("分类添加成功！",U('goodsclass/index'));
				}
				else{
					$this->error("分类添加失败！");
				}
				exit;
			}			
			$this->assign("class_list",$class_list);
			$this->display();
		}
		/**
		 * 分类修改
		 */
		public function editclass(){
			$classify=M('classify');
			$class_list=$classify->where("parentid = 0")->select();
			$cid=$_GET['id'];
			$info=$classify->where("cid = ".$cid)->find();
			$class_list1='';
			if($info['pid1']>0){
				$class_list1=$classify->where("parentid = ".$info['pid1'])->select();
			}
			if(IS_POST){
				$cid=$_POST['cid'];
				$data['cname']=$_POST['cname'];
				$data['parentid']=$_POST['parentid'];
				$data['pid1']=$_POST['pid1'];
				$data['pid2']=$_POST['pid2'];
				$res=$classify->where(array("cid"=>$cid))->save($data);
				if($res){
					$this->success("分类修改成功！",U('goodsclass/index'));
				}
				else{
					$this->error("分类修改失败！");
				}
				exit;
			}			
			
			$this->assign("class_list1",$class_list1);
			$this->assign("class_list",$class_list);
			$this->assign("info",$info);
			$this->display();
		}
		/**
		 * 分类删除
		 */
		public function delclass(){
			$classify=M('classify');
			$cid = $_GET['id'];
			if($cid){
				$res = $classify->where(array("cid"=>$cid))->delete();
				if($res){
					$this->success("删除成功！",U("Goodsclass/index"));
				}
				else{
					$this->error("删除失败！");
				}
			}
			else{
				$this->error("参数错误！");
			}
		}
		
		/**
		 * 商品列表
		 */
		public function goods(){
			$goods=M("goods");
			$classify=M('classify');
			import("@.ORG.Util.Page");// 导入分页类    	   	
	    	$count = $goods->where()->count(); // 查詢滿足要求的總記錄數
	    	$p = new Page($count,20);
			$goods_list=$goods->where()->limit ( $p->firstRow, $p->listRows )->select();
			foreach($goods_list as $k=>$v){
				$class=$classify->where(array("cid"=>$v['gclassification']))->find();
				$goods_list[$k]["class"]=$class['cname'];
			}
			
			$show       = $p->show();// 分页显示输出
		    $this->assign('page',$show);// 赋值分页输出		
			$this->assign("goods_list",$goods_list);
			$this->display();
		}
		/**
		 * 商品添加
		 */
		public function addgoods(){
			$classify=M('classify');
			$goods=M('goods');
			$class_list=$classify->where("parentid = 0")->select();
			if(IS_POST){
				$count=0;
			
				$dest_folder   = './Public/Uploads/'.date('Ymd',time()).'/';   //上传图片保存的路径
				
				$goodsimg = array();//定义多图存放数组
				
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
	            foreach ($_FILES["gpic"]["error"] as $key => $error){
	                
	                if($error == 0){
	                    $tmp_namey = $_FILES["gpic"]["tmp_name"][$key];
	                    $ay=explode(".",$_FILES["gpic"]["name"][$key]);  //截取文件名跟后缀
	                    $prename = $ay[0];
	                    $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                    $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                    move_uploaded_file($tmp_namey, $uploadfiley);
	                    $goodsimg[$count]=$uploadfiley;
	                    $count++;
	                }
	            }
	            $goodsimgs = implode(',',$goodsimg);
    			$data['gname']=$_POST['gname'];
    			$data['pid1']=$_POST['pid1'];
    			$data['pid2']=$_POST['pid2'];
    			$data['pid3']=$_POST['pid3'];	
    			$data['gclassification']=$_POST['gclassification'];
    			$data['goldprice']=$_POST['goldprice'];
    			$data['gprice']=$_POST['gprice'];
    			$data['goods_num']=$_POST['goods_num'];
    			$data['gintroduce']=$_POST['gintroduce'];
    			$data['gpic']=$goodsimgs;
				$res=$goods->add($data);
				if($res){
					$this->success("添加成功！",U('Goodsclass/goods'));
				}
				else{
					$this->error("添加失败！");
				}
				exit;
			}
			$this->assign("class_list",$class_list);
			$this->display();
		}
		/**
		 * 商品修改
		 */
		public function editgoods(){
			$classify=M('classify');
			$goods=M('goods');
			$goods_id=$_GET['id'];
			$class_list="";
			$class_list2="";
			$class_list3="";
			$goods_info=$goods->where(array("gid"=>$goods_id))->find();
			$class_list=$classify->where("parentid = 0")->select();
			if($goods_info['pid2']>0){
				$class_list2=$classify->where("parentid = ".$goods_info['pid1'])->select();
			}
			if($goods_info['pid3']>0){
				$class_list3=$classify->where("parentid = ".$goods_info['pid2'])->select();
			}
			$goods_info["gimg"]=explode(",",$goods_info['gpic']);
			
			
			
			if(IS_POST){
				$count=0;
			
				$dest_folder   = './Public/Uploads/'.date('Ymd',time()).'/';   //上传图片保存的路径
				
				$goodsimg = array();//定义多图存放数组
				
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
	            foreach ($_FILES["gpic"]["error"] as $key => $error){
	                
	                if($error == 0){
	                    $tmp_namey = $_FILES["gpic"]["tmp_name"][$key];
	                    $ay=explode(".",$_FILES["gpic"]["name"][$key]);  //截取文件名跟后缀
	                    $prename = $ay[0];
	                    $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                    $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                    move_uploaded_file($tmp_namey, $uploadfiley);
	                    $goodsimg[$count]=$uploadfiley;
	                    $count++;
	                }
	            }
	            $goodsimgs = implode(',',$goodsimg);
    			$data['gname']=$_POST['gname'];
    			$data['pid1']=$_POST['pid1'];
    			$data['pid2']=$_POST['pid2'];
    			$data['pid3']=$_POST['pid3'];	
    			$data['gclassification']=$_POST['gclassification'];
    			$data['goldprice']=$_POST['goldprice'];
    			$data['gprice']=$_POST['gprice'];
    			$data['goods_num']=$_POST['goods_num'];
    			$data['gpic']=$goodsimgs;
    			//print_r($data);exit;
				$res=$goods->add($data);
				if($res){
					$this->success("添加成功！",U('Goodsclass/goods'));
				}
				else{
					$this->error("添加失败！");
				}
				exit;
			}
			$this->assign("goods_info",$goods_info);
			$this->assign("class_list",$class_list);
			$this->assign("class_list2",$class_list2);
			$this->assign("class_list3",$class_list3);
			$this->display();
		}
		
		/**
		 * 种子商城
		 */
		public function jzzgoods(){
			$jzzgoods=M("jzzgoods");
			import("@.ORG.Util.Page");// 导入分页类    	   	
	    	$count = $jzzgoods->where()->count(); // 查詢滿足要求的總記錄數
	    	$p = new Page($count,20);
			$goods_list=$jzzgoods->where()->limit ( $p->firstRow, $p->listRows )->select();
			$show       = $p->show();// 分页显示输出
		    $this->assign('page',$show);// 赋值分页输出		
			$this->assign("goods_list",$goods_list);
			$this->display();
		}
		/**
		 * 添加金种子商城
		 */
		public function addjzzgoods(){
			$jzzgoods=M("jzzgoods");
			if(IS_POST){
				$count=0;
				$dest_folder   = './Public/Uploads/'.date('Y-m-d',time()).'/';   //上传图片保存的路径
				$goodsimg = array();//定义多图存放数组
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
	            foreach ($_FILES["gpic"]["error"] as $key => $error){
	                if($error == 0){
	                    $tmp_namey = $_FILES["gpic"]["tmp_name"][$key];
	                    $ay=explode(".",$_FILES["gpic"]["name"][$key]);  //截取文件名跟后缀
	                    $prename = $ay[0];
	                    $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                    $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                    move_uploaded_file($tmp_namey, $uploadfiley);
	                    $goodsimg[$count]=ltrim($uploadfiley,'.');
	                    $count++;
	                }
	            }
	            $goodsimgs = implode(',',$goodsimg);
    			$data['gname']=$_POST['gname'];
    			$data['goldprice']=$_POST['goldprice'];
    			$data['gprice']=$_POST['gprice'];
    			$data['goods_num']=$_POST['goods_num'];
    			$data['postage']=$_POST['postage'];
    			$data['center']=$_POST['center'];
    			$data['gintroduce']=$_POST['gintroduce'];
    			$data['goods_spec']=$_POST['goods_spec'];
    			$data['if_tg']=$_POST['if_tg'];
    			$data['gpic']=$goodsimgs;
				$res=$jzzgoods->add($data);
				if($res){
					$this->success("添加成功！",U('Goodsclass/jzzgoods'));
				}
				else{
					$this->error("添加失败！");
				}
				exit;
			}
			$this->display();
		}
		/**
		 * 修改金种子商品
		 */
		public function editjzzgoods(){
			$jzzgoods=M("jzzgoods");
			
			if(IS_POST){
				$count=0;
			
				$dest_folder   = './Public/Uploads/'.date('Ymd',time()).'/';   //上传图片保存的路径
				
				$goodsimg = array();//定义多图存放数组
				$goodsimg=$_POST['gpic'];
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
	            foreach ($_FILES["gpic"]["error"] as $key => $error){
	                
	                if($error == 0){
	                    $tmp_namey = $_FILES["gpic"]["tmp_name"][$key];
	                    $ay=explode(".",$_FILES["gpic"]["name"][$key]);  //截取文件名跟后缀
	                    $prename = $ay[0];
	                    $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                    $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                    move_uploaded_file($tmp_namey, $uploadfiley);
	                    $goodsimg[$key]=ltrim($uploadfiley,'.');
	                    $count++;
	                }
	            }
	            $id=$_POST['id'];
	            $goodsimgs = implode(',',$goodsimg);
    			$data['gname']=$_POST['gname'];
    			$data['goldprice']=$_POST['goldprice'];
    			$data['gprice']=$_POST['gprice'];
    			$data['goods_num']=$_POST['goods_num'];
    			$data['postage']=$_POST['postage'];
    			$data['center']=$_POST['center'];
    			$data['gintroduce']=$_POST['gintroduce'];
    			$data['goods_spec']=$_POST['goods_spec'];
    			$data['if_tg']=$_POST['if_tg'];
    			$data['gpic']=$goodsimgs;
				$res=$jzzgoods->where(array("gid"=>$id))->save($data);
				if($res){
					$this->success("修改成功！",U('Goodsclass/jzzgoods'));
				}
				else{
					$this->error("修改 失败！");
				}
				exit;
			}
			if($_GET['id']){
				$id=$_GET['id'];
				$info=$jzzgoods->where(array("gid"=>$id))->find();
				$gpic=explode(",",$info['gpic']);
				
				
				$this->assign("gpic",$gpic);
				$this->assign("info",$info);
				$this->display();
			}
			else{
				$this->error("参数错误！");
			}
			
			
		}
		/**
		 * 删除开心对商品
		 */
		public function deljzzgoods(){
			$jzzgoods=M("jzzgoods");
			$id = $_GET['id'];
			if($id){
				$res = $jzzgoods->where(array("gid"=>$id))->delete();
				if($res){
					$this->success("删除成功！",U("Goodsclass/jzzgoods"));
				}
				else{
					$this->error("删除失败！");
				}
			}
			else{
				$this->error("参数错误！");
			}
		}
		/**
		 * 开心兑订单
		 */
		public function jzzorder_list(){
			$jhorder = M("jhorder");
			$member = M("member");
			import("@.ORG.Util.Page");// 导入分页类
			$where = "1 = 1";
			if($_GET['state'] != ''){
				$where .= " and status = ".$_GET['state'];
			}
	    	$count = $jhorder->where($where)->count(); // 查詢滿足要求的總記錄數
	    	$p = new Page($count,20);
			$list = $jhorder->where($where)->order("id desc")->limit($p->firstRow, $p->listRows)->select();
			$show  = $p->show();// 分页显示输出
			$this->assign("list",$list);
			$this->assign("page",$show);
			$this->display();
		}
		/**
		 * 开心兑发货
		 */
		public function jzzorder_deliver(){
			$jhorder = M("jhorder");
			$id = $_GET['id'];
			if($id){
				$info = $jhorder->where(array("id"=>$id))->find();
				if(IS_POST){
					$id = $_POST['id'];
					$data['kuaidiname'] = $_POST['kuaidiname'];
					$data['expressnum'] = $_POST['expressnum'];
					$data['status'] = 2;
					$res = $jhorder->where(array("id"=>$id))->save($data);
					if($res){
						$this->success("发货成功！",U('Goodsclass/jzzorder_list'));
					}
					else{
						$this->error("发货失败！");
					}
					exit;
				}
				$this->assign("info",$info);
			}
			else{
				$this->error("参数错误！");
			}
			$this->display();
		}
	/**
		 * 幸运兑
		 */
		public function xydui(){
			$xyd = M("xyd");
			import("@.ORG.Util.Page");// 导入分页类    	   	
	    	$count = $xyd->where()->count(); // 查詢滿足要求的總記錄數
	    	$p = new Page($count,20);
			$xyd_list=$xyd->where()->limit ( $p->firstRow, $p->listRows )->select();
			
			$show       = $p->show();// 分页显示输出
			$this->assign("list",$xyd_list);
			$this->assign("page",$show);
			$this->display();
		}
		/**
		 * 添加幸运兑商品
		 */
		public function addxyd(){
			$xyd = M("xyd");
			if(IS_POST){
				
				$count=0;
				$data['xyd_name'] = $_POST['xyd_name'];
				$data['xyd_addtime'] = strtotime($_POST['xyd_addtime']);
				$data['xyd_endtime'] = strtotime($_POST['xyd_endtime']);
				$data['xyd_price'] = $_POST['xyd_price'];
				$data['xyd_center'] = $_POST['xyd_center'];
				$dest_folder   = './Public/Uploads/'.date('Ymd',time()).'/';   //上传图片保存的路径
				
				$goodsimg = array();//定义多图存放数组
				
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
	            foreach ($_FILES["xyd_pic"]["error"] as $key => $error){
	                
	                if($error == 0){
	                    $tmp_namey = $_FILES["xyd_pic"]["tmp_name"][$key];
	                    $ay=explode(".",$_FILES["xyd_pic"]["name"][$key]);  //截取文件名跟后缀
	                    $prename = $ay[0];
	                    $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                    $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                    move_uploaded_file($tmp_namey, $uploadfiley);
	                    $goodsimg[$count]=substr($uploadfiley,1);;
	                    $count++;
	                }
	            }
	            $goodsimgs = implode(',',$goodsimg);
	            $data['xyd_pic'] = $goodsimgs;
	            $res = $xyd->add($data);
	            if($res){
	            	$this->success("添加成功！",U("Goodsclass/xydui"));
	            }
	            else{
	            	$this->error("添加失败！");
	            }
				exit;
			}
			$this->display();
		}
		/**
		 * 修改幸运兑商品
		 */
		public function editxyd(){
			$xyd = M("xyd");
			if(IS_POST){
				
				$count=0;
				$data['xyd_name'] = $_POST['xyd_name'];
				$data['xyd_addtime'] = strtotime($_POST['xyd_addtime']);
				$data['xyd_endtime'] = strtotime($_POST['xyd_endtime']);
				$data['xyd_price'] = $_POST['xyd_price'];
				$data['xyd_center'] = $_POST['xyd_center'];
				$dest_folder   = './Public/Uploads/'.date('Ymd',time()).'/';   //上传图片保存的路径
				
				$goodsimg = array();//定义多图存放数组
				$goodsimg=$_POST['xyd_pic'];
				
				if(!file_exists($dest_folder)){
					mkdir($dest_folder,0777); // 创建文件夹，并给予最高权限
					chmod($dest_folder,0777);
				  }
	            foreach ($_FILES["xyd_pic"]["error"] as $key => $error){
	                
	                if($error == 0){
	                    $tmp_namey = $_FILES["xyd_pic"]["tmp_name"][$key];
	                    $ay=explode(".",$_FILES["xyd_pic"]["name"][$key]);  //截取文件名跟后缀
	                    $prename = $ay[0];
	                    $namey = time().mt_rand(1,9).".".$ay[1];;  // 文件的重命名 （日期+随机数+后缀）
	                    $uploadfiley = $dest_folder.$namey;     // 文件的路径
	                    move_uploaded_file($tmp_namey, $uploadfiley);
	                    $goodsimg[$count]=substr($uploadfiley,1);;
	                    $count++;
	                }
	            }
	            $goodsimgs = implode(',',$goodsimg);
	            $data['xyd_pic'] = $goodsimgs;
	            $res = $xyd->where(array("id"=>$_POST['id']))->save($data);
	            if($res){
	            	$this->success("修改成功！",U("Goodsclass/xydui"));
	            }
	            else{
	            	$this->error("修改失败！");
	            }
				exit;
			}
			$id = $_GET['id'];
			if($id){
				$info = $xyd->where(array("id"=>$id))->find();
				$gpic=explode(",",$info['xyd_pic']);
				$this->assign("gpic",$gpic);
				$this->assign("info",$info);
			}
			else{
				$this->error("参数错误！");
				exit;
			}
			$this->display();
		}
		/**
		 * 删除幸运兑商品
		 */
		public function delxyd(){
			$xyd = M("xyd");
			$id = $_GET['id'];
			if($id){
				$res = $xyd->where(array("id"=>$id))->delete();
				if($res){
					$this->success("删除成功！",U("Goodsclass/xydui"));
				}
				else{
					$this->error("删除失败！");
				}
			}
			else{
				$this->error("参数错误！");
			}
		}
		/**
		 * 幸运兑参与人员
		 */
		public function xydcy(){
			$xyd = M("xyd");
			$member = M("member");
			$xydod = M("xydod");
			$id = $_GET['id'];
			if($id){
				$list = $xydod->where(array("xyd_id"=>$id))->select();
				$info = $xyd->where(array("id"=>$id))->find();
				foreach($list as $key=>$val){
					$user = $member->where(array("id"=>$val["user_id"]))->find();
					$list[$key]['user_name'] = $user['truename'];
					$list[$key]['xyd_name'] = $info['xyd_name'];
				}
				$this->assign('list',$list);
				$this->assign('info',$info);
				$this->display();
			}
			else{
				$this->error("参数错误！");
			}
			
		}
		/**
		 * 幸运兑获奖
		 */
		public function xydhuojiang(){
			$xyd = M("xyd");
			$xydod = M("xydod");
			$id = $_GET['id'];
			$xyd_id = $_GET['xyd_id'];
			if($id && $xyd_id){
				$xydinfo = $xyd->where(array("id"=>$xyd_id))->find();
				if($xydinfo['xyd_endtime']>time()){
					$this->error("抱歉，当前活动未结束，无法进行分配获奖操作！");
				}
				else{
					$xydod->where("id = ".$id." and xyd_id = ".$xyd_id)->save(array("state"=>2));
					$xydod->where("id != ".$id." and xyd_id = ".$xyd_id)->save(array("state"=>3));
					$xyd->where(array("id"=>$xyd_id))->save(array("state"=>1));
				}
			}
			else{
				$this->error("参数错误！");
			}
		}
	}
?>