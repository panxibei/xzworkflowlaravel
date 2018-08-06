@extends('home.layouts.homebase')

@section('my_title', "Login - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
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
					<h3 class="panel-title">Sign In</h3>
				</div>
				<div class="panel-body">
					<form id="login_form" role="form" method="post" v-cloak>
						<fieldset>
							<div class="form-group">
								<input ref="ref_username" v-model="username" @keyup.enter="loginsubmit" class="form-control" type="text" placeholder="username" v-bind:autofocus="usernameautofocus" required>
							</div>
							<div class="form-group">
								<input ref="ref_password" v-model="password" @keyup.enter="loginsubmit" class="form-control" type="password" placeholder="password" required>
							</div>
							<div class="form-group">
								<label>
									<input ref="ref_captcha" v-model="captcha" @keyup.enter="loginsubmit" class="form-control" type="text" pattern="[0-9]{4}" placeholder="captcha" style="width:100px;" value=""  autocomplete="off" required>
								</label>&nbsp;
								<!--<img src="{{captcha_src('flatxz')}}" onclick="this.src+=Math.random().toString().substr(-1);" style="cursor:pointer;vertical-align:top;">-->
								<img ref="captcha" src="{{captcha_src('flatxz')}}" @click="captchaclick" style="cursor:pointer;vertical-align:top;">
							</div>
							<div class="checkbox">
								<label>
									<input ref="ref_rememberme" v-model="rememberme" type="checkbox">Remember Me &nbsp;&nbsp;&nbsp;
								</label><a href="#">Forget?</a>
							</div>
							
							<button type="button" class="btn btn-primary" ref="ref_login_submit" @click="loginsubmit">登 录</button>&nbsp;&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-primary" ref="ref_login_reset" @click="loginreset">重 置</button>&nbsp;&nbsp;
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
		captcha: '',
		rememberme: false,
		loginmessage: '',
		usernameautofocus: true
    },
	methods: {
		loginsubmit: function(event){
			var _this = this;

			_this.logindisabled(true);
			_this.loginmessage = '<div class="text-info">Please wait ...</div>';
			
			if (_this.username.length == 0 || _this.password.length == 0 || _this.captcha.length == 0) {
				_this.loginmessage = '<div class="text-warning">Please full the item</div>';
				_this.logindisabled(false);
				return false;
			}

			var url = "{{ route('login.checklogin') }}";
			axios.post(url, {
				name: _this.username,
				password: _this.password,
				captcha: _this.captcha,
				rememberme: _this.rememberme
			})
			.then(function (response) {
				// console.log(response);
				var token = response.data;
				if (token) {
					// alert('success');
					
					_this.password = '**********';
					_this.loginmessage = '<div class="text-success">login success, waiting ....</div>';
					window.setTimeout(function(){
						_this.loginreset;
						var url = "{{ route('admin.config.index') }}";
						window.location.href = url;
					},1000);
				} else {
					// alert('failed');
					_this.loginmessage = '<div class="text-warning">captcha error or login failed</div>';
					_this.logindisabled(false);
				}
			})
			.catch(function (error) {
				// console.log(error);
				_this.loginmessage = '<div class="text-warning">error: failed</div>';
				_this.logindisabled(false);
			})
			_this.captchaclick();
		},
		captchaclick: function(){
			this.$refs.captcha.src+=Math.random().toString().substr(-1);
		},
		loginreset: function(){
			var _this = this;
			_this.username = _this.password = _this.captcha = '',
			_this.rememberme = false
		},
		logindisabled: function (value) {
			var _this = this;
			if (value) {
				_this.$refs.ref_username.disabled = true;
				_this.$refs.ref_password.disabled = true;
				_this.$refs.ref_captcha.disabled = true;
				_this.$refs.ref_rememberme.disabled = true;
				_this.$refs.ref_login_submit.disabled = true;
				_this.$refs.ref_login_reset.disabled = true;
			} else {
				_this.$refs.ref_username.disabled = false;
				_this.$refs.ref_password.disabled = false;
				_this.$refs.ref_captcha.disabled = false;
				_this.$refs.ref_rememberme.disabled = false;
				_this.$refs.ref_login_submit.disabled = false;
				_this.$refs.ref_login_reset.disabled = false;
			}
		}
	}
});
</script>
@endsection