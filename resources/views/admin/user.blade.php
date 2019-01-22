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
			<i-col span="3">
				<i-button @click="ondelete_user()" :disabled="delete_disabled_user" type="warning" size="small">Delete</i-button>&nbsp;<br>&nbsp;
			</i-col>
			<i-col span="2">
				<i-button type="default" size="small" @click="oncreate_user()"><Icon type="ios-color-wand-outline"></Icon> 新建用户</i-button>
			</i-col>
			<i-col span="2">
				<i-button type="default" size="small" @click="onexport_user()"><Icon type="ios-download-outline"></Icon> 导出用户</i-button>
			</i-col>
			<i-col span="17">
				&nbsp;
			</i-col>
		</i-row>
		
		<i-row :gutter="16">
			<i-col span="24">
	
				<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata" @on-selection-change="selection => onselectchange(selection)"></i-table>
				<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
			
				<Modal v-model="modal_user_add" @on-ok="oncreate_user_ok" ok-text="新建" title="Create - User" width="420">
					<div style="text-align:left">
						
						<p>
							name&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_name" placeholder="" size="small" clearable style="width: 120px"></i-input>

							&nbsp;&nbsp;&nbsp;&nbsp;

							email&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_email" placeholder="" size="small" clearable style="width: 120px" type="email"></i-input>
							
							<br><br>

							password&nbsp;&nbsp;
							<i-input v-model.lazy="user_add_password" placeholder="" size="small" clearable style="width: 120px" type="password"></i-input>
							&nbsp;*默认密码为12345678

						</p>
						
						&nbsp;
					
					</div>	
				</Modal>
				
				<Modal v-model="modal_user_edit" @on-ok="user_edit_ok" ok-text="保存" title="Edit - User" width="420">
					<div style="text-align:left">
						
						<p>
							name&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_name" placeholder="" size="small" clearable style="width: 120px"></i-input>

							&nbsp;&nbsp;&nbsp;&nbsp;

							email&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_email" placeholder="" size="small" clearable style="width: 120px" type="email"></i-input>
							
							<br><br>

							password&nbsp;&nbsp;
							<i-input v-model.lazy="user_edit_password" placeholder="不修改密码请留空" size="small" clearable style="width: 120px" type="password"></i-input>

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











