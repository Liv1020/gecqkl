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
                <li class="active"><?php echo ($title); ?></li>
            </ul>
        </div>

        <div class="page-content">
            <!-- 页面导航 -->
            <div class="page-header">
                <h1>
                    <?php echo ($title); ?>
                    <small>
                        <i class="icon-double-angle-right"></i>
                         查看
                    </small>
                </h1>
            </div>

            <!-- 添加按钮 -->
            <div class="row" style="padding:0px;height:auto;overflow:hidden;margin-bottom:10px;">
                <div class="col-xs-12 col-sm-8">
                    <a class="btn btn-primary pull-left" style="padding:1px 10px;" href="<?php echo U('Admin/Goodsattr/addAttr');?>">
                        <i class="icon-plus-sign bigger-125"></i>
                        添加属性
                    </a>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <form action="" method="get">
                        <div class="input-group pull-right">
                            <input class="form-control search-query" type="text" value="<?php echo ($_GET['searchval']); ?>" name="searchval" placeholder="输入搜索内容...">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="submit">
                                    Search
                                    <i class="icon-search icon-on-right bigger-110"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="sample-table-2" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>排序</th>
                                    <th class="width-15">属性名称</th>
                                    <th class="width-15">所属分类</th>
                                    <th class="width-45">属性值</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <!--一行的开始-->
                            <!--遍历查询到的数据-->
                            <tbody>
                                <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
                                    <td><?php echo ($vo['attrid']); ?></td>
                                    <td><?php echo ($vo['attrsort']); ?></td>
                                    <td><?php echo ($vo['attrname']); ?></td>
                                    <td><?php echo ($vo['cname']); ?></td>
                                    <td><?php echo ($vo['attrvalue']); ?></td>
                                    <td>
                                        <div class="visible-md visible-lg visible-sm visible-xs action-buttons">
                                            <a name="table-edit" class="green" href="<?php echo U('Admin/Goodsattr/modifyAttr',array('id'=>$vo['attrid']));?>">
                                                <i class="icon-pencil bigger-130"></i>
                                            </a>

                                            <a name="del_<?php echo ($vo['attrid']); ?>" class="red" data-toggle="modal" href="#deleteModal">
                                                <i class="icon-trash bigger-130"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <!--遍历结束--><?php endforeach; endif; ?>

                                <tr style="padding:0px;">
                                    <td colspan=7><?php echo ($page); ?></td>
                                </tr>
                                <!--一行的结束--> 
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->
    <!-- 中间部分结束 -->
    
    <!-- 删除按钮弹出层 -->
    <div class="modal modal-small fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:300px;">
            <div class="modal-content" style="top:160px;left:80%;">
                <div class="modal-header" style="height:40px;padding:5px 10px;line-height:30px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <p class="bigger-120" id="myModalLabel">删除数据</p>
                </div>
                <div class="modal-body" style="height:70px;padding:5px 10px;line-height:60px;">
                    <p class="text-danger bigger-150">确定删除数据！</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" id="delete-data">删除</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div id="success-info" class="alert alert-success fade in col-xs-4 hide" style="position:fixed;top:20px;left:35%;z-index:1000;">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
        <strong></strong>
    </div>
    <div id="error-info" class="alert alert-danger fade in col-xs-4 hide" style="position:fixed;top:20px;left:35%;z-index:1000;">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
        <strong></strong>
    </div>
    <!-- 删除按钮弹出层结束 -->

			
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
        jQuery(function($) {
            //设置当前页面的菜单高亮显示 开始
            var nownav = "#sidebar a[href*=Goodsattr]";
            var parentattr = $(nownav).parent().parent().attr("class");
            $(nownav).parent().addClass('active');
            if (parentattr == 'submenu') {
                $(nownav).parent().parent().parent().addClass('active open');
            };
            //设置当前页面的菜单高亮显示 结束

            //全选js
            $('table th input:checkbox').on('click' , function(){
                var that = this;
                $(this).closest('table').find('tr > td:first-child input:checkbox')
                .each(function(){
                    this.checked = that.checked;
                    $(this).closest('tr').toggleClass('selected');
                });
                    
            });
            
            var delid;
            var delline;
            $('a[name^=del]').on('click',function(){
                delid = ($(this).attr('name').split('_'))[1];
                delline = $(this).parent().parent().parent();
            });
           
            $('#delete-data').on('click',function(){
                $.ajax({
                       url:"/cheng/admin.php/Admin/Goodsattr/deleteAttr",
                       type:"post",
                       data:{attrid:delid},
                       success:function(data){
                            if (data == 1) {
                                warningInfo("#success-info","删除成功！");
                                location.href='';
                            }else if(data == 2){
                                warningInfo("#error-info","没有删除权限！");
                            }else{
                                warningInfo("#error-info","删除失败！");
                            }
                            $('#deleteModal').modal('hide');
                       }
                });
            });
           
            //信息提示函数
            function warningInfo(id,info){
                $(id).removeClass('hide');
                $(id).fadeOut(0);
                $(id).children().eq(1).html(info);
                $(id).fadeToggle(0).delay(1000).fadeToggle(1000);
            }
        });
    </script>

	</body>
</html>