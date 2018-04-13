<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	@yield('my_title')
	<link rel="stylesheet" href="{{ asset('statics/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('statics/startbootstrap/metisMenu/metisMenu.min.css') }}">
	<link rel="stylesheet" href="{{ asset('statics/startbootstrap/css/sb-admin-2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('statics/startbootstrap/font-awesome/css/font-awesome.min.css') }}">
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/jquery.cookie.js') }}"></script>
	<script src="{{ asset('js/jquery.form.min.js') }}"></script>
	<script src="{{ asset('statics/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap-dialog.min.js') }}"></script>
	<script src="{{ asset('statics/startbootstrap/metisMenu/metisMenu.min.js') }}"></script>
	<script src="{{ asset('statics/startbootstrap/js/sb-admin-2.min.js') }}"></script>
	@yield('my_js')
</head>
<body>
	<!-- 头部 -->
	@section('my_logo_and_title')
	<div class="header">
		<div class="text-center">
			<h2>{$Think.config.site_title}
			<small>{$Think.config.site_version}</small></h2>
		</div>
	</div>
	@show
	<hr>
	<!-- /头部 -->

	<!-- 主体 -->
	@yield('my_body')
	<!-- /主体 -->

	<!-- 底部 -->
	@section('my_footer')
	<div class="text-center">
		<hr>
		<small>
			<a href="{:U('Home/Index/index')}">{$Think.config.site_system_name}</a>&nbsp;|&nbsp;{$Think.config.site_copyright}
		</small>
	</div>
	@show
	<!-- /底部 -->
</body>
</html>