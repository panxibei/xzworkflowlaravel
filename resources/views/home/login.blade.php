@extends('home.layouts.homebase')

@section('my_title', "Login - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
/*
$(document).ready(function(){
	$(function(){
		//如果记住我，就直接登录
		if($.cookie('cookie_mt_rememberme')=='1'){
			//alert($.cookie('cookie_mt_rememberme'));
			BootstrapDialog.show({
				size: BootstrapDialog.SIZE_SMALL,
				closable: false,
				closeByBackdrop: false,
				closeByKeyboard: false,
				title: "{$Think.config.site_title}",
				message:'请稍后，正在登录 ....',
				onshow: function(dialogRef){
					dialogRef.getModalContent().css('margin-top',function(){
						var modalHeight = dialogRef.getModalContent().height();
						return ((window.screen.height / 2) - (modalHeight / 2) - 200);
					});
				},
				onshown: function(dialogRef){
					setTimeout(function(){
					
				var url = "{:U('Home/Index/check_login_for_cookies')}";
				$.post(url,{user_token:$.cookie('cookie_mt_user_token')},function(jdata){
					//alert(jdata.ajax_msg);
				});
					
						dialogRef.close();
					}, 2000);
				},
				onhidden: function(dialogRef){
					window.location.replace("{:U('Main/Index/circulation')}");
				}
			});
		}
	});


	$('#form_login').on('submit', function(e) {
		$("#login").attr({"disabled":"disabled"});
		e.preventDefault();
		var user_username=$("#username").val();
		var user_password=$("#password").val();
		var user_verifycode=$("#verify_code").val();
		var user_rememberme=$("#rememberme").prop("checked")?1:0;
		if(user_password==""||user_username==""){
			alert('登录名与密码不能为空！');
			$("#user_username").focus();
			setTimeout(function(){$("#login").removeAttr("disabled");},1000);
			return false;
		} else {
			var url = "{:U('Home/Index/check_login')}";
			$.post(url,{user_username:user_username,user_password:user_password,user_verifycode:user_verifycode,user_rememberme:user_rememberme},function(jdata){
				if(jdata.ajax_status == 0) { //登录成功
					//alert('登录成功，正在转向后台主页！');
					$('#username').val('');
					$('#password').val('');
					$('#verify_code').val('');
					//$('div#verify_is_ok').html('登录成功！！正在跳转。。。');
					$('div#verify_is_ok').html(jdata.ajax_msg);
					setTimeout(function(){window.location.href = "{:U('Main/Index/circulation')}";$('div#verify_is_ok').html('&nbsp;');$('#login').removeAttr('disabled');},1000);
				} else {
					//$('span#verify_is_ok').html('登录失败！！');
					$('div#verify_is_ok').html(jdata.ajax_msg);
					$('#verify_image').click();
					
					if (jdata.ajax_status == 1) { //帐号密码错误
						$('#password').val('').focus();
					} else {
						if (jdata.ajax_status == 2) { //帐号锁定
							$('#password').val('').focus();
						} else {
							if (jdata.ajax_status == 3) {
								$('#password').val('').focus();
								<!-- $('div#verify_is_ok').html('域用户信息验证错误！！'); -->
							} else {
								if (jdata.ajax_status == 4) { //验证码错误
									//$("#verify_image").click();
									$('#verify_code').val('').focus();
								} else {
									if (jdata.ajax_status == 5) { //系统故障，可能数据库连接异常
										$('#password').val('').focus();
										//$('div#verify_is_ok').html('系统故障！！');
									} else { //jdata.status == 2 用户名或密码错误
										$('#password').val('').focus();
										$('div#verify_is_ok').html('未知错误！！');
									}
								}
							}
						}
					}
					setTimeout(function(){$('#login').removeAttr('disabled');},1000);
				}
			});
		}
	});
	//表单重置
	$('#input_reset').click(function(){
		$('#form_login').resetForm();
	});
});
*/
</script>
@endsection

@section('my_logo_and_title')
@parent
@endsection


@section('my_body')
@parent
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="login-panel panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Please Sign In</h3>
				</div>
				<div class="panel-body">
					<form id="login_form" role="form" method="post">
						<fieldset>
							<div class="form-group">
								<input v-model="username" class="form-control" type="text" placeholder="username" autofocus required>
							</div>
							<div class="form-group">
								<input v-model="password" class="form-control" type="password" placeholder="password" required>
							</div>
							<div class="form-group">
								<label>
									<input v-model="verifycode" class="form-control" type="text" pattern="[0-9]{4}" title="请输入4位验证码" style="width:100px;" value="8888"  autocomplete="off" required>
								</label>
								<img id="verify_image" src="" onclick="this.src+='?rand='+Math.random();" style="cursor:pointer;vertical-align:top;">
							</div>
							<div class="checkbox">
								<label>
									<input v-model="rememberme" type="checkbox">Remember Me &nbsp;&nbsp;&nbsp;
								</label><a href="">Forget?</a>
							</div>
							
							<button type="button" class="btn btn-primary" id="login_submit" @click="loginsubmit">登 录</button>&nbsp;&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-primary" id="login_reset" @click="loginreset">重 置</button>&nbsp;&nbsp;
							<div v-html="loginmessage">@{{ loginmessage }}</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_login = new Vue({
    el: '#login_form',
    data: {
		username: '',
		password: '',
		verifycode: '',
		rememberme: false,
		loginmessage: ''
    },
	methods: {
		'loginsubmit': function(event){
			// alert(event.target.id);
			// alert(event.target.value);
			
			var _this = this;
			var url = "{{ route('login.checklogin') }}";
			axios.post(url, {
					username: _this.username,
					password: _this.password,
					verifycode: _this.verifycode,
					rememberme: _this.rememberme
				})
				.then(function (response) {
					// alert(response.data);
					_this.loginreset();
					var token = response.data;
					if (token) {
						// alert('success');
						_this.loginmessage = '<div class="text-success">login success, waiting ....</div>';
						// return false;
						window.setTimeout(function(){
							
							var url = "{{ route('admin.config.index') }}";
							window.location.href = url + "?token=" + token;
							// $('div#verify_is_ok').html('&nbsp;');$('#login').removeAttr('disabled');
						},1000);
					} else {
						// alert('failed');
						_this.loginmessage = '<div class="text-warning">login failed</div>';
					}
				})
				.catch(function (error) {
					// alert('failed');
					// console.log(error);
					_this.loginreset();
					_this.loginmessage = '<div class="text-warning">error: failed</div>';
				})
		},
		'loginreset': function(){
			var _this = this;
			_this.username = '',
			_this.password = '',
			_this.verifycode = '',
			_this.rememberme = false
		}
	}
});
</script>
@endsection