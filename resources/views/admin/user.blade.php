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
		
			<Collapse v-model="collapse_query">
				<Panel name="1">
					User Query Filter
					<p slot="content">
					
						<i-row :gutter="16">
							<i-col span="8">
								* login time&nbsp;&nbsp;
								<Date-picker v-model.lazy="queryfilter_logintime" @on-change="usergets(page_current, page_last);onselectchange();" type="daterange" size="small" placement="top" style="width:200px"></Date-picker>
							</i-col>
							<i-col span="4">
								name&nbsp;&nbsp;
								<i-input v-model.lazy="queryfilter_name" @on-change="usergets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
							</i-col>
							<i-col span="4">
								email&nbsp;&nbsp;
								<i-input v-model.lazy="queryfilter_email" @on-change="usergets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
							</i-col>
							<i-col span="4">
								login ip&nbsp;&nbsp;
								<i-input v-model.lazy="queryfilter_loginip" @on-change="usergets(page_current, page_last)" size="small" clearable style="width: 100px"></i-input>
							</i-col>
							<i-col span="4">
								&nbsp;
							</i-col>
						</i-row>
					
					
					&nbsp;
					</p>
				</Panel>
			</Collapse>
			<br>
			
			<i-row :gutter="16">
				<br>
				<i-col span="2">
					<i-button @click="ondelete_user()" :disabled="delete_disabled_user" type="warning" size="small">Delete</i-button>&nbsp;<br>&nbsp;
				</i-col>
				<i-col span="4">
					导出：&nbsp;&nbsp;&nbsp;&nbsp;
					<i-button type="default" size="small" @click=""><Icon type="ios-download-outline"></Icon> 导出</i-button>
				</i-col>
				<i-col span="18">
					&nbsp;
				</i-col>
			</i-row>
			
			<i-row :gutter="16">
				<i-col span="24">
		
					<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata" @on-selection-change="selection => onselectchange(selection)"></i-table>
					<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
				
					<Modal v-model="modal_user_edit" @on-ok="user_edit_ok" ok-text="保存" title="Edit - User" width="420">
						<div style="text-align:left">
							
							<p>
								name&nbsp;&nbsp;
								<i-input v-model.lazy="user_edit_name" placeholder="" size="small" clearable style="width: 120px"></i-input>

								&nbsp;&nbsp;&nbsp;&nbsp;

								email&nbsp;&nbsp;
								<i-input v-model.lazy="user_edit_email" placeholder="" size="small" clearable style="width: 120px" type="email"></i-input>

							</p>
							
							&nbsp;
						
						</div>	
					</Modal>
			
				</i-col>
			</i-row>

		
		</Tab-pane>

		<Tab-pane label="Create/Edit Template">
		


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
		sideropennames: ['3'],

		tablecolumns: [
			{
				type: 'selection',
				width: 50,
				align: 'center',
				fixed: 'left'
			},
			{
				type: 'index',
				align: 'center',
				width: 60,
			},
			{
				title: 'id',
				key: 'id',
				sortable: true,
				width: 80
			},
			{
				title: 'name',
				key: 'name',
				width: 120
			},
			{
				title: 'email',
				key: 'email',
				width: 160
			},
			{
				title: 'login IP',
				key: 'login_ip',
				width: 100
			},
			{
				title: 'counts',
				key: 'login_counts',
				align: 'center',
				sortable: true,
				width: 100
			},
			{
				title: 'login time',
				key: 'login_time',
				width: 160
			},
			{
				title: 'status',
				key: 'deleted_at',
				align: 'center',
				width: 80,
				render: (h, params) => {
					return h('div', [
						// params.row.deleted_at.toLocaleString()
						params.row.deleted_at ? '禁用' : '启用'
					]);
				}
			},
			{
				title: 'created_at',
				key: 'created_at',
				width: 160
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 80,
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
									vm_app.user_edit(params.row)
								}
							}
						}, 'Edit')
					]);
				},
				fixed: 'right'
			}
		],
		tabledata: [],
		tableselect: [],
		
		//分页
		page_current: 1,
		page_total: 1, // 记录总数，非总页数
		page_size: {{ $config['PERPAGE_RECORDS_FOR_USER'] }},
		page_last: 1,		
		
		// 创建
		user_add_id: '',
		user_add_name: '',
		
		// 编辑
		modal_user_edit: false,
		user_edit_id: '',
		user_edit_name: '',
		user_edit_email: '',
		
		// 删除
		delete_disabled_user: true,

		// tabs索引
		currenttabs: 0,
		
		// 查询过滤器
		queryfilter_name: "{{ $config['FILTERS_USER_NAME'] }}",
		queryfilter_email: "{{ $config['FILTERS_USER_EMAIL'] }}",
		queryfilter_logintime: "{{ $config['FILTERS_USER_LOGINTIME'] }}" || [],
		queryfilter_loginip: "{{ $config['FILTERS_USER_LOGINIP'] }}",
		
		//
		collapse_query: '',
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
			this.usergets(currentpage, this.page_last);
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
					_this.usergets(1, _this.page_last);
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
			
			var queryfilter_logintime = [];

			for (var i in _this.queryfilter_logintime) {
				if (typeof(_this.queryfilter_logintime[i])!='string') {
					queryfilter_logintime.push(_this.queryfilter_logintime[i].Format("yyyy-MM-dd"));
				} else if (_this.queryfilter_logintime[i] == '') {
					// queryfilter_logintime.push(new Date().Format("yyyy-MM-dd"));
					_this.tabledata = [];
					return false;
				} else {
					queryfilter_logintime.push(_this.queryfilter_logintime[i]);
				}
			}

			var queryfilter_name = _this.queryfilter_name;
			var queryfilter_email = _this.queryfilter_email;
			var queryfilter_loginip = _this.queryfilter_loginip;

			_this.loadingbarstart();
			var url = "{{ route('admin.user.list') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page,
					queryfilter_name: queryfilter_name,
					queryfilter_logintime: queryfilter_logintime,
					queryfilter_email: queryfilter_email,
					queryfilter_loginip: queryfilter_loginip,
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
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},		
		
		// 表user选择
		onselectchange: function (selection) {
			var _this = this;
			_this.tableselect = [];

			for (var i in selection) {
				_this.tableselect.push(selection[i].id);
			}
			
			_this.delete_disabled_user = _this.tableselect[0] == undefined ? true : false;
		},
		
		// user编辑前查看
		user_edit: function (row) {
			var _this = this;
			
			_this.user_edit_id = row.id;
			_this.user_edit_name = row.name;
			_this.user_edit_email = row.email;
			// _this.relation_xuqiushuliang_edit[0] = row.xuqiushuliang;
			// _this.relation_xuqiushuliang_edit[1] = row.xuqiushuliang;
			// _this.user_created_at_edit = row.created_at;
			// _this.user_updated_at_edit = row.updated_at;

			_this.modal_user_edit = true;
		},		
		

		// user编辑后保存
		user_edit_ok: function () {
			var _this = this;
			
			var id = _this.user_edit_id;
			var name = _this.user_edit_name;
			var email = _this.user_edit_email;
			// var created_at = _this.relation_created_at_edit;
			// var updated_at = _this.relation_updated_at_edit;
			
			if (name == '' || name == null || name == undefined
				|| email == '' || email == null || email == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(email)) {
				_this.$notify('Email is incorrect!');
				return false;
			}
			
			var url = "{{ route('admin.user.edit') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id: id,
				name: name,
				email: email,
				// xuqiushuliang: xuqiushuliang[1],
				// created_at: created_at,
				// updated_at: updated_at
			})
			.then(function (response) {
				console.log(response.data);
				return false;
				
				_this.relationgets(_this.pagecurrent_relation, _this.pagelast_relation);
				
				if (response.data) {
					_this.success(false, '成功', '更新成功！');
					
					_this.relation_id_edit = '';
					_this.relation_jizhongming_edit = '';
					_this.relation_pinfan_edit = '';
					_this.relation_pinming_edit = '';
					_this.relation_xuqiushuliang_edit = [0, 0];
					_this.relation_leibie_edit = '';
					_this.relation_created_at_edit = '';
					_this.relation_updated_at_edit = '';
				} else {
					_this.error(false, '失败', '更新失败！请刷新查询条件后再试！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '更新失败！');
			})			
		},		
		
		// ondelete_user
		ondelete_user: function () {
			var _this = this;
			
			var tableselect = _this.tableselect;
			
			if (tableselect[0] == undefined) return false;
			
			var url = "{{ route('admin.user.delete') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				tableselect: tableselect
			})
			.then(function (response) {
				if (response.data) {
					_this.success(false, '成功', '删除成功！');
					_this.delete_disabled_user = true;
					_this.tableselect = [];
					_this.usergets(_this.page_current, _this.page_last);
				} else {
					_this.error(false, '失败', '删除失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除失败！');
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
			// cfg_data['FILTERS_USER_LOGINTIME_DATEFROM'] = _this.queryfilter_datefrom;
			// cfg_data['FILTERS_USER_LOGINTIME_DATETO'] = _this.queryfilter_dateto;

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
		_this.current_nav = '权限管理';
		_this.current_subnav = 'User';
		// 显示所有user
		_this.usergets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection