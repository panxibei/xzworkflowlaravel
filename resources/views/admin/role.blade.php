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
						<div class="col-lg-12">
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
									<multi-select v-model="selected_selectroletodelete" :options="options_selectroletodelete" ref="roledeleteselect" filterable collapse-selected size="sm" placeholder="请选择要删除的角色名称..." />
								</div>
								<div class="form-group">
									<button @click="roledelete" type="button" class="btn btn-danger btn-sm" >删除角色</button>
								</div>
							</div>
						</div>
						
						
						<div class="col-lg-12">
							<div style="background-color:#c9e2b3;height:1px"></div><br>
							
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
									<button type="button" class="btn btn-primary btn-sm" >添加角色到当前用户</button>
								</div>
								<div class="form-group">
									<label>Select role(s) to remove</label><br>
									<multi-select v-model="selected_currentuserroles" :options="options_currentuserroles" filterable collapse-selected size="sm" placeholder="请选择要移除的角色名称..." />
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-primary btn-sm" >移除角色从当前用户</button>
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
							<div class="col-lg-3">
								<div class="form-group">
									<label>Select role to view users</label><br>
									<multi-select v-model="selected" :options="options" :limit="1" filterable collapse-selected size="sm" placeholder="请选择角色名称..."/>
								</div>
								<div class="form-group">
									<label>User(s) using current role</label><br>
									<select id="select_slot2field_query_slot" class="form-control" size="11"></select>
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
    el: '#role_list',
    data: {
		notification_type: '',
		notification_title: '',
		notification_content: '',
		gets: {},
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
		rolelist: function(){
			// var _this = this;
			// var url = "{{ route('admin.group.list') }}";
			// var perPage = 1; // 有待修改，将来使用配置项
			
			// if (page > last_page) {
				// page = last_page;
			// } else if (page < 1) {
				// page = 1;
			// }
			// _this.gets.current_page = page;
			// axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			// axios.get(url, {
					// params: {
						// perPage: perPage,
						// page: page
					// }
				// })
				// .then(function (response) {
					// console.log(response);
					// _this.gets = response.data;
					// alert(_this.gets);
				// })
				// .catch(function (error) {
					// console.log(error);
				// })
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
		// 1.创建角色
		rolecreate: function () {
			var rolename = this.$refs.rolecreateinput.value;
			var _this = this;
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
			var rolename = this.$refs.roledeleteselect.value;
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
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
			// 显示所有角色
			var url = "{{ route('admin.role.rolelist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url, {
			})
			.then(function (response) {
				console.log(response);
				var json = response.data;
				_this.options_selectroletodelete = _this.json2selectvalue(json);
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 3.选择用户后显示当前用户拥有的角色
		changeuser: function (userid) {
			var _this = this;
			var url = "{{ route('admin.role.userhasrole') }}";

			if(userid.length==0){
				_this.options_currentuserroles = [];
				_this.options_currentusernothasroles = [];
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
			var username = this.$refs.roleuser.value;
			// var rolename = this.$refs.rolerole.value;
			// 提交为数组
			var rolename = [];
			rolename.push(this.$refs.rolerole.value);
			
			if(username.length==0||rolename.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.role.give') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					username: username,
					rolename: rolename
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Role [' + rolename + '] failed to update!');
				} else {
					_this.alert_message('SUCCESS', 'Role [' + rolename + '] updated successfully!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		}
		// 5.从用户移除角色

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
		var url = "{{ route('admin.role.rolelist') }}";
		axios.get(url, {
		})
		.then(function (response) {
			console.log(response);
			var json = response.data;
			_this.options_selectroletodelete = _this.json2selectvalue(json);
		})
		.catch(function (error) {
			console.log(error);
			alert(error);
		})

	}
});
</script>
@endsection