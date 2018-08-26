@extends('admin.layouts.adminbase')

@section('my_title')
Admin(Mailinglist) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div>

	<Divider orientation="left">Mailinglist Management</Divider>

	<Tabs type="card" v-model="currenttabs">
		<Tab-pane label="Mailinglist List">
			<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
			<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
		</Tab-pane>

		<Tab-pane label="Create/Edit Mailinglist">
		
			<i-row>
				<i-col span="8">
					<Card>
						<p slot="title">新建/编辑 MAILINGLIST</p>
						<p>
							<input v-model="mailinglist_add_id" type="hidden">
							* 名称<br>
							<i-input v-model="mailinglist_add_name" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							<input v-model="mailinglist_add_template_id" type="hidden">
							* Template<br>
							<i-input v-model="mailinglist_add_template_name" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<i-button type="primary" @click="createmailinglist()">Create</i-button>&nbsp;&nbsp;
						<i-button type="primary" @click="updatemailinglist()">Update</i-button>&nbsp;&nbsp;
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
		
		sideractivename: '2-3-1',
		sideropennames: ['2', '2-3'],

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
				title: 'template_id',
				key: 'template_id',
				width: 80
			},
			{
				title: 'template_name',
				key: 'template_name'
			},
			{
				title: 'isdefault',
				key: 'isdefault',
				width: 60,
				render: (h, params) => {
					return h('div', [
						params.row.isdefault==1?'★':''
					]);
				}
			},
			{
				title: 'slot2user_id',
				key: 'slot2user_id',
				width: 80
			},
			{
				title: 'created_at',
				key: 'created_at',
				width: 100
			},
			{
				title: 'updated_at',
				key: 'updated_at',
				width: 100
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 160,
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
									vm_app.mailinglist_detail(params.row)
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
									vm_app.mailinglist_delete(params.row.id)
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
		page_size: {{ $config['PERPAGE_RECORDS_FOR_MAILINGLIST'] }},
		page_last: 1,		
		
		// 创建ID
		mailinglist_add_id: '',
		// 创建名称
		mailinglist_add_name: '',

		// 创建ID
		mailinglist_add_template_id: '',
		// 创建名称
		mailinglist_add_template_name: '',		
		
		// tabs索引
		currenttabs: 0,
		
		// 查询过滤器
		queryfilter_name: "{{ $config['FILTERS_USER_NAME'] }}",
		queryfilter_email: "{{ $config['FILTERS_USER_EMAIL'] }}",
		queryfilter_datefrom: "{{ $config['FILTERS_USER_LOGINTIME_DATEFROM'] }}" || null,
		queryfilter_dateto: "{{ $config['FILTERS_USER_LOGINTIME_DATETO'] }}" || null,
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		show_progress: true,
		progress: 100,
		show_table: false,
		// show_update: false,
		gets: {},
		// perpage: {{ $config['PERPAGE_RECORDS_FOR_MAILINGLIST'] }},
		// 编辑时值
		edit_id: '',
		edit_name: '',
		currentmailinglist_name: '',
		// currentmailinglistpassword: '',
		// 创建
		show_createmailinglist: false,
		createmailinglist_name: '',
		createmailinglist_templateid: '',
		selected_create_template: [],
		options_create_template: [],
		selected_edit_template: [],
		options_edit_template: [],
		// 编辑
		show_editmailinglist: false,
		editmailinglist_name: '',
		editmailinglist_email: '',
		// 查询
		open_querymailinglist: false,
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
			this.mailinglistlist(currentpage, this.page_last);
		},
		// 切换页记录数
		onpagesizechange: function (pagesize) {
			
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_MAILINGLIST'] = pagesize;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.page_size = pagesize;
					_this.mailinglistlist(1, _this.page_last);
				} else {
					_this.warning(false, 'Warning', 'failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},
		
		mailinglistlist: function(page, last_page){
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

			_this.loadingbarstart();
			var url = "{{ route('admin.mailinglist.list') }}";
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
		
		// 创建mailinglist
		createmailinglist: function () {
			var _this = this;
			var name = _this.mailinglist_add_name;
			var templateid = _this.selected_create_template[0];

			if ( name.length == 0 || templateid.length == 0) {return false;}

			var url = "{{ route('admin.mailinglist.create') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				name: name,
				templateid: templateid
			})
			.then(function (response) {
				if (response.data) {
					_this.$notify('Mailinglist created successfully!');
					_this.createmailinglist_name = '';
					_this.selected_create_template = [];
					_this.mailinglistlist(_this.gets.current_page, _this.gets.last_page);
				} else {
					_this.$notify('Mailinglist created failed!');
				}
			})
			.catch(function (error) {
				_this.$notify('Error! Mailinglist created failed!');
				// console.log(error);
			})
		},
		
		// 更新mailinglist
		updatemailinglist: function () {
			var _this = this;
			// console.log(_this.edit_id);
			// console.log(_this.edit_name);
			// console.log(_this.selected_edit_template[0]);
			// return false;
			
			var id = _this.edit_id;
			var name = _this.edit_name;
			var template_id = _this.selected_edit_template[0];
			
			if (id == undefined || name == undefined || name.length == 0 || template_id == undefined) {
				_this.$notify('Values are incorrect!');
				return false;
			}

			var url = "{{ route('admin.mailinglist.edit') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				id: id,
				name: name,
				template_id: template_id
			})
			.then(function (response) {
				if (response.data) {
					_this.show_editmailinglist = false;
					_this.$notify('Mailinglist updated successfully!');
					_this.mailinglistlist(_this.gets.current_page, _this.gets.last_page);
				} else {
					_this.$notify('Mailinglist updated failed!');
				}
			})
			.catch(function (error) {
				_this.$notify('Error! Mailinglist updated failed!');
				// console.log(error);
			})
		},

		
		// 显示当前template并切换到编辑界面
		mailinglist_detail: function (row) {
			var _this = this;
			
			_this.mailinglist_add_id = row.id;
			_this.mailinglist_add_name = row.name;

			// 切换到第二个面板
			_this.currenttabs = 1;
		},

		// 删除template
		mailinglist_delete: function (id) {
			var _this = this;
			
			if (id == undefined) {
				_this.error(false, 'Error', 'Please select the mailinglist(s)!');
				return false;
			}
			
			var url = "{{ route('admin.mailinglist.delete') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				id: id
			})
			.then(function (response) {
				if (response.data == undefined) {
					_this.warning(false, 'Warning', 'Mailinglist(s) failed to delete!');
				} else {
					_this.success(false, 'Success', 'Mailinglist(s) deleted successfully!');
					
					// 刷新
					_this.mailinglistlist(_this.page_current, _this.page_last);
				}

			})
			.catch(function (error) {
				// _this.error(false, 'Error', error.response.data.message);
				_this.error(false, 'Error', error);
			})
		},
		
		onreset: function () {
			this.mailinglist_add_id = '';
			this.mailinglist_add_name = '';
		},

		
		
		
		
		
		
		
		
		
		
		
		
		
		

	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '用户关联 - Mailinglist';
		_this.mailinglistlist(1, 1);
		// _this.load_templateid();
	}
});
</script>
@endsection