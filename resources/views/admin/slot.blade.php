@extends('admin.layouts.adminbase')

@section('my_title')
Admin(slot) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent

<div>

	<Divider orientation="left">Slot Management</Divider>

	<Tabs type="card" v-model="currenttabs">
		<Tab-pane label="Slot List">
			<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
			<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
		</Tab-pane>

		<Tab-pane label="Create/Edit Field">
		
			<i-row>
				<i-col span="6">
					<Card>
						<p slot="title">新建/编辑SLOT</p>
						<p>
							<input v-model="slot_add_id" type="hidden">
							* 名称<br>
							<i-input v-model="slot_add_name" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<i-button type="primary" @click="slotcreateorupdate('create')">Create</i-button>&nbsp;&nbsp;
						<i-button type="primary" @click="slotcreateorupdate('update')">Update</i-button>&nbsp;&nbsp;
						<i-button @click="onreset()">Reset</i-button>
					</Card>
				</i-col>
				<i-col span="18">
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
var vm_slot = new Vue({
    el: '#app',
    data: {
		current_nav: '',
		current_subnav: '',
		
		sideractivename: '2-1-1',
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
									vm_app.field_detail(params.row)
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
									vm_app.field_delete(params.row.id)
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
		page_size: {{ $config['PERPAGE_RECORDS_FOR_SLOT'] }},
		page_last: 1,		
		
		// 创建ID
		slot_add_id: '',
		// 创建名称
		slot_add_name: '',

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
		// 切换当前页
		oncurrentpagechange: function (currentpage) {
			this.slotgets(currentpage, this.pagelast);
		},
		// 切换页记录数
		onpagesizechange: function (pagesize) {
			
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_SLOT'] = pagesize;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.page_size = pagesize;
					_this.slotgets(1, _this.page_last);
				} else {
					_this.warning(false, 'Warning', 'failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'failed!');
			})
		},		
		
		// slot列表
		slotgets: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.slot.slotgets') }}";
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.loadingbarstart();
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
		
		
		
		
		
		
		
		
		
		
		
		
		
		// 显示当前slot并切换到编辑界面
		slot_detail: function (index) {
			var _this = this;
			
			_this.slot_add_id = _this.gets.data[index].id;
			_this.slot_add_name = _this.gets.data[index].name;

			// 切换到第二个面板
			_this.currenttabs = 1;
		},
		slotreset: function () {
			this.slot_add_id = '';
			this.slot_add_name = '';
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
		// 创建或更新slot
		slotcreateorupdate: function (createorupdate) {
			var _this = this;
			var postdata = {};
			postdata['id'] = _this.slot_add_id;
			postdata['name'] = _this.slot_add_name;
			postdata['createorupdate'] = createorupdate;
			
			if(postdata['name'].length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please input the slot name!';
				_this.notification_message();
				return false;
			}

			var url = "{{ route('admin.slot.createorupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					postdata: postdata
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'slot failed to ' + createorupdate + ' !';
					_this.notification_message();
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'slot ' + createorupdate + ' successfully!';
					_this.notification_message();

					if (createorupdate=='create') {_this.slotreset()}

				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				// _this.notification_content = error.response.data.message;
					_this.notification_content = 'Error! slot failed to ' + createorupdate + ' !';
				_this.notification_message();
			})
		},
		// 删除slot
		slot_delete: function (id) {
			var _this = this;
			
			if (id == undefined) {
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please select the slot(s)!';
				_this.notification_message();
				return false;
			}
			
			var url = "{{ route('admin.slot.slotdelete') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					id: id
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'slot(s) failed to delete!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'slot(s) deleted successfully!';
					_this.notification_message();
					
					// 刷新
					_this.slotgets(_this.current_page, _this.last_page);
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},

		configperpageforslot: function (value) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_SLOT'] = value;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.perpage = value;
					_this.slotgets(1, 1);
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
		_this.current_nav = '元素管理';
		_this.current_subnav = '基本元素 - Slot';
		// 显示所有slot
		_this.slotgets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection