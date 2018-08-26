@extends('admin.layouts.adminbase')

@section('my_title')
Admin(template) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div>

	<Divider orientation="left">Template Management</Divider>

	<Tabs type="card" v-model="currenttabs">
		<Tab-pane label="Template List">
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
var vm_app = new Vue({
    el: '#app',
    data: {
		current_nav: '',
		current_subnav: '',
		
		sideractivename: '2-1-3',
		sideropennames: ['2', '2-1'],

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
				title: 'created_at',
				key: 'created_at',
			},
			{
				title: 'updated_at',
				key: 'updated_at',
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
		page_size: {{ $config['PERPAGE_RECORDS_FOR_TEMPLATE'] }},
		page_last: 1,		
		
		// 创建ID
		template_add_id: '',
		// 创建名称
		template_add_name: '',

		// tabs索引
		currenttabs: 0,		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

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
			cfg_data['PERPAGE_RECORDS_FOR_TEMPLATE'] = pagesize;
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
		
		// template列表
		templategets: function(page, last_page){
			var _this = this;
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}

			_this.loadingbarstart();
			var url = "{{ route('admin.template.templategets') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page
				}
			})
			.then(function (response) {
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
		
		onreset: function () {
			this.template_add_id = '';
			this.template_add_name = '';
		},
		
		// 创建或更新template
		templatecreateorupdate: function (createorupdate) {
			var _this = this;
			var postdata = {};
			postdata['id'] = _this.template_add_id;
			postdata['name'] = _this.template_add_name;
			postdata['createorupdate'] = createorupdate;
			
			if(postdata['name'].length==0){
				_this.error(false, 'Error', 'Please input the template name!');
				return false;
			}

			var url = "{{ route('admin.template.createorupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				postdata: postdata
			})
			.then(function (response) {
				// console.log(response);
				if (response.data != 1) {
					_this.error(false, 'Error', 'template failed to ' + createorupdate + ' !');
				} else {
					_this.success(false, 'Success', 'template ' + createorupdate + ' successfully!');

					if (createorupdate=='create') {_this.onreset()}
				}
				// 刷新
				_this.templategets(_this.page_current, _this.last_page);
				
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'Error! template failed to ' + createorupdate + ' !');
			})
		},

		// 显示当前template并切换到编辑界面
		template_detail: function (row) {
			var _this = this;
			
			_this.template_add_id = row.id;
			_this.template_add_name = row.name;

			// 切换到第二个面板
			_this.currenttabs = 1;
		},

		// 删除template
		template_delete: function (id) {
			var _this = this;
			
			if (id == undefined) {
				_this.error(false, 'Error', 'Please select the template(s)!');
				return false;
			}
			
			var url = "{{ route('admin.template.templatedelete') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				id: id
			})
			.then(function (response) {
				if (response.data == undefined) {
					_this.warning(false, 'Warning', 'Template(s) failed to delete!');
				} else {
					_this.success(false, 'Success', 'Template(s) deleted successfully!');
					
					// 刷新
					_this.templategets(_this.page_current, _this.last_page);
				}

			})
			.catch(function (error) {
				_this.error(false, 'Error', error.response.data.message);
			})
		},






	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '基本元素 - Template';
		// 显示所有template
		_this.templategets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection