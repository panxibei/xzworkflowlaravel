@extends('admin.layouts.adminbase')

@section('my_title')
Admin(User) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div>

	<Divider orientation="left">User Management</Divider>

	<Tabs type="card" v-model="currenttabs">
		<Tab-pane label="User List">
			<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
			<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
		</Tab-pane>

		<Tab-pane label="Create/Edit Template">
		
			<i-row>
				<i-col span="8">
					<Card>
						<p slot="title">新建/编辑 TEMPLATE</p>
						<p>
							<input v-model="template_add_id" type="hidden">
							* 名称<br>
							<i-input v-model="template_add_name" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<i-button type="primary" @click="templatecreateorupdate('create')">Create</i-button>&nbsp;&nbsp;
						<i-button type="primary" @click="templatecreateorupdate('update')">Update</i-button>&nbsp;&nbsp;
						<i-button @click="onreset()">Reset</i-button>
					</Card>
				</i-col>
				<i-col span="16">
				&nbsp;
				</i-col>
			</i-row>

		</Tab-pane>

	</Tabs>

</div>










@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
@parent
<script>
// ajax 获取数据
var vm_app = new Vue({
    el: '#app',
    data: {
		current_nav: '',
		current_subnav: '',
		
		sideractivename: '3-1',
		sideropennames: ['1'],

		tablecolumns: [
			{
				type: 'index',
				width: 60,
				align: 'center'
			},
			{
				title: 'id',
				key: 'id',
				sortable: true,
				width: 80
			},
			{
				title: 'name',
				key: 'name'
			},
			{
				title: 'login IP',
				key: 'login_ip'
			},
			{
				title: 'login counts',
				key: 'login_counts',
				align: 'center'
			},
			{
				title: 'login time',
				key: 'login_time'
			},
			{
				title: 'status',
				key: 'deleted_at',
				align: 'center'
			},
			{
				title: 'created_at',
				key: 'created_at',
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'primary',
								size: 'small'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.template_detail(params.row)
								}
							}
						}, 'View'),
						h('Button', {
							props: {
								type: 'error',
								size: 'small'
							},
							on: {
								click: () => {
									vm_app.template_delete(params.row.id)
								}
							}
						}, 'Delete')
					]);
				}
			}
		],
		tabledata: [],
		
		//分页
		page_current: 1,
		page_total: 1, // 记录总数，非总页数
		page_size: {{ $config['PERPAGE_RECORDS_FOR_USER'] }},
		page_last: 1,		
		
		// 创建ID
		template_add_id: '',
		// 创建名称
		template_add_name: '',

		// tabs索引
		currenttabs: 0,
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
		queryfilter_name: "{{ $config['FILTERS_USER_NAME'] }}",
		queryfilter_email: "{{ $config['FILTERS_USER_EMAIL'] }}",
		queryfilter_datefrom: "{{ $config['FILTERS_USER_LOGINTIME_DATEFROM'] }}" || null,
		queryfilter_dateto: "{{ $config['FILTERS_USER_LOGINTIME_DATETO'] }}" || null
    },
	methods: {
		menuselect: function (name) {
			navmenuselect(name);
		},
		// 1.加载进度条
		loadingbarstart () {
			this.$Loading.start();
		},
		loadingbarfinish () {
			this.$Loading.finish();
		},
		loadingbarerror () {
			this.$Loading.error();
		},
		// 2.Notice 通知提醒
		info (nodesc, title, content) {
			this.$Notice.info({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		success (nodesc, title, content) {
			this.$Notice.success({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		warning (nodesc, title, content) {
			this.$Notice.warning({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		error (nodesc, title, content) {
			this.$Notice.error({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		
		alert_exit: function () {
			this.$Notice.error({
				title: '会话超时',
				desc: '会话超时，请重新登录！',
				duration: 2,
				onClose: function () {
					window.location.href = "{{ route('login') }}";
				}
			});
		},
		
		// 切换当前页
		oncurrentpagechange: function (currentpage) {
			this.templategets(currentpage, this.page_last);
		},
		// 切换页记录数
		onpagesizechange: function (pagesize) {
			
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_USER'] = pagesize;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.page_size = pagesize;
					_this.templategets(1, _this.page_last);
				} else {
					_this.warning(false, 'Warning', 'failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'failed!');
			})
		},
		
		usergets: function(page, last_page){
			var _this = this;
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}

			var queryfilter_name = _this.queryfilter_name;
			var queryfilter_email = _this.queryfilter_email;
			var queryfilter_datefrom = new Date(_this.queryfilter_datefrom);
			var queryfilter_dateto = new Date(_this.queryfilter_dateto);

			_this.gets.current_page = page;

			_this.loadingbarstart();
			var url = "{{ route('admin.user.list') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page,
					queryfilter_name: queryfilter_name,
					queryfilter_email: queryfilter_email,
					queryfilter_datefrom: queryfilter_datefrom,
					queryfilter_dateto: queryfilter_dateto
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;

				if (response.data.length == 0 || response.data.data == undefined) {
					_this.alert_exit();
				}
				// _this.gets = response.data;
				// alert(_this.gets);
				
				_this.page_current = response.data.current_page;
				_this.page_total = response.data.total;
				_this.page_last = response.data.last_page;
				_this.tabledata = response.data.data;
				
				_this.loadingbarfinish();
				
				
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
		_this.current_nav = '元素管理';
		_this.current_subnav = '基本元素 - Template';
		// 显示所有template
		_this.userlist(1, 1);
	}
});
</script>
@endsection