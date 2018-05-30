@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Role) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="role_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Role Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					角色管理
				</div>
				<div class="panel-body">
					<div class="row">

					<div class="panel-body">
						<tabs>
							<tab title="UserList">
								<!--角色列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->
									<div class="table-responsive">
										<table class="table table-condensed">
											<thead>
												<tr>
													<th>id</th>
													<th>name</th>
													<th>guard_name</th>
													<th>created_at</th>
													<th>updated_at</th>
													<th>操作（保留）</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="val in gets.data">
													<td><div>@{{ val.id }}</div></td>
													<td><div>@{{ val.name }}</div></td>
													<td><div>@{{ val.guard_name }}</div></td>
													<td><div>@{{ val.created_at }}</div></td>
													<td><div>@{{ val.updated_at }}</div></td>
													<td><div><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit fa-fw"></i></button>
													&nbsp;<button class="btn btn-danger btn-xs"><i class="fa fa-times fa-fw"></i></button></div></td>
												</tr>
											</tbody>
										</table>

										<div class="dropup">
											<tr>
												<td colspan="9">
													<div>
														<nav>
															<ul class="pagination pagination-sm">
																<li><a aria-label="Previous" @click="rolegets(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

																<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
																	<a v-if="n==1" @click="rolegets(1, gets.last_page)" href="javascript:;">1</a>
																	<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="rolegets(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
																	<a v-else-if="n==2||n==gets.last_page">...</a>
																</li>&nbsp;

																<li><a aria-label="Next" @click="rolegets(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
																<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

																	<div class="col-xs-2">
																	<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="rolegets($event.target.value, gets.last_page)">
																	</div>

																<div class="btn-group">
																<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ perpage }}条<span class="caret"></span></button>
																<ul class="dropdown-menu">
																<li><a @click="configperpageforrole(2)" href="javascript:;"><small>2条记录</small></a></li>
																<li><a @click="configperpageforrole(5)" href="javascript:;"><small>5条记录</small></a></li>
																<li><a @click="configperpageforrole(10)" href="javascript:;"><small>10条记录</small></a></li>
																<li><a @click="configperpageforrole(20)" href="javascript:;"><small>20条记录</small></a></li>
																</ul>
																</div>
															</ul>
														</nav>
													</div>
												</td>
											</tr>
										</div>

									</div>
								</div>
							</tab>
							<tab title="General">
								<!--角色操作1-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div><br>-->
									<div class="col-lg-3">
										<div class="form-group">
											<label>Create role</label><br>
											<input class="form-control input-sm" type="text" ref="rolecreateinput" placeholder="角色名称" />
										</div>
										<div class="form-group">
											<button @click="rolecreate" type="button" class="btn btn-primary btn-sm">新建角色</button>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Select role(s) to delete</label><br>
											<multi-select v-model="selected_selectroletodelete" :options="options_selectroletodelete" filterable collapse-selected size="sm" placeholder="请选择要删除的角色名称..." />
										</div>
										<div class="form-group">
											<button @click="roledelete" type="button" class="btn btn-danger btn-sm" >删除角色</button>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Sync perimssion(s) to a role</label><br>
											角色： <multi-select v-model="selected_syncrole" :options="options_syncrole" :limit="1" filterable collapse-selected size="sm" placeholder="请选择角色..." />
										</div>
										<div class="form-group">
											权限： <multi-select v-model="selected_syncpermission" :options="options_syncpermission" filterable collapse-selected size="sm" placeholder="请选择权限..." />
										</div>
										<div class="form-group">
											<button @click="syncpermissiontorole" type="button" class="btn btn-primary btn-sm" >同步权限到角色</button>
										</div>
									</div>
									<div class="col-lg-3">

		@hasallroles('role_role_page|role_permission_page')
			我拥有访问role页面权限!
		@else
			我没有访问role页面权限...
		@endrole
		<br>
		@can('permission_config_page')
		  我有permission_role_page权限
		@endcan

									</div>
								</div>

							</tab>
							<tab title="Advance">

								<!--角色操作2-->
								<div class="col-lg-12">
									<br><!--<div style="background-color:#c9e2b3;height:1px"></div><br>-->
									
									<div class="col-lg-3">
										<div class="form-group">
											<label>Select User</label><br>
											<multi-select v-model="selected_selecteduser" :options="options_selecteduser" :limit="1" @change="changeuser" filterable collapse-selected size="sm" placeholder="请选择用户名称..."/>
										</div>
										<div class="form-group">
											<label>Select role(s) to add</label><br>
											<multi-select v-model="selected_currentusernothasroles" :options="options_currentusernothasroles" filterable collapse-selected size="sm" placeholder="请选择要添加的角色名称..." />
										</div>
										<div class="form-group">
											<button @click="rolegive" type="button" class="btn btn-primary btn-sm" >添加角色到当前用户</button>
										</div>
										<div class="form-group">
											<label>Select role(s) to remove</label><br>
											<multi-select v-model="selected_currentuserroles" :options="options_currentuserroles" filterable collapse-selected size="sm" placeholder="请选择要移除的角色名称..." />
										</div>
										<div class="form-group">
											<button @click="roleremove" type="button" class="btn btn-primary btn-sm" >移除角色从当前用户</button>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Current user's role(s)</label><br>
											<select v-model="selected_currentuserroles" class="form-control" size="16">
												<option v-for="option in options_currentuserroles" v-bind:value="option.value">
													@{{ option.label }}
												</option>
											</select>
										</div>
									</div>
									<div class="col-lg-1">
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Select role to view users</label><br>
											<multi-select v-model="selected_roletoviewuser" :options="options_roletoviewuser" :limit="1" @change="changeroletoviewuser" ref="roletoviewuserselect" filterable collapse-selected size="sm" placeholder="请选择角色名称..."/>
										</div>
										<div class="form-group">
											<label>User(s) using current role</label><br>
											<select v-model="selected_roletoviewuserresult" class="form-control" size="11">
												<option v-for="option in options_roletoviewuserresult" v-bind:value="option.value">
													@{{ option.label }}
												</option>
											</select>
										</div>
									</div>
								</div>

							</tab>
						</tabs>
					</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endsection

