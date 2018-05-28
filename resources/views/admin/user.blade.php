@extends('admin.layouts.adminbase')

@section('my_title', "Admin(User) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="user_list">
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">User Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					用户管理
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<btn type="default" @click="open_queryuser=!open_queryuser" size="sm">Query Filter</btn>&nbsp;
								<btn type="default" @click="userexport()" size="sm"><i class="fa fa-external-link fa-fw"></i> 导出</btn>&nbsp;
								<btn type="default" @click="open_createuser=true" size="sm">Create User</btn>
								
								
							</div>
						</div>
						<div class="col-lg-12">
							<collapse v-model="open_queryuser">
								<div class="well" style="margin-bottom: 0">
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label class="control-label">账号</label>
												<input v-model.lazy="queryfilter_name" class="form-control input-sm" type="text" placeholder="账号">
								<br><btn type="default" size="sm"  @click="queryfilter()">Query</btn>
								&nbsp;<btn type="default" size="sm"  @click="queryfilter_name=queryfilter_email='';queryfilter_datefrom=queryfilter_dateto=null;queryfilter()">Clear</btn>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label class="control-label">Email</label>
												<input v-model.lazy="queryfilter_email" class="form-control input-sm" type="text" placeholder="邮箱">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label class="control-label">最近登录时间（始）</label>
												<dropdown class="form-group">
													<div class="input-group">
														<input class="form-control" type="text" v-model.lazy="queryfilter_datefrom" placeholder="开始时间">
														<div class="input-group-btn">
															<btn class="dropdown-toggle"><i class="fa fa-calendar fa-fw"></i></btn>
														</div>
													</div>
													<template slot="dropdown">
														<li>
															<date-picker v-model="queryfilter_datefrom"/>
														</li>
													</template>
												</dropdown>
											</div>
										</div>
										<div class="col-lg-3">
											<label class="control-label">最近登录时间（终）</label>
											<dropdown class="form-group">
												<div class="input-group">
													<input class="form-control" type="text" v-model.lazy="queryfilter_dateto" placeholder="结束时间">
													<div class="input-group-btn">
														<btn class="dropdown-toggle"><i class="fa fa-calendar fa-fw"></i></btn>
													</div>
												</div>
												<template slot="dropdown">
													<li>
														<date-picker v-model="queryfilter_dateto"/>
													</li>
												</template>
											</dropdown>
										</div>
										<div class="col-lg-3">
										</div>
									</div>
								</div>
							</collapse>						
						</div>


						<div class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div>
							<div class="table-responsive" v-cloak>
								<table class="table table-condensed">
									<thead>
										<tr>
											<th>ID</th>
											<th>用户名</th>
											<th>Email</th>
											<th>最近登录IP</th>
											<th>登录次数</th>
											<th>最近登录时间</th>
											<th>状态</th>
											<th>创建/更新时间</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody id="tbody_user_query">
				
										<tr v-for="val in gets.data">
											<td><div>@{{ val.id }}</div></td>
											<td><div>@{{ val.name }}</div></td>
											<td><div>@{{ val.email }}</div></td>
											<td><div>@{{ val.login_ip }}</div></td>
											<td><div>@{{ val.login_counts }}</div></td>
											<td><div>@{{ val.login_time }}</div></td>
											<td><div>@{{ val.deleted_at ? "禁用" : "启用" }}</div></td>
											<td><div>@{{ val.created_at }}<br>@{{ val.updated_at }}</div></td>
											<td><div>
											&nbsp;<btn type="primary" size="xs" @click="open_edituser=true;currentuser=val;" :id="'btnedituser'+val.id"><i class="fa fa-edit fa-fw"></i></btn>
											<tooltip text="编辑" :target="'#btnedituser'+val.id"/>
											&nbsp;<btn type="warning" size="xs" @click="trashuser(val.id)" :id="'btntrashuser'+val.id"><i class="fa fa-trash-o fa-fw"></i></btn>
											<tooltip text="禁用/启用" :target="'#btntrashuser'+val.id"/>
											&nbsp;<btn type="danger" size="xs" @click="deleteuser(val.id, val.name)" :id="'btndeleteuser'+val.id"><i class="fa fa-times fa-fw"></i></btn>
											<tooltip text="删除" :target="'#btndeleteuser'+val.id"/>
											</div></td>
										</tr>

									</tbody>
								</table>
								<div id="div_user_query" class="dropup">
								
									<tr><td colspan="9"><div><nav>

										<ul class="pagination pagination-sm">
											<li><a aria-label="Previous" @click="userlist(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

											<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
												<a v-if="n==1" @click="userlist(1, gets.last_page)" href="javascript:;">1</a>
												<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="userlist(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
												<a v-else-if="n==2||n==gets.last_page">...</a>
											</li>&nbsp;

											<li><a aria-label="Next" @click="userlist(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
											<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

												<div class="col-xs-2">
												<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="userlist($event.target.value, gets.last_page)">
												</div>

											<div class="btn-group">
											<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ perpage }}条<span class="caret"></span></button>
											<ul class="dropdown-menu">
											<li><a @click="configperpageforuser(2)" href="javascript:;"><small>2条记录</small></a></li>
											<li><a @click="configperpageforuser(5)" href="javascript:;"><small>5条记录</small></a></li>
											<li><a @click="configperpageforuser(10)" href="javascript:;"><small>10条记录</small></a></li>
											<li><a @click="configperpageforuser(20)" href="javascript:;"><small>20条记录</small></a></li>
											</ul>
											</div>
										</ul>

									</nav></div></td></tr>

								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal user edit -->
<modal v-model="open_edituser" @hide="callback_edituser" size="sm">
	<span slot="title"><i class="fa fa-user fa-fw"></i> Edit User</span>

	<div class="container">
		<div class="row">
			<div  class="col-lg-3">
				<!--<input v-model="currentuser.id" type="hidden" class="form-control input-sm">-->
				<input :value="up2dateuser.id=currentuser.id" type="hidden" class="form-control input-sm">
				<div class="form-group">
					<label>账号</label>
					<!--<input v-model="currentuser.name" type="text" class="form-control input-sm">-->
					<input :value="currentuser.name" @change="forchange('name', $event.target.value)" type="text" class="form-control input-sm">
				</div>
				<div class="form-group">
					<label>Email</label>
					<input :value="currentuser.email" @change="forchange('email', $event.target.value)" type="text" class="form-control input-sm">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input :value="currentuser.password" @change="forchange('password', $event.target.value)" type="text" class="form-control input-sm">
				</div>
			</div>
		</div>
	</div>

	<div slot="footer">
		<btn @click="open_edituser=false">Cancel</btn>
		<btn @click="edituser" type="primary">Update</btn>
	</div>	
</modal>

<!-- Modal create user-->
<modal v-model="open_createuser" @hide="callback_createuser" size="sm">
	<span slot="title"><i class="fa fa-user fa-fw"></i> Create User</span>

	<div class="container">
		<div class="row">
			<div  class="col-lg-3">
				<div class="form-group">
					<label>账号</label>
					<input v-model="createuser_name" type="text" class="form-control input-sm">
				</div>
				<div class="form-group">
					<label>Email</label>
					<input v-model="createuser_email" type="email" class="form-control input-sm">
				</div>
			</div>
		</div>
	</div>

	<div slot="footer">
		<btn @click="open_createuser=false">Cancel</btn>
		<btn @click="createuser" type="primary">Create User</btn>
	</div>	
</modal>

</div>
@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_user = new Vue({
    el: '#user_list',
    data: {
		gets: {},
		perpage: {{ $PERPAGE_RECORDS_FOR_USER }},
		currentuser: {
			id: '',
			name: '',
			email: '',
			password: ''
		},
		up2dateuser: {
			id: '',
			name: '',
			email: '',
			password: ''
		},
		// currentuserpassword: '',
		// 创建
		open_createuser: false,
		createuser_name: '',
		createuser_email: '',
		// 编辑
		open_edituser: false,
		edituser_name: '',
		edituser_email: '',
		// 查询
		open_queryuser: false,
		// 查询过滤器
		queryfilter_name: "{{ $FILTERS_USER_NAME }}",
		queryfilter_email: "{{ $FILTERS_USER_EMAIL }}",
		queryfilter_datefrom: "{{ $FILTERS_USER_LOGINTIME_DATEFROM }}" || null,
		queryfilter_dateto: "{{ $FILTERS_USER_LOGINTIME_DATETO }}" || null
    },
	methods: {
		// 表单变化后的值
		forchange: function (key, value) {
			// alert(value);
			var _this = this;
			if (key == "name") {
				_this.up2dateuser.name = value
			} else if ((key == "email")) {
				_this.up2dateuser.email = value
			} else if ((key == "password")) {
				_this.up2dateuser.password = value
			}
			// alert(_this.up2dateuser.id);
		},
		userlist: function(page, last_page){
			var _this = this;
			var queryfilter_name = _this.queryfilter_name;
			var queryfilter_email = _this.queryfilter_email;
			var queryfilter_datefrom = new Date(_this.queryfilter_datefrom);
			var queryfilter_dateto = new Date(_this.queryfilter_dateto);

			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;

			var url = "{{ route('admin.user.list') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.perpage,
					page: page,
					queryfilter_name: queryfilter_name,
					queryfilter_email: queryfilter_email,
					queryfilter_datefrom: queryfilter_datefrom,
					queryfilter_dateto: queryfilter_dateto
				}
			})
			.then(function (response) {
				// console.log(response);
				// alert(response.data);
				if (typeof(response.data.data) == "undefined") {
					// alert('toekn失效，跳转至登录页面');
					// _this.alert_exit();
				}
				// return false;
				_this.gets = response.data;
				// alert(_this.gets);
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		alert_exit: function () {
			this.$alert({
				title: '会话超时',
				content: '会话超时，请重新登录！'
			// }, (msg) => {
			}, function (msg) {
				// callback after modal dismissed
				// this.$notify(`You selected ${msg}.`);
				// this.$notify('You selected ${msg}.');
				// window.setTimeout(function(){
					window.location.href = "{{ route('admin.config.index') }}";
				// },1000);
			})
		},
		configperpageforuser: function (value) {
			var _this = this;
			
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_USER'] = _this.perpage = value;

			_this.changeconfig(cfg_data);			

			_this.userlist(1, 1);
		},
		callback_createuser: function (msg) {
			var _this = this;
			// _this.$notify(`Modal dismissed with msg '${msg}'.`)
			_this.createuser_name = _this.createuser_email = '';
		},
		createuser: function () {
			var _this = this;
			var name = _this.createuser_name;
			var email = _this.createuser_email;

			if ( name.length == 0 || email.length == 0) {return false;}
			
			// var re = new RegExp(“a”);  //RegExp对象。参数就是我们想要制定的规则。有一种情况必须用这种方式，下面会提到。
			// var re = /a/;   // 简写方法 推荐使用 性能更好  不能为空 不然以为是注释 ，
			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(email)) {
				_this.$notify('Email is incorrect!');
				return false;
			}

			var url = "{{ route('admin.user.create') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				name: name,
				email: email
			})
			.then(function (response) {
				if (response.data) {
					_this.$notify('User created successfully!');
					_this.createuser_name = '';
					_this.createuser_email = '';
					_this.userlist(_this.gets.current_page, _this.gets.last_page);
				} else {
					_this.$notify('User created failed!');
				}
			})
			.catch(function (error) {
				_this.$notify('Error! User created failed!');
				// console.log(error);
			})
		},
		callback_edituser: function (msg) {
			// this.$notify(`Modal dismissed with msg '${msg}'.`)
		},
		edituser: function () {
			var _this = this;
			var user = _this.up2dateuser;
			
			if (user.length == 0) {return false;}
			// user['password'] = _this.currentuserpassword;
			if (user.name.trim().length == 0 || user.email.trim().length == 0) {
				_this.$notify('Please input username and email!');
				return false;
			}

			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(user.email)) {
				_this.$notify('Email is incorrect!');
				return false;
			}

			var url = "{{ route('admin.user.edit') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				user: user
			})
			.then(function (response) {
				if (response.data) {
					_this.open_edituser = false;
					_this.$notify('User updated successfully!');
					// _this.currentuser.password = '';
					_this.userlist(_this.gets.current_page, _this.gets.last_page);
				} else {
					_this.$notify('User updated failed!');
				}
			})
			.catch(function (error) {
				_this.$notify('Error! User updated failed!');
				// console.log(error);
			})
		},
		callback_deleteuser: function (msg) {
			// this.$notify(`Modal dismissed with msg '${msg}'.`)
		},
		trashuser: function (userid) {
			var _this = this;
			if (userid == undefined || userid.length == 0) {return false;}
			var url = "{{ route('admin.user.trash') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				userid: userid
			})
			.then(function (response) {
				if (response.data) {
					_this.$notify('User 禁用/启用 successfully!');
					_this.userlist(_this.gets.current_page, _this.gets.last_page);
				} else {
					_this.$notify('User 禁用/启用 failed!');
				}
			})
			.catch(function (error) {
				_this.$notify('Error! User deleted failed!');
				// console.log(error);
			})			
		},
		deleteuser: function (userid, username) {
			var _this = this;
			if (userid == undefined || userid.length == 0) {return false;}
			
			_this.$confirm({
				okText: '删除',
				okType: 'danger',
				cancelText: '取消',
				title: '危险',
				content: '即将完全删除用户 [' + username + ']，确认吗？'
			})
			.then(function () {
				// this.$notify({
					// type: 'success',
					// content: 'Delete completed.'
				// })
				
				var url = "{{ route('admin.user.delete') }}";
				axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
				axios.post(url, {
					userid: userid
				})
				.then(function (response) {
					if (response.data) {
						_this.$notify('User deleted successfully!');
						_this.userlist(_this.gets.current_page, _this.gets.last_page);
					} else {
						_this.$notify('User deleted failed!');
					}
				})
				.catch(function (error) {
					_this.$notify('Error! User deleted failed!');
					// console.log(error);
				})
			})
			.catch(function () {
				// this.$notify('Delete canceled.')
				return false;
			})
		},
		queryfilter: function () {
			var _this = this;
			// var queryfilter_name = _this.queryfilter_name;
			// var queryfilter_email = _this.queryfilter_email;
			var queryfilter_datefrom = new Date(_this.queryfilter_datefrom);
			var queryfilter_dateto = new Date(_this.queryfilter_dateto);
			if (queryfilter_datefrom > queryfilter_dateto) {
				_this.$notify('Date is incorrect!');
				return false;
			}

			var cfg_data = {};
			cfg_data['FILTERS_USER_NAME'] = _this.queryfilter_name;
			cfg_data['FILTERS_USER_EMAIL'] = _this.queryfilter_email;
			cfg_data['FILTERS_USER_LOGINTIME_DATEFROM'] = _this.queryfilter_datefrom;
			cfg_data['FILTERS_USER_LOGINTIME_DATETO'] = _this.queryfilter_dateto;

			_this.changeconfig(cfg_data);
			
			_this.userlist(1, 1);
		},
		userexport: function(){
			// var _this = this;
			// var queryfilter_name = _this.queryfilter_name;
			// var queryfilter_email = _this.queryfilter_email;
			// var queryfilter_datefrom = new Date(_this.queryfilter_datefrom);
			// var queryfilter_dateto = new Date(_this.queryfilter_dateto);
			
			// if (queryfilter_datefrom > queryfilter_dateto) {
				// _this.$notify('Date is incorrect!');
				// return false;
			// }

			var url = "{{ route('admin.user.excelexport') }}";
			
			window.setTimeout(function(){
				window.location.href = url;
			},1000);
			return false;

		},
		changeconfig: function (cfg_data) {
			var _this = this;

			// var cfg_data = {};
			// cfg_data[key] = value;

			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					// alert('success');
				} else {
					// _this.notification_type = 'danger';
					// _this.notification_title = 'Error';
					// _this.notification_content = cfg_name + 'failed to be modified!';
					// _this.notification_message();
					// event.target.value = cfg_value;
				}
			})
			.catch(function (error) {
				// alert('failed');
				// console.log(error);
			})			
		}
	},
	mounted: function(){
		var _this = this;
		_this.userlist(1, 1);
	}
});
</script>
@endsection