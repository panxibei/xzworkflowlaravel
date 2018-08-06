@extends('home.layouts.homebase')

@section('my_title')
Login - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_logo_and_title')
@parent
@endsection


@section('my_body')
@parent

<br><br><br>

<i-row :gutter="16">
	<i-col span="10">
		&nbsp;
	</i-col>
	<i-col span="4">
	
		<i-form ref="formInline" :model="formInline" :rules="ruleInline" @submit.native.prevent>
			<Form-item prop="username">
				<i-input ref="ref_username" type="text" v-model="formInline.username" @on-enter="handleSubmit('formInline')" placeholder="Username">
					<Icon type="ios-person-outline" slot="prepend"></Icon>
				</i-input>
			</Form-item>
		
			<Form-item prop="password">
				<i-input ref="ref_password" type="password" v-model="formInline.password" @on-enter="handleSubmit('formInline')" placeholder="Password">
					<Icon type="ios-lock-outline" slot="prepend"></Icon>
				</i-input>
			</Form-item>

			<i-row>
				<i-col span="16">
					<Form-item prop="captcha">
						<i-input ref="ref_captcha" type="text" v-model="formInline.captcha" @on-enter="handleSubmit('formInline')" placeholder="Captcha" style="width:120px">
							<Icon type="ios-lock-outline" slot="prepend"></Icon>
						</i-input>
					</Form-item>
				</i-col>
				<i-col span="8">
					<img ref="captcha" src="{{captcha_src('flatxz')}}" @click="captchaclick" style="cursor:pointer;vertical-align:top;">
				</i-col>
			</i-row>
			
			
			<br><br>
			<i-row>
				<i-col span="16">
					Remember Me&nbsp;
					<i-switch ref="ref_rememberme" v-model="formInline.rememberme" size="small">
						<span slot="open"></span>
						<span slot="close"></span>
					</i-switch>
				</i-col>
				<i-col span="8">
					<a href="#">Forget?</a>
				</i-col>
			</i-row>
			
			<br><br><br>
			<Form-item>
			<i-button type="primary" @click="handleSubmit('formInline')" ref="ref_login_submit">登 录</i-button>&nbsp;&nbsp;
			<i-button @click="handleReset('formInline')" ref="ref_login_reset" style="margin-left: 8px">重 置</i-button>
			</Form-item>
			
			<div v-html="formInline.loginmessage">@{{ formInline.loginmessage }}</div>
		
		
		</i-form>
	</i-col>
	<i-col span="10">
		&nbsp;
	</i-col>
</i-row>

<br><br><br>

@endsection

@section('my_footer')
<br><br>
@parent
<br><br><br><br>
@endsection

@section('my_js_others')
<script>
// ajax 获取数据
var vm_app = new Vue({
    el: '#app',
    data: {
		
		formInline: {
			username: '',
			password: '',
			captcha: '',
			rememberme: false,
			loginmessage: ''
		},
		ruleInline: {
			username: [
				{ required: true, message: 'Please fill in the user name', trigger: 'blur' }
			],
			password: [
				{ required: true, message: 'Please fill in the password.', trigger: 'blur' },
				{ type: 'string', min: 3, message: 'Password length is more than 3 bits', trigger: 'blur' }
			],
			captcha: [
				{ required: true, message: 'Please fill in the captcha.', trigger: 'blur' },
				{ type: 'string', min: 3, message: 'The captcha length is 3 bits', trigger: 'blur' }
			]
		},
		
    },
	methods: {
		handleSubmit(name) {
			this.$refs[name].validate((valid) => {
				if (valid) {
					// this.$Message.success('Success!');
					
					var _this = this;

					_this.logindisabled(true);
					_this.loginmessage = '<div class="text-info">Please wait ...</div>';
					
					if (_this.formInline.username.length == 0 || _this.formInline.password.length == 0 || _this.formInline.captcha.length == 0) {
						_this.formInline.loginmessage = '<div class="text-warning">Please full the item</div>';
						_this.logindisabled(false);
						return false;
					}

					var url = "{{ route('login.checklogin') }}";
					axios.post(url, {
						name: _this.formInline.username,
						password: _this.formInline.password,
						captcha: _this.formInline.captcha,
						rememberme: _this.formInline.rememberme
					})
					.then(function (response) {
						if (response.data) {
							_this.formInline.password = '**********';
							_this.formInline.loginmessage = '<font color="blue">login success, waiting ....</font>';
							window.setTimeout(function(){
								_this.loginreset;
								var url = "{{ route('admin.config.index') }}";
								window.location.href = url;
							}, 1000);
						} else {
							_this.formInline.loginmessage = '<font color="red">captcha error or login failed</font>';
							_this.logindisabled(false);
						}
					})
					.catch(function (error) {
						// console.log(error);
						_this.loginmessage = '<font color="red">error: failed</font>';
						_this.logindisabled(false);
					})
					_this.captchaclick();
				} else {
					// this.$Message.error('Fail!');
				}
			})
		},
		handleReset (name) {
			this.$refs[name].resetFields();
		},
		captchaclick: function(){
			this.$refs.captcha.src+=Math.random().toString().substr(-1);
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
		},
	}
});
</script>
@endsection