<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title><?php echo ($title); ?>——养生链后台管理</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- basic styles -->
		<link rel="stylesheet" href="/cheng/Public/admin/css/bootstrap.min.css" />
		<link rel="stylesheet" href="/cheng/Public/admin/css/font-awesome.min.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="/cheng/Public/admin/css/ace.min.css" />
		<link rel="stylesheet" href="/cheng/Public/admin/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="/cheng/Public/admin/css/ace-skins.min.css" />
		
	</head>

	<body>
		<div class="navbar navbar-default" id="navbar">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">

				</div><!-- /.navbar-header -->

				<div class="navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="/cheng/Public/admin/avatars/user.jpg" alt="Jason's Photo" />
								<span class="user-info">
									<small>欢迎光临,</small>
									<?php echo (session('uname')); ?>
								</span>

								<i class="icon-caret-down"></i>
							</a>

							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="<?php echo U('Admin/Profile/index');?>">
										<i class="icon-cog"></i>
										修改密码
									</a>
								</li>
								<li class="divider"></li>

								<li>
									<a href="<?php echo U('Admin/Login/loginOut');?>">
										<i class="icon-off"></i>
										退出
									</a>
								</li>
							</ul>
						</li>
					</ul><!-- /.ace-nav -->
				</div><!-- /.navbar-header -->
			</div><!-- /.container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>
				
				<!-- 加载左部分 -->
				        <!-- 侧边菜单开始 -->
        <div class="sidebar" id="sidebar">
            <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
            </script>
            <div class="sidebar-shortcuts" id="sidebar-shortcuts" style="height:42px;">
            </div><!-- #sidebar-shortcuts -->

            <ul class="nav nav-list">

                    <li>
                        <a id="indexpage" href="<?php echo U('Admin/Index/index');?>">
                            <i class="icon-dashboard"></i>
                            <span class="menu-text"> 控制台 </span>
                        </a>
                    </li>


               


                
                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-asterisk"></i>
                            <span class="menu-text"> 商品管理 </span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
 								<li>
                                    <a href="<?php echo U('Admin/Goodsclass/index');?>">
                                        <i class="icon-double-angle-right"></i>
                                        商品分类
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Admin/Goodsbrand/index');?>">
                                        <i class="icon-double-angle-right"></i>
                                      品牌
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Admin/Goodsissue/index');?>">
                                        <i class="icon-double-angle-right"></i>
                                        商品发布
                                    </a>
                                </li>

                                <li>
                                    <a href="<?php echo U('Admin/Goodsup/index');?>">
                                        <i class="icon-double-angle-right"></i>
                                        上架商品
                                    </a>
                                </li>

                                <li>
                                    <a href="<?php echo U('Admin/Goodsdown/index');?>">
                                        <i class="icon-double-angle-right"></i>
                                        仓库商品
                                    </a>
                                </li>
                   
                        </ul>
                    </li>

                    <li>
                        <a href="<?php echo U('Admin/Ordermanage/index');?>">
                            <i class="icon-bar-chart"></i>
                            <span class="menu-text">  订单管理 </span>
                        </a>
                    </li>
       
            
            </ul>

            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
            </div>
        </div>

				<!-- 中间部分开始 -->
				
    <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
            <!-- 面包屑导航 -->
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home home-icon"></i>
                    <a href="<?php echo U('Admin/Index/index');?>">首页</a>
                </li>
                <li>
                    <a href="<?php echo U('Admin/Goodsattr/index');?>"><?php echo ($page['title']['prev']); ?></a>
                </li>
                <li class="active">
                    <?php echo ($page['title']['current']); ?>
                </li>
            </ul>
        </div>
        
        <div class="page-content">
            <!-- 页面导航 -->
            <div class="page-header">
                <h1>
                    <?php echo ($page['title']['current']); ?>
                    <a class="btn btn-info btn-sm pull-right" href="<?php echo U('Admin/Goodsattr/index');?>">
                        <i class="icon-reply icon-only"></i>
                    </a>
                </h1>
            </div>
            
            <div class="row" style="margin-top:35px;">
                <div class="col-xs-12">
                    <form class="form-horizontal" role="form" action="<?php echo U('Admin/Goodsattr/insertAttr');?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right"> 选择商品分类 </label>
                            <div class="col-sm-10" id="selparent">
                                <div id="menu-0" class="col-xs-10 col-sm-2  no-padding">
                                    <select class="form-control">
                                        <option value="0">选择商品分类</option>
                                        <?php if(is_array($classdata)): foreach($classdata as $key=>$vo): ?><option value="<?php echo ($vo['cid']); ?>"}><?php echo ($vo['cname']); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input class="col-xs-10 col-sm-5" id="showid" name="cid" value="0" type="hidden">
                            
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right"> 属性名称 </label>
                            <div class="col-sm-10">
                                <input class="col-xs-10 col-sm-5" type="text" placeholder="请填写属性名称" name="attrname">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right"> 排序 </label>
                            <div class="col-sm-10">
                                <input class="col-xs-10 col-sm-5" type="text" name="attrsort" value="0">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right"> 属性值 </label>
                            <div class="col-sm-10" id="attrlist">
                                <input class="col-xs-10 col-sm-5" type="text" placeholder="请填写属性值" name="attrval[]"><a class="remove" href="#"><i class="icon-remove"></i></a><br /><br />
                                <input class="col-xs-10 col-sm-5" type="text" placeholder="请填写属性值" name="attrval[]"><a class="remove" href="#"><i class="icon-remove"></i></a><br /><br />
                                <input class="col-xs-10 col-sm-5" type="text" placeholder="请填写属性值" name="attrval[]"><a class="remove" href="#"><i class="icon-remove"></i></a><br /><br />
                                <input class="col-xs-10 col-sm-5" type="text" placeholder="请填写属性值" name="attrval[]"><a class="remove" href="#"><i class="icon-remove"></i></a><br /><br />
                                <button id="addattr" type="button" class="btn btn-sm btn-info">+增加更多属性</button>
                            </div>
                        </div>
                        
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-2 col-md-10">
                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    提交
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <button class="btn" type="reset">
                                    <i class="icon-undo bigger-110"></i>
                                    重置
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

			
			</div>
			<!-- 返回顶部 -->
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- //加载公共js -->
		<script type="text/javascript">
		window.jQuery || document.write("<script src='/cheng/Public/admin/js/jquery-2.0.3.min.js'>"+"<"+"script>");
		</script>
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='/cheng/Public/admin/js/jquery.mobile.custom.min.js'>"+"<"+"script>");
		</script>
		<script src="/cheng/Public/admin/js/bootstrap.min.js"></script>
		<script src="/cheng/Public/admin/js/ace-elements.min.js"></script>
		<script src="/cheng/Public/admin/js/ace.min.js"></script>
		<script src="/cheng/Public/admin/js/ace-extra.min.js"></script>
		
    <script type="text/javascript">
        $(function(){
            //设置当前页面的菜单高亮显示 开始
            var nownav = '#sidebar a[href*=Goodsattr]';
            var parentattr = $(nownav).parent().parent().attr("class");
            $(nownav).parent().addClass('active');
            if (parentattr == 'submenu') {
                $(nownav).parent().parent().parent().addClass('active open');
            };
            //设置当前页面的菜单高亮显示 结束

            //联动菜单
            $('#selparent').on('change','select',function(){
                var parentid = 0;
                var number = $(this).parent().prop('id').split('-')[1];
                numbers = parseInt(number)+1;
                var prevval = $(this).parent().prev().children().eq(0).val();
                $(this).parent().nextAll().remove();
                parentid = $(this).val();

                if ($(this).val() == prevval) {
                    $('#showid').val(parentid);
                    return;
                };

                if (parentid == 0) {
                    $('#showid').val(parentid);
                    return;
                };
                var str = '<div id="menu-'+numbers+'" class="col-xs-10 col-sm-2  no-padding"><select class="form-control"></select></div>';
                $(this).parent().after(str);
                $.ajax({
                    url:"/cheng/admin.php/Admin/Goodsclass/viewClassId",
                    type:'post',
                    data:{parentid:parentid},
                    success:function(data){
                    var datas = JSON.parse(data);
                    printstr = '<option value="'+parentid+'">--</option>';
                    for (var i=0; i< datas.length; i++) {
                    printstr += '<option value="'+datas[i].cid+'">'+datas[i].cname+'</option>';
                    }
                    $('#menu-'+numbers+' select').html(printstr);
                    }
                })
                $('#showid').val(parentid);
            })

            //添加按钮
            $('#addattr').on('click',function(){
                $(this).before('<input class="col-xs-10 col-sm-5" type="text" name="attrval[]"><a class="remove" href="#"><i class="icon-remove"></i></a><br /><br />');
            });

            //删除按钮
            $('div').on('click','a[class=remove]',function(){
            $(this).prev().remove();
            $(this).next().remove();
            $(this).next().remove();
            $(this).remove();
            });
        });
    </script>

	</body>
</html>