@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
@parent
<script>
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
						// params.row.deleted_at ? '禁用' : '启用'
						
						h('i-switch', {
							props: {
								type: 'primary',
								size: 'small',
								value: ! params.row.deleted_at
							},
							style: {
								marginRight: '5px'
							},
							on: {
								'on-change': (value) => {//触发事件是on-change,用双引号括起来，
									//参数value是回调值，并没有使用到
									vm_app.trash_user(params.row.id) //params.index是拿到table的行序列，可以取到对应的表格值
								}
							}
						}, 'Edit')
						
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
		modal_user_add: false,
		user_add_id: '',
		user_add_name: '',
		user_add_email: '',
		user_add_password: '',
		
		// 编辑
		modal_user_edit: false,
		user_edit_id: '',
		user_edit_name: '',
		user_edit_email: '',
		user_edit_password: '',
		
		// 删除
		delete_disabled_user: true,

		// tabs索引
		currenttabs: 0,
		
		// 查询过滤器
		queryfilter_name: "{{ $config['FILTERS_USER_NAME'] }}",
		queryfilter_email: "{{ $config['FILTERS_USER_EMAIL'] }}",
		queryfilter_logintime: "{{ $config['FILTERS_USER_LOGINTIME'] }}" || [],
		queryfilter_loginip: "{{ $config['FILTERS_USER_LOGINIP'] }}",
		
		// 查询过滤器下拉
		collapse_query: '',
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
					// _this.tabledata = [];
					// return false;
					queryfilter_logintime = ['1970-01-01', '9999-12-31'];
					break;
				} else {
					queryfilter_logintime.push(_this.queryfilter_logintime[i]);
				}
			}
			// console.log(queryfilter_logintime);

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

				// if (response.data.length == 0 || response.data.data == undefined) {
					// _this.alert_exit();
				// }
				
				if (response.data) {
					_this.delete_disabled_user = true;
					_this.tableselect = [];

					_this.page_current = response.data.current_page;
					_this.page_total = response.data.total;
					_this.page_last = response.data.last_page;
					_this.tabledata = response.data.data;
				} else {
					_this.alert_exit();
				}
				
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
			// _this.user_edit_password = row.password;
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
			var password = _this.user_edit_password;
			// var created_at = _this.relation_created_at_edit;
			// var updated_at = _this.relation_updated_at_edit;
			
			if (name == '' || name == null || name == undefined
				|| email == '' || email == null || email == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(email)) {
				_this.warning(false, 'Warning', 'Email is incorrect!');
				return false;
			}
			
			var url = "{{ route('admin.user.update') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id: id,
				name: name,
				email: email,
				password: password,
				// xuqiushuliang: xuqiushuliang[1],
				// created_at: created_at,
				// updated_at: updated_at
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;
				
				_this.usergets(_this.page_current, _this.page_last);
				
				if (response.data) {
					_this.success(false, '成功', '更新成功！');
					
					_this.user_edit_id = '';
					_this.user_edit_name = '';
					_this.user_edit_email = '';
					_this.user_edit_password = '';
					
					// _this.relation_xuqiushuliang_edit = [0, 0];
					// _this.relation_created_at_edit = '';
					// _this.relation_updated_at_edit = '';
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
					_this.usergets(_this.page_current, _this.page_last);
					_this.success(false, '成功', '删除成功！');
				} else {
					_this.error(false, '失败', '删除失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '删除失败！');
			})
		},
		
		trash_user: function (userid) {
			var _this = this;
			
			if (userid == undefined || userid.length == 0) return false;
			var url = "{{ route('admin.user.trash') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				userid: userid
			})
			.then(function (response) {
				if (response.data) {
					_this.success(false, '成功', 'User 禁用/启用 successfully!');
					_this.usergets(_this.page_current, _this.page_last);
				} else {
					_this.error(false, '失败', '禁用/启用失败！');
				}
			})
			.catch(function (error) {
				_this.error(false, '错误', '禁用/启用失败！');
			})			
		},
		
		// 显示新建用户
		oncreate_user: function () {
			// 默认密码为12345678
			this.user_add_password = '12345678';
			this.modal_user_add = true;
		},
		
		// 新建用户
		oncreate_user_ok: function () {
			var _this = this;
			var name = _this.user_add_name;
			var email = _this.user_add_email;
			var password = _this.user_add_password;
			
			if (name == '' || name == null || name == undefined
				|| email == '' || email == null || email == undefined
				|| password == '' || password == null || password == undefined) {
				_this.warning(false, '警告', '内容不能为空！');
				return false;
			}
			
			// var re = new RegExp(“a”);  //RegExp对象。参数就是我们想要制定的规则。有一种情况必须用这种方式，下面会提到。
			// var re = /a/;   // 简写方法 推荐使用 性能更好  不能为空 不然以为是注释 ，
			var regexp = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
			if (! regexp.test(email)) {
				_this.warning(false, 'Warning', 'Email is incorrect!');
				return false;
			}

			var url = "{{ route('admin.user.create') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				name: name,
				email: email,
				password: password
			})
			.then(function (response) {
				if (response.data) {
					_this.success(false, 'Success', 'User created successfully!');
					_this.user_add_name = '';
					_this.user_add_email = '';
					_this.user_add_password = '';
					_this.usergets(_this.page_current, _this.page_last);
				} else {
					_this.error(false, 'Warning', 'User created failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'User created failed!');
			})
		},		
		
		// 导出用户
		onexport_user: function(){
			var url = "{{ route('admin.user.excelexport') }}";
			window.setTimeout(function(){
				window.location.href = url;
			}, 1000);
			return false;
		},
		
		
		
		
		
		
		
		


	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '权限管理';
		_this.current_subnav = '用户';
		// 显示所有user
		_this.usergets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection