<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>@section('my_title')
{{$config['SITE_TITLE']}}  Ver: {{$config['SITE_VERSION']}}
@show
</title>
<link rel="stylesheet" href="{{ asset('statics/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('statics/daterangepicker/daterangepicker.css') }}">
<!--<link rel="stylesheet" href="{{ asset('css/bootstrap-dialog.min.css') }}">-->
<link rel="stylesheet" href="{{ asset('statics/startbootstrap/metisMenu/metisMenu.min.css') }}">
<link rel="stylesheet" href="{{ asset('statics/startbootstrap/css/sb-admin-2.min.css') }}">
<link rel="stylesheet" href="{{ asset('statics/startbootstrap/font-awesome/css/font-awesome.min.css') }}">
<!--<link rel="stylesheet" href="{{ asset('statics/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">-->
<style type="text/css">
	/* 解决闪烁问题的CSS */
	[v-cloak] {	display: none; }
</style>
@yield('my_style')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<!--<script src="{{ asset('js/jquery.cookie.js') }}"></script>-->
<script src="{{ asset('statics/bootstrap/js/bootstrap.min.js') }}"></script>
<!--<script src="{{ asset('js/jquery.form.min.js') }}"></script>-->
<!--<script src="{{ asset('js/bootstrap-dialog.min.js') }}"></script>-->
<script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('statics/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('statics/startbootstrap/metisMenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('statics/startbootstrap/js/sb-admin-2.min.js') }}"></script>
<!--<script src="{{ asset('statics/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>-->
<script src="{{ asset('js/functions.js') }}"></script>
@yield('my_js')
</head>
<body>
<div id="wrapper">
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
<!-- 头部 -->
@section('my_logo_and_title')
<div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<a class="navbar-brand" href="{:U('Admin/Index/index')}">{{$config['SITE_TITLE']}} 后台管理</a>
</div>
<!-- /.navbar-header -->

<ul class="nav navbar-top-links navbar-right">
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
		</a>
		<ul class="dropdown-menu dropdown-messages">
			<li>
				<a href="#">
					<div>
						<strong>John Smith</strong>
						<span class="pull-right text-muted">
							<em>Yesterday</em>
						</span>
					</div>
					<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<strong>John Smith</strong>
						<span class="pull-right text-muted">
							<em>Yesterday</em>
						</span>
					</div>
					<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<strong>John Smith</strong>
						<span class="pull-right text-muted">
							<em>Yesterday</em>
						</span>
					</div>
					<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a class="text-center" href="#">
					<strong>Read All Messages</strong>
					<i class="fa fa-angle-right"></i>
				</a>
			</li>
		</ul>
		<!-- /.dropdown-messages -->
	</li>
	<!-- /.dropdown -->
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
		</a>
		<ul class="dropdown-menu dropdown-tasks">
			<li>
				<a href="#">
					<div>
						<p>
							<strong>Task 1</strong>
							<span class="pull-right text-muted">40% Complete</span>
						</p>
						<div class="progress progress-striped active">
							<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
								<span class="sr-only">40% Complete (success)</span>
							</div>
						</div>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<p>
							<strong>Task 2</strong>
							<span class="pull-right text-muted">20% Complete</span>
						</p>
						<div class="progress progress-striped active">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
								<span class="sr-only">20% Complete</span>
							</div>
						</div>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<p>
							<strong>Task 3</strong>
							<span class="pull-right text-muted">60% Complete</span>
						</p>
						<div class="progress progress-striped active">
							<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
								<span class="sr-only">60% Complete (warning)</span>
							</div>
						</div>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<p>
							<strong>Task 4</strong>
							<span class="pull-right text-muted">80% Complete</span>
						</p>
						<div class="progress progress-striped active">
							<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
								<span class="sr-only">80% Complete (danger)</span>
							</div>
						</div>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a class="text-center" href="#">
					<strong>See All Tasks</strong>
					<i class="fa fa-angle-right"></i>
				</a>
			</li>
		</ul>
		<!-- /.dropdown-tasks -->
	</li>
	<!-- /.dropdown -->
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
		</a>
		<ul class="dropdown-menu dropdown-alerts">
			<li>
				<a href="#">
					<div>
						<i class="fa fa-comment fa-fw"></i> New Comment
						<span class="pull-right text-muted small">4 minutes ago</span>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<i class="fa fa-twitter fa-fw"></i> 3 New Followers
						<span class="pull-right text-muted small">12 minutes ago</span>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<i class="fa fa-envelope fa-fw"></i> Message Sent
						<span class="pull-right text-muted small">4 minutes ago</span>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<i class="fa fa-tasks fa-fw"></i> New Task
						<span class="pull-right text-muted small">4 minutes ago</span>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="#">
					<div>
						<i class="fa fa-upload fa-fw"></i> Server Rebooted
						<span class="pull-right text-muted small">4 minutes ago</span>
					</div>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a class="text-center" href="#">
					<strong>See All Alerts</strong>
					<i class="fa fa-angle-right"></i>
				</a>
			</li>
		</ul>
		<!-- /.dropdown-alerts -->
	</li>
	<!-- /.dropdown -->
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
		</a>
		<ul class="dropdown-menu dropdown-user">
			<li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
			</li>
			<li><a href="#"><i class="fa fa-user fa-fw"></i> {{ $user['name'] or 'Unknown User'}}</a>
			</li>
			<li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
			</li>
			<li class="divider"></li>
			<li><a href="{:U('Main/Index/main')}"><i class="fa fa-home fa-fw"></i> 前台首页</a>
			</li>
			<li><a href="{{route('admin.logout')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
			</li>
		</ul>
		<!-- /.dropdown-user -->
	</li>
	<!-- /.dropdown -->
</ul>
<!-- /.navbar-top-links -->
@show
<!-- /头部 -->

<!-- 左边菜单 -->
<div class="navbar-default sidebar" role="navigation">
	<div class="sidebar-nav navbar-collapse">
		<ul class="nav" id="side-menu">
			<li class="sidebar-search">
				<div class="input-group custom-search-form">
					<input type="text" class="form-control" placeholder="Search...">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button">
							<i class="fa fa-search"></i>
						</button>
					</span>
				</div>
				<!-- /input-group -->
			</li>
			<li>
				<a href="{:U('Admin/Index/index')}"><i class="fa fa-dashboard fa-fw"></i> Dashboard后台首页</a>
			</li>
			<li>
				<a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="flot.html">Flot Charts</a>
					</li>
					<li>
						<a href="morris.html">Morris.js Charts</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="tables.html"><i class="fa fa-table fa-fw"></i> Tables</a>
			</li>
			<li>
				<a href="{{ route('admin.config.index') }}"><i class="fa fa-gear fa-fw"></i> 系统配置</a>
			</li>
			<li>
				<a href="#"><i class="fa fa-dropbox fa-fw"></i> 元素管理<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="#"><i class="fa fa-magic fa-fw"></i> 基本元素 ...<span class="fa arrow"></span></a>
						<ul class="nav nav-third-level">
							<li>
								<a href="{{ route('admin.field.index') }}"><i class="fa fa-bars fa-fw"></i> Field</a>
							</li>
							<li>
								<a href="{{ route('admin.slot.index') }}"><i class="fa fa-list-alt fa-fw"></i> Slot</a>
							</li>
							<li>
								<a href="{{ route('admin.template.index') }}"><i class="fa fa-file-text-o fa-fw"></i> Template</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-chain fa-fw"></i> 元素关联 ...<span class="fa arrow"></span></a>
						<ul class="nav nav-third-level">
							<li>
								<a href="{:U('Admin/Index/slot2field')}"><i class="fa fa-list-alt fa-fw"></i> Slot2Field</a>
							</li>
							<li>
								<a href="{:U('Admin/Index/template2slot')}"><i class="fa fa-file-text-o fa-fw"></i> Template2Slot</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-chain fa-fw"></i> 用户关联 ...<span class="fa arrow"></span></a>
						<ul class="nav nav-third-level">
							<li>
								<a href="{:U('Admin/Index/mailinglist')}"><i class="fa fa-envelope-o fa-fw"></i> Mailing List</a>
							</li>
							<li>
								<a href="{:U('Admin/Index/slot2user')}"><i class="fa fa-envelope fa-fw"></i> Slot2User</a>
							</li>
							<li>
								<a href="{:U('Admin/Index/user4workflow')}"><i class="fa fa-user-md fa-fw"></i> User4Workflow</a>
							</li>						
						</ul>
					</li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="fa fa-group fa-fw"></i> 权限管理<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="{{ route('admin.user.index') }}"><i class="fa fa-user fa-fw"></i> 用户管理</a>
					</li>
					<li>
						<a href="{{ route('admin.role.index') }}"><i class="fa fa-group fa-fw"></i> 角色管理</a>
					</li>
					<li>
						<a href="{{ route('admin.permission.index') }}"><i class="fa fa-key fa-fw"></i> 权限管理</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="{:U('Admin/Index/index')}"><i class="fa fa-edit fa-fw"></i> 其他管理</a>
			</li>
			<li>
				<a href="forms.html"><i class="fa fa-edit fa-fw"></i> Forms</a>
			</li>
			<li>
				<a href="#"><i class="fa fa-wrench fa-fw"></i> UI Elements<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="panels-wells.html">Panels and Wells</a>
					</li>
					<li>
						<a href="buttons.html">Buttons</a>
					</li>
					<li>
						<a href="notifications.html">Notifications</a>
					</li>
					<li>
						<a href="typography.html">Typography</a>
					</li>
					<li>
						<a href="icons.html"> Icons</a>
					</li>
					<li>
						<a href="grid.html">Grid</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
		</ul>
	</div>
	<!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->
<!-- /左边菜单 -->
</nav>

<!-- 主体 -->
@yield('my_body')
<!-- /主体 -->
<!-- 底部 -->
@section('my_footer')
<div class="text-center">
	<a href="{:U('Home/Index/index')}">{{$config['SITE_TITLE']}}</a>&nbsp;|&nbsp;{{$config['SITE_COPYRIGHT']}}
</div>
<br>
<script src="{{ asset('js/vue.min.js') }}"></script>
<script src="{{ asset('js/uiv.min.js') }}"></script>
<script src="{{ asset('js/axios.min.js') }}"></script>
<script src="{{ asset('js/bluebird.min.js') }}"></script>
@show
<!-- /底部 -->
</div>
</body>
</html>
