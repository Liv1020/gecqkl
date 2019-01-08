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
		
    <style>
        .sel{
            height:auto;
            overflow: hidden;
            border:1px solid #CCC;
            padding:0px;
            margin-bottom: 5px;
        }
        .sel-heading{
            background: #EEE;
            height:35px;
            margin: 0px;
            color: #2c79a6;
        }
        .sel label{
            height: 30px;
            line-height: 35px;
            padding-left: 10px;
            float: left;
            margin-right: 10px;
        }
        .sel-input input{
            border:1px solid bule;
        }
        .sel-title{
            display: block;
            height:35px;
            line-height: 35px;
            margin: 0px;
            font-size: 16px;
        }
        .sel-body{
            padding:20px;
            height:auto;
            overflow: hidden;
        }
        .childsel{
            float: left;
            margin: 5px;
        }
    </style>

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
                    <a href="<?php echo U('Admin/Goodsbrand/index');?>"><?php echo ($page['title']['prev']); ?></a>
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
                    <a class="btn btn-info btn-sm pull-right" href="<?php echo U('Admin/Goodsbrand/index');?>">
                        <i class="icon-reply icon-only"></i>
                    </a>
                </h1>
            </div>

            <div class="row" style="margin-top:35px;">
                <div class="col-xs-12">
                    <form class="form-horizontal" role="form" action="<?php echo U('Admin/Goodsbrand/insertBrand');?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right"> 品牌名称 </label>
                            <div class="col-sm-10">
                                <input class="col-xs-10 col-sm-5" type="text" placeholder="请填写品牌名称" name="bname">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0px;">
                            <label class="col-sm-2 control-label no-padding-right"> 上传品牌标识 </label>
                            <div class="col-sm-10">
                                <div class="ace-file-input no-padding col-xs-10 col-sm-5">
                                    <input style="height:30px;" type="file" id="id-input-file-2" name="brandpic" />
                                </div>
                            </div>
                        </div>
                        <!-- 选择品牌 -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right"> 选择拥有产品 </label>
                            <div class="col-sm-10" id="selparent">
                                <div id="accordion" class="col-xs-10 col-sm-12 no-padding">
                                    <div class="sel-group" id="selclass">
                                        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><div class="sel">
                                                <label>
                                                    <input type="checkbox" id="checkAll-<?php echo ($i); ?>" class="ace ace-checkbox-2"/>
                                                    <span class="lbl">全选</span>
                                                </label>
                                                <div class="sel-heading" data-toggle="collapse" data-toggle="collapse" data-parent="#selclass" data-target="#sel-<?php echo ($i); ?>">
                                                    <span class="sel-title">
                                                        <?php echo ($val['cname']); ?>
                                                    </span>
                                                </div>
                                                <div id="sel-<?php echo ($i); ?>" class="sel-collapse collapse">
                                                    <div class="sel-body" id="check-<?php echo ($i); ?>">
                                                        <?php if(is_array($childs[$i-1])): foreach($childs[$i-1] as $key=>$vo): if($vo['isbottom'] == 1): ?><div class="childsel">
                                                                    <input type="checkbox" class="ace ace-checkbox-2" name="team[]" value="<?php echo ($vo['cid']); ?>" />
                                                                    <span class="lbl"><?php echo ($vo['cname']); ?></span>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="childsel" style="margin-left:10px;">
                                                                    <span class="lbl" style="color:#05A;"><?php echo ($vo['cname']); ?>：</span>
                                                                </div><?php endif; endforeach; endif; ?>
                                                    </div>
                                                </div>
                                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 选择品牌结束 -->

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
            var nownav = '#sidebar a[href*=Goodsbrand]';
            var parentattr = $(nownav).parent().parent().attr("class");
            $(nownav).parent().addClass('active');
            if (parentattr == 'submenu') {
                $(nownav).parent().parent().parent().addClass('active open');
            };
            //设置当前页面的菜单高亮显示 结束

            //默认打开选项
            $('#selclass').children().eq(0).children().eq(2).addClass('in');

            //全选 
            $("[id^=checkAll-]:checkbox").on('click',function(){
                var num = $(this).prop('id').split('-')[1];
                var divs = "#check-"+num;
                $(divs+" :checkbox").prop("checked",this.checked);
            });
           
            $('[name^=team]:checkbox').click(function(){
                var num = $(this).parent().parent().prop('id').split('-')[1];
                var $tmp = $('#check-'+num+' :checkbox');
                $("#checkAll-"+num).prop('checked',$tmp.length==$tmp.filter(':checked').length);
            });

            $('#id-input-file-2').ace_file_input({
                no_file:'选择广告图片 ...',
                btn_choose:'上传',
                btn_change:'修改',
                droppable:false,
                onchange:null,
                thumbnail:false //| true | large
                //whitelist:'gif|png|jpg|jpeg'
                //blacklist:'exe|php'
                //onchange:''
                //
            }); 
        
            //文件上传
            $('#id-file-format').removeAttr('checked').on('change', function() {
                var before_change
                var btn_choose
                var no_icon
                if(this.checked) {
                    btn_choose = "Drop images here or click to choose";
                    no_icon = "icon-picture";
                    before_change = function(files, dropped) {
                        var allowed_files = [];
                        for(var i = 0 ; i < files.length; i++) {
                            var file = files[i];
                            if(typeof file === "string") {
                                //IE8 and browsers that don't support File Object
                                if(! (/\.(jpe?g|png|gif|bmp)$/i).test(file) ) return false;
                            }
                            else {
                                var type = $.trim(file.type);
                                if( ( type.length > 0 && ! (/^image\/(jpe?g|png|gif|bmp)$/i).test(type) )
                                        || ( type.length == 0 && ! (/\.(jpe?g|png|gif|bmp)$/i).test(file.name) )//for android's default browser which gives an empty string for file.type
                                    ) continue;//not an image so don't keep this file
                            }
                            
                            allowed_files.push(file);
                        }
                        if(allowed_files.length == 0) return false;
        
                        return allowed_files;
                    }
                }
                else {
                    btn_choose = "Drop files here or click to choose";
                    no_icon = "icon-cloud-upload";
                    before_change = function(files, dropped) {
                        return files;
                    }
                }
                var file_input = $('#id-input-file-3');
                file_input.ace_file_input('update_settings', {'before_change':before_change, 'btn_choose': btn_choose, 'no_icon':no_icon})
                file_input.ace_file_input('reset_input');
            });

            $('.date-picker').datepicker({autoclose:true}).prev().on(ace.click_event, function(){
                $(this).next().focus();
            });
        });
    </script>

	</body>
</html>