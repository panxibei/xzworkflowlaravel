@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Permission) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="permission_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Permission Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					权限管理
				</div>
				<div class="panel-body">
					<div class="row">

						<!--角色列表-->
						<div class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div>
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
											<td><div>@{{ val.created_at || '未知' }}</div></td>
											<td><div>@{{ val.updated_at || '未知'}}</div></td>
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
														<li><a aria-label="Previous" @click="permissiongets(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

														<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
															<a v-if="n==1" @click="permissiongets(1, gets.last_page)" href="javascript:;">1</a>
															<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="permissiongets(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
															<a v-else-if="n==2||n==gets.last_page">...</a>
														</li>&nbsp;

														<li><a aria-label="Next" @click="permissiongets(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
														<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

															<div class="col-xs-2">
															<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="permissiongets($event.target.value, gets.last_page)">
															</div>

														<div class="btn-group">
														<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ gets.per_page }}条<span class="caret"></span></button>
														<ul class="dropdown-menu">
														<li><a><small>5条记录</small></a></li>
														<li><a><small>10条记录</small></a></li>
														<li><a><small>20条记录</small></a></li>
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
				
				
						<!--权限操作1-->
						<div class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div><br>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Create permission</label><br>
									<input class="form-control input-sm" type="text" ref="permissioncreateinput" placeholder="权限名称" />
								</div>
								<div class="form-group">
									<button @click="permissioncreate" type="button" class="btn btn-primary btn-sm">新建权限</button>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Select permission(s) to delete</label><br>
									<multi-select v-model="selected_selectpermissiontodelete" :options="options_selectpermissiontodelete" filterable collapse-selected size="sm" placeholder="请选择要删除的权限名称..." />
								</div>
								<div class="form-group">
									<button @click="permissiondelete" type="button" class="btn btn-danger btn-sm" >删除权限</button>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Sync perimssion(s) to a role</label><br>
									权限： <multi-select v-model="selected_syncpermission" :options="options_syncpermission" :limit="1" filterable collapse-selected size="sm" placeholder="请选择权限..." />
								</div>
								<div class="form-group">
									角色： <multi-select v-model="selected_syncrole" :options="options_syncrole" filterable collapse-selected size="sm" placeholder="请选择角色..." />
								</div>
								<div class="form-group">
									<button @click="syncroletopermission" type="button" class="btn btn-primary btn-sm" >同步角色至权限</button>
								</div>
							</div>
						</div>
						
						<!--角色操作2-->
						<div class="col-lg-12">
							<div style="background-color:#c9e2b3;height:1px"></div><br>
							
							<div class="col-lg-3">
								<div class="form-group">
									<label>Select Role</label><br>
									<multi-select v-model="selected_selectedrole" :options="options_selectedrole" :limit="1" @change="changerole" filterable collapse-selected size="sm" placeholder="请选择角色名称..."/>
								</div>
								<div class="form-group">
									<label>Select permission(s) to add</label><br>
									<multi-select v-model="selected_currentrolenothaspermissions" :options="options_currentrolenothaspermissions" filterable collapse-selected size="sm" placeholder="请选择要添加的角色名称..." />
								</div>
								<div class="form-group">
									<button @click="permissiongive" type="button" class="btn btn-primary btn-sm" >添加角色到当前用户</button>
								</div>
								<div class="form-group">
									<label>Select permission(s) to remove</label><br>
									<multi-select v-model="selected_currentrolepermissions" :options="options_currentrolepermissions" filterable collapse-selected size="sm" placeholder="请选择要移除的权限名称..." />
								</div>
								<div class="form-group">
									<button @click="permissionremove" type="button" class="btn btn-primary btn-sm" >移除角色从当前用户</button>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Current role's permission(s)</label><br>
									<select v-model="selected_currentrolepermissions" class="form-control" size="16">
										<option v-for="option in options_currentrolepermissions" v-bind:value="option.value">
											@{{ option.label }}
										</option>
									</select>
								</div>
							</div>
							<div class="col-lg-1">
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label>Select permission to view roles</label><br>
									<multi-select v-model="selected_permissiontoviewrole" :options="options_permissiontoviewrole" :limit="1" @change="changepermissiontoviewrole" ref="roletoviewuserselect" filterable collapse-selected size="sm" placeholder="请选择角色名称..."/>
								</div>
								<div class="form-group">
									<label>Role(s) using current permission</label><br>
									<select v-model="selected_permissiontoviewroleresult" class="form-control" size="11">
										<option v-for="option in options_permissiontoviewroleresult" v-bind:value="option.value">
											@{{ option.label }}
										</option>
									</select>
								</div>
							</div>
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
    el: '#permission_list',
    data: {
		notification_type: '',
		notification_title: '',
		notification_content: '',
		gets: {},
		// 选择角色
		selected_selectedrole: [],
        options_selectedrole: [],
		// 选择要删除的权限
		selected_selectpermissiontodelete: [],
        options_selectpermissiontodelete: [],
		// 选择角色后显示当前角色拥有的权限
		selected_currentrolepermissions: [],
        options_currentrolepermissions: [],
		// 当前角色没有拥有的权限
		selected_currentrolenothaspermissions: [],
        options_currentrolenothaspermissions: [],
		// 选择权限查看哪些角色使用
		selected_permissiontoviewrole: [],
        options_permissiontoviewrole: [],
		selected_permissiontoviewroleresult: [],
        options_permissiontoviewroleresult: [],
		// 同步哪些权限到指定角色
		selected_syncpermission: [],
        options_syncpermission: [],
		selected_syncrole: [],
        options_syncrole: [],
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
		notification_message () {
			this.$notify({
				type: this.notification_type,
				title: this.notification_title,
				content: this.notification_content
			})
		},
		// 1.创建权限 ok
		permissioncreate: function () {
			var _this = this;
			var permissionname = _this.$refs.permissioncreateinput.value;

			var url = "{{ route('admin.permission.create') }}";

			if(permissionname.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please input the role name!';
				_this.notification_message();
				return false;
			}
			
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					permissionname: permissionname
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Permission [' + permissionname + '] failed to create!';
					_this.notification_message();
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Permission [' + permissionname + '] created successfully!';
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
		// 2.删除权限 ok
		permissiondelete: function () {
			var _this = this;
			var permissionname = _this.selected_selectpermissiontodelete;
			// alert(permissionname);return false;
			
			if(permissionname.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please select the permission(s)!';
				_this.notification_message();
				return false;
			}
			
			var url = "{{ route('admin.permission.permissiondelete') }}";

			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					permissionname: permissionname
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Permission(s) failed to delete!';
					_this.notification_message();
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Permission(s) deleted successfully!';
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
		// 3.选择角色后显示拥有的权限
		changerole: function (roleid) {
			var _this = this;
			var url = "{{ route('admin.permission.rolehaspermission') }}";

			_this.options_currentrolepermissions = [];
			_this.selected_currentrolepermissions = [];
			_this.options_currentrolenothaspermissions = [];
			_this.selected_currentrolenothaspermissions = [];
			if(roleid.length==0){
				return false;
			}

			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					roleid: roleid
				}
			})
			.then(function (response) {
				var json = response.data.rolehaspermission;
				_this.options_currentrolepermissions = _this.json2selectvalue(json);

				json = response.data.rolenothaspermission;
				_this.options_currentrolenothaspermissions = _this.json2selectvalue(json);
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 4.给角色赋予权限 ok
		permissiongive: function () {
			var _this = this;
			var roleid = _this.selected_selectedrole;
			var permissionid = _this.selected_currentrolenothaspermissions;
			
			if (roleid.length == 0 || permissionid.length == 0) { return false; }
			var url = "{{ route('admin.permission.give') }}";

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
					_this.notification_content = 'Permission(s) failed to give!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Permission(s) gave successfully!';
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
		// 5.从用户移除角色 ok
		permissionremove: function () {
			var _this = this;
			var roleid = _this.selected_selectedrole;
			var permissionid = _this.selected_currentrolepermissions;

			if (roleid.length == 0 || permissionid.length == 0) { return false; }
			var url = "{{ route('admin.permission.remove') }}";

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
					_this.notification_content = 'Permission(s) failed to remove!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Permission(s) removed successfully!';
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
		// 6.显示所有待删除的权限 ok
		permissionlistdelete: function () {
			var _this = this;
			var url = "{{ route('admin.permission.permissionlistdelete') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				var json = response.data;
				_this.options_selectpermissiontodelete = _this.json2selectvalue(json);
				_this.selected_selectpermissiontodelete = [];
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 7.显示所有权限 ok
		permissionlist: function () {
			var _this = this;
			var url = "{{ route('admin.permission.permissionlist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				var json = response.data;
				_this.options_permissiontoviewrole = _this.json2selectvalue(json);
				_this.selected_permissiontoviewrole = [];
				_this.options_syncpermission = _this.options_permissiontoviewrole;
				_this.selected_syncpermission = [];
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 8.显示所有角色 ok
		rolelist: function () {
			var _this = this;
			var url = "{{ route('admin.role.rolelist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				var json = response.data;
				_this.options_selectedrole = _this.json2selectvalue(json);
				_this.selected_selectedrole = [];
				_this.options_syncrole = _this.options_selectedrole;
				_this.selected_syncrole = [];
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 9.选择权限来查看哪些角色 ok
		changepermissiontoviewrole: function (permissionid) {
			var _this = this;
			if (permissionid.length == 0) {
				_this.options_permissiontoviewroleresult = [];
				_this.selected_permissiontoviewroleresult = [];
				return false;
			}
			var url = "{{ route('admin.permission.permissiontoviewrole') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
				params: {
					permissionid: permissionid
				}
			})
			.then(function (response) {
				var json = response.data;
				_this.options_permissiontoviewroleresult = _this.json2selectvalue(json);
				_this.selected_permissiontoviewroleresult = [];
			})
			.catch(function (error) {
				// console.log(error);
				alert(error);
			})
		},
		// 10.同步权限到指定角色
		syncroletopermission: function () {
			var _this = this;
			var permissionid = _this.selected_syncpermission;
			var roleid = _this.selected_syncrole;
			// alert(roleid);alert(permissionid);return false;

			if (roleid.length == 0 || permissionid.length == 0) { return false; }
			
			var url = "{{ route('admin.permission.syncroletopermission') }}";

			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					permissionid: permissionid,
					roleid: roleid
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
			_this.changerole(_this.selected_selectedrole);
			_this.permissionlistdelete();
			_this.permissionlist();
			_this.rolelist();
		},
		// 12.角色列表
		permissiongets: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.permission.permissiongets') }}";
			var perPage = 1; // 有待修改，将来使用配置项
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: perPage,
					page: page
				}
			})
			.then(function (response) {
				if (typeof(response.data.data) == "undefined") {
					alert(response);
					// _this.alert_exit();
				}
				_this.gets = response.data;
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		}
	},
	mounted: function(){
		var _this = this;

		// 显示所有
		_this.permissiongets(1, 1); // perPage: 1, page: 1
		_this.permissionlistdelete();
		_this.permissionlist();
		_this.rolelist();
	}
});
</script>
@endsection