@section('my_footer')
@parent
<script>
var vm_role = new Vue({
    el: '#role_list',
    data: {
		notification_type: '',
		notification_title: '',
		notification_content: '',
		gets: {},
		perpage: {{ $PERPAGE_RECORDS_FOR_ROLE }},
		// 选择用户
		selected_selecteduser: [],
        options_selecteduser: [],
		// 选择要删除的角色
		selected_selectroletodelete: [],
        options_selectroletodelete: [],
		// 选择用户后显示当前用户拥有的角色
		selected_currentuserroles: [],
        options_currentuserroles: [],
		// 当前用户没有拥有的角色
		selected_currentusernothasroles: [],
        options_currentusernothasroles: [],
		// 选择角色查看哪些用户使用
		selected_roletoviewuser: [],
        options_roletoviewuser: [],
		selected_roletoviewuserresult: [],
        options_roletoviewuserresult: [],
		// 同步哪些权限到指定角色
		selected_syncrole: [],
        options_syncrole: [],
		selected_syncpermission: [],
        options_syncpermission: [],
		// select样例
		selected: [],
        options: [
			{value: 1, label:'Option1'},
			{value: 2, label:'Option2'},
			{value: 3, label:'Option3333333333'},
			{value: 4, label:'Option4'},
			{value: 5, label:'Option5'}
        ]
    },
	methods: {
		// 把laravel返回的结果转换成select能接受的格式
		json2selectvalue: function (json) {
			var arr = [];
			for (var key in json) {
				// alert(key);
				// alert(json[key]);
				// arr.push({ obj.['value'] = key, obj.['label'] = json[key] });
				arr.push({ value: key, label: json[key] });
			}
			return arr;
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
		notification_message: function () {
			this.$notify({
				type: this.notification_type,
				title: this.notification_title,
				content: this.notification_content
			})
		},
		// 1.创建角色
		rolecreate: function () {
			var _this = this;
			var rolename = _this.$refs.rolecreateinput.value;
			var url = "{{ route('admin.role.create') }}";

			if(rolename.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please input the role name!';
				_this.notification_message();
				return false;
			}
			
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					rolename: rolename
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					// _this.alert_message('WARNING', 'Role [' + rolename + '] failed to create!');
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Role [' + rolename + '] failed to create!';
					_this.notification_message();
				} else {
					// _this.alert_message('SUCCESS', 'Role [' + rolename + '] created successfully!');
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Role [' + rolename + '] created successfully!';
					_this.notification_message();

					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				// console.log(error);
				// alert(error.response.data.message);
				// _this.alert_message('ERROR', error.response.data.message);
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 2.删除角色
		roledelete: function () {
			var _this = this;
			var rolename = _this.selected_selectroletodelete;
			// alert(rolename);return false;
			
			if(rolename.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please select the role(s)!';
				_this.notification_message();
				return false;
			}
			
			var url = "{{ route('admin.role.roledelete') }}";
			// alert(url);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					rolename: rolename
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Role(s) failed to delete!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Role(s) deleted successfully!';
					_this.notification_message();
					
					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 3.选择用户后显示当前用户拥有的角色
		changeuser: function (userid) {
			var _this = this;
			var url = "{{ route('admin.role.userhasrole') }}";

			_this.options_currentuserroles = [];
			_this.selected_currentuserroles = [];
			_this.options_currentusernothasroles = [];
			_this.selected_currentusernothasroles = [];
			if(userid.length==0){
				return false;
			}

			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
				}
			})
			.then(function (response) {
				var json = response.data.userhasrole;
				_this.options_currentuserroles = _this.json2selectvalue(json);

				json = response.data.usernothasrole;
				_this.options_currentusernothasroles = _this.json2selectvalue(json);
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 4.给用户赋予角色
		rolegive: function () {
			var _this = this;
			var userid = _this.selected_selecteduser;
			var roleid = _this.selected_currentusernothasroles;
			
			if (userid.length == 0 || roleid.length == 0) { return false; }
			var url = "{{ route('admin.role.give') }}";

			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					userid: userid,
					roleid: roleid
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Role(s) failed to give!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Role(s) gave successfully!';
					_this.notification_message();
					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 5.从用户移除角色
		roleremove: function () {
			var _this = this;
			var userid = _this.selected_selecteduser;
			var roleid = _this.selected_currentuserroles;

			if (userid.length == 0 || roleid.length == 0) { return false; }
			var url = "{{ route('admin.role.remove') }}";

			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					userid: userid,
					roleid: roleid
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Role(s) failed to remove!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Role(s) removed successfully!';
					_this.notification_message();
					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},		
		// 6.显示所有待删除的角色
		rolelistdelete: function () {
			var _this = this;
			var url = "{{ route('admin.role.rolelistdelete') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				var json = response.data;
				_this.options_selectroletodelete = _this.json2selectvalue(json);
				_this.selected_selectroletodelete = [];
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 7.显示所有角色
		rolelist: function () {
			var _this = this;
			var url = "{{ route('admin.role.rolelist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				var json = response.data;
				_this.options_roletoviewuser = _this.json2selectvalue(json);
				_this.selected_roletoviewuser = [];
				_this.options_syncrole = _this.options_roletoviewuser;
				_this.selected_syncrole = [];
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 8.显示所有权限
		permissionlist: function () {
			var _this = this;
			var url = "{{ route('admin.role.permissionlist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				var json = response.data;
				_this.options_syncpermission = _this.json2selectvalue(json);
				_this.selected_syncpermission = [];
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 9.选择角色来查看哪些用户
		changeroletoviewuser: function (roleid) {
			var _this = this;
			if (roleid.length == 0) {
				_this.options_roletoviewuserresult = [];
				_this.selected_roletoviewuserresult = [];
				return false;
			}
			var url = "{{ route('admin.role.roletoviewuser') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
				params: {
					roleid: roleid
				}
			})
			.then(function (response) {
				var json = response.data;
				_this.options_roletoviewuserresult = _this.json2selectvalue(json);
				_this.selected_roletoviewuserresult = [];
			})
			.catch(function (error) {
				// console.log(error);
				alert(error);
			})
		},
		// 10.同步权限到指定角色
		syncpermissiontorole: function () {
			var _this = this;
			var roleid = _this.selected_syncrole;
			var permissionid = _this.selected_syncpermission;
			// alert(roleid);alert(permissionid);return false;

			if (roleid.length == 0 || permissionid.length == 0) { return false; }
			
			var url = "{{ route('admin.role.syncpermissiontorole') }}";

			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					roleid: roleid,
					permissionid: permissionid
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Permission(s) failed to sync!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Permission(s) sync successfully!';
					_this.notification_message();
					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 11.每次操作后的各部分刷新
		refreshview: function () {
			var _this = this;
			_this.changeuser(_this.selected_selecteduser);
			_this.rolelistdelete();
			_this.rolelist();
			_this.permissionlist();
		},
		// 12.角色列表
		rolegets: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.role.rolegets') }}";
			// var perPage = 1; // 有待修改，将来使用配置项
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.perpage,
					page: page
				}
			})
			.then(function (response) {
				if (typeof(response.data.data) == "undefined") {
					// alert(response);
					_this.alert_exit();
				}
				_this.gets = response.data;
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		configperpageforrole: function (value) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_ROLE'] = value;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.perpage = value;
					_this.rolegets(1, 1);
				} else {
					alert('failed');
				}
			})
			.catch(function (error) {
				alert('failed');
				// console.log(error);
			})
		}
	},
	mounted: function(){
		var _this = this;

		// 显示所有用户
		var url = "{{ route('admin.role.userlist') }}";
		axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
		axios.get(url, {
		})
		.then(function (response) {
			console.log(response);
			var json = response.data;
			_this.options_selecteduser = _this.json2selectvalue(json);
		})
		.catch(function (error) {
			console.log(error);
			alert(error);
		})
		// 显示所有角色
		_this.rolegets(1, 1); // page: 1, last_page: 1
		_this.rolelistdelete();
		_this.rolelist();
		_this.permissionlist();
	}
});
</script>
@endsection