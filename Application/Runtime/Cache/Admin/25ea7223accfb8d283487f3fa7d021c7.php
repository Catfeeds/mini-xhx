<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">

    <title>小浣熊后台系统</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link href="/Themes/Admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Themes/Admin/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Morris -->
    <link href="/Themes/Admin/css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="/Themes/Admin/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

    <link href="/Themes/Admin/css/animate.css" rel="stylesheet">
    <link href="/Themes/Admin/css/style.css" rel="stylesheet">
    <link href="/Themes/Admin/css/page.css" rel="stylesheet">

    <!-- layer -->
    <link href="/Themes/Admin/js/plugins/layer/skin/layer.css" rel="stylesheet">
	<script src="/Themes/Admin/js/jquery-2.1.1.min.js"></script>

</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">

                        <div class="dropdown profile-element"> 
                        	<span></span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="oneline m-t-xs"> <strong class="font-bold"><?php echo ($adminname); ?></strong>
                             </span>  <span class="text-muted text-xs oneline">管理员 <b class="caret"></b></span> </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="<?php echo U('Admin/Login/editpws');?>">修改密码</a>
                                </li>
                                <li><a href="<?php echo U('Admin/Login/logout');?>">安全退出</a>
                                </li>
                            </ul>
                        </div>
                        <div class="logo-element">
                        </div>
                    </li>
					<?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Index/',session('admin_id')) == false){ }else{ ?>
					<li>
                        <a href="<?php echo U('Index/index');?>"><i class="fa fa-home"></i> <span class="nav-label">首页</span></a>
						
                    </li>
					<?php }?>
					<?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/User/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">会员管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/User/index',session('admin_id')) == true){ ?>
                                <li>
                                    <a href="<?php echo U('User/index');?>">会员列表</a>
                                </li>
                            <?php
 } ?>
                     </ul>
                    </li>
                    <?php
 } ?>
                    <?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Carousel/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">轮播图片管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Carousel/index',session('admin_id')) == true){ ?>
                            <li>
                                <a href="<?php echo U('Carousel/index');?>">轮播图片列表</a>
                            </li>
                            <?php
 } ?>
                        </ul>
                    </li>
                    <?php
 } ?>
					<?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Child/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">育儿百科</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Child/index',session('admin_id')) == true){ ?>
                                <li>
                                    <a href="<?php echo U('Child/index',array('cid'=>1));?>">护理</a>
                                    <a href="<?php echo U('Child/index',array('cid'=>2));?>">早教</a>
                                    <a href="<?php echo U('Child/index',array('cid'=>3));?>">营养</a>
                                    <a href="<?php echo U('Child/index',array('cid'=>4));?>">辣妈</a>
                                </li>
                            <?php
 } ?>
                     </ul>
                    </li>
                    <?php
 } ?>
					<?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Mr/',session('admin_id')) == true){ ?>
					<li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">萌娃启智</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Mr/index',session('admin_id')) == true){ ?>
                                <li>
                                    <a href="<?php echo U('Mr/edit',array('id'=>14));?>">宝宝巴士</a>
                                </li>
                            <?php
 } ?>
                     </ul>
                    </li>
                    <?php
 } ?>
                    <?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Comment/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">评论管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Comment/index',session('admin_id')) == true){ ?>
                            <li>
                                <a href="<?php echo U('Comment/index');?>">评论列表</a>
                            </li>
                            <?php
 } ?>
                        </ul>
                    </li>
                    <?php
 } ?>
					<?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Goods/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">商品管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Goods/index',session('admin_id')) == true){ ?>
                                <li>
                                    <a href="<?php echo U('Goods/index');?>">商品列表</a>
                                </li>
                            <?php
 } ?>
                     </ul>
                    </li>
                    <?php
 } ?>
					<?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Orders/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">订单管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Orders/index',session('admin_id')) == true){ ?>
                                <li>
                                    <a href="<?php echo U('Orders/index');?>">订单列表</a>
                                </li>
                            <?php
 } ?>
                     </ul>
                    </li>
                    <?php
 } ?>
                    <?php
 $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Cache/',session('admin_id')) == true){ ?>
                    <li>
                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">配置管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php
 if($auth->check(MODULE_NAME . '/Cache/set',session('admin_id')) == true){ ?>
                                <li>
                                    <a href="<?php echo U('Cache/set');?>">更新配置</a>
                                </li>
                            <?php
 } ?>
                     </ul>
                    </li>
                    <?php
 } ?>
					<?php  $auth = new \Think\Auth; if($auth->check(MODULE_NAME . '/Root/',session('admin_id')) == false){ }else{ ?>
						<li>
	                        <a href="#"><i class="fa fa-globe"></i> <span class="nav-label">账户管理</span><span class="fa arrow"></span></a>
	                        <ul class="nav nav-second-level">
								<?php  if($auth->check(MODULE_NAME . '/Root/userlist',session('admin_id')) == false){ }else{ ?>
								<li>
									<a href="<?php echo U('Root/userlist');?>">账户列表</a>
	                            </li>
								<?php  } if($auth->check(MODULE_NAME . '/Root/grouplist',session('admin_id')) == false){ }else{ ?>
	                            <li>
	                            	<a href="<?php echo U('Root/grouplist');?>">账户组列表</a>
	                            </li>
								<?php  } if($auth->check(MODULE_NAME . '/Root/rulelist',session('admin_id')) == false){ }else{ ?>
								<li>
									<a href="<?php echo U('Root/rulelist');?>">权限列表</a>
	                            </li>
								<?php  } ?>
	                        </ul>
	                    </li>
					<?php  } ?>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message"><a href="<?php echo U('Index/index');?>" title="返回首页"><i class="fa fa-home"></i></a>欢迎使用后台管理系统</span>
                        </li>
                        <li>
                            <a href="<?php echo U('Login/index');?>">
                                <i class="fa fa-sign-out"></i> 退出
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>
	<style>
	.mar_left{
	margin-left: 8px;
	}
	</style>
            <div class="gray-bg dashbard-1">
                <div class="row">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <ol class="breadcrumb">
                                    <li><a href="#">主页</a></li>
									<li>订单管理</li>
                                    <li><strong>订单列表</strong></li>
                                </ol>
                            </div>
                            <div class="ibox-content">
                            	 <form action="/admin.php/Orders/index"  method="get" id="orderform" class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">订单状态</label>
                                        <div class="col-sm-2">
                                            <select id="agency_id" class="form-control m-b" name="status">
												<option value="">请选择</option>
												<option value="1">成功</option>
												<option value="2">等待发货</option>
												<option value="3">已发货</option>
											</select>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i> 搜索</button>
                                        </div>
                                    </div>
                                </form>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>名称</th>
                                            <th>图片</th>
											<th>已付草莓数量</th>
											<!--<th>已付水蜜桃数量</th>-->
                                            <th>购买数量</th>
											<th>联系人</th>
											<th>联系电话</th>
											<th>地址</th>
											<th>状态</th>
                                            <th>买家留言</th>
                                            <th>创建时间</th>
											<th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr class="gradeX">
                                            <td><?php echo ($list["sn"]); ?></td>
                                            <td><?php echo ($list["g_name"]); ?></td>
                                            <td><?php if($list["g_img"] != ''): ?><img src="<?php echo ($list["g_img"]); ?>" width="60"/><?php endif; ?></td>
                                            <td><?php echo ($list["totalstrawberry"]); ?></td>
                                            <!--<td><?php echo ($list["totalpeach"]); ?></td>-->
                                            <td><?php echo ($list["num"]); ?></td>
                                            <td><?php echo ($list["linkname"]); ?></td>
                                            <td><?php echo ($list["linktel"]); ?></td>
                                            <td><?php echo ($list["linkaddress"]); ?></td>
											<td>
												<?php if($list["status"] == 1): ?>完成
												<?php elseif($list["status"] == 2): ?>
													等待发货
												<?php else: ?>
													已经发货<?php endif; ?>
											</td>
                                            <td><?php echo ($list["message"]); ?></td>
                                            <td><?php echo (date("Y-m-d H:i:s",$list["create_time"])); ?></td>
											 <td>
												<a href="<?php echo U('Orders/add',array('id'=>$list['id']));?>" class="red mar_left">编辑 </a>
												<a href="<?php echo U('Orders/del',array('id'=>$list['id']));?>" class="red mar_left">删除 </a><br/>
                                                 <a href="javascript:del(<?php echo ($list["id"]); ?>)" class="red mar_left">删除</a><br/>
												<a href="<?php echo U('user/index',array('openid'=>$list['openid']));?>" class="red mar_left">查看下单的微信用户 </a>
											</td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?> 
										<tr>
											<td colspan="14"><div class="pages"><?php echo ($page); ?></div></td>
										</tr>  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
<script>
    function del(aa){
        if(confirm("确定要删除数据吗")){
            location.href="/admin.php/Orders/del?id="+aa;
        }
    }
</script>
		</div>	
	</div>
<!-- Mainly scripts -->
   
    <script src="/Themes/Admin/js/bootstrap.min.js?v=3.4.0"></script>
    <script src="/Themes/Admin/js/plugins/metisMenu/jquery.metisMenu.js"></script><!---->
    <script src="/Themes/Admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/Themes/Admin/js/plugins/jeditable/jquery.jeditable.js"></script>

    <!-- Data Tables -->
    <script src="/Themes/Admin/js/plugins/dataTables/jquery.dataTables.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="/Themes/Admin/js/hplus.js?v=2.2.0"></script>
    <script src="/Themes/Admin/js/plugins/pace/pace.min.js"></script>

    <!-- layerDate plugin javascript -->
    <script src="/Themes/Admin/js/plugins/layer/laydate/laydate.js"></script>
    <script>

         //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY/MM/DD hh:mm:ss',
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY/MM/DD hh:mm:ss',
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
    </script>  

    <!-- 自定义js 
    <script src="/Themes/Admin/js/content.min.js?v=1.0.0"></script>-->

    <!--  Nestable List  -->
    <script src="/Themes/Admin/js/plugins/nestable/jquery.nestable.js"></script>
    <script>//列表
        $(document).ready(function () {
			
			$('.dataTables-example').dataTable({"ordering": false});//
			
			var oTable = $('#editable').dataTable();
			
           /* var updateOutput = function (e) {
                var list = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
                } else {
                    output.val('浏览器不支持');
                }
            };
            // activate Nestable for list 1
            $('#nestable').nestable({
                group: 1
            }).on('change', updateOutput);

            // activate Nestable for list 2
            $('#nestable2').nestable({
                group: 1
            }).on('change', updateOutput);

            // output initial serialised data
            updateOutput($('#nestable').data('output', $('#nestable-output')));
            updateOutput($('#nestable2').data('output', $('#nestable2-output')));

            $('#nestable-menu').on('click', function (e) {
                var target = $(e.target),
                    action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });*/
			

        });
    </script>

    <!-- 点击切换-->
    <script>
	function ordertype(ord){
		type = $(ord).val();
		if(type == 1){
			location.href = "<?php echo U('Shangcheng/orders',array('token'=>$token));?>";
			
		}else{
			location.href = "<?php echo U('Waimai/orders',array('token'=>$token));?>";
		}
	}
    </script>
	<!-- ECharts -->
    <script src="/Themes/Admin/js/plugins/echarts/echarts-all.js"></script>
</body>
</html>