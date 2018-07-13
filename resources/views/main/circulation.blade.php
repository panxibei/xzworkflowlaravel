@extends('main.layouts.mainbase')

@section('my_title')
Main(circulation) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_tag')
@parent
<Tag type="dot" closable>标签三</Tag>
@endsection

@section('my_body')
@parent

<!--<Spin size="large" v-if="spinShow"></Spin>-->
<i-table height="360" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
<br>
<Page :current="pagecurrent" :total="pagetotal" :page-size="pagepagesize" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" size="small" show-total show-elevator show-sizer :page-size-opts="pagesizeopts"></Page>
@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
<script>
var vm_app = new Vue({
    el: '#app',
    data: {
		// 左侧导航
		sideractivename: '2-1',
		sideropennames: "['2']",
		
		//
		// spinShow: true,
		
		// 表格
		tablecolumns: [
			{
				title: 'ID',
				key: 'id',
				// sortable: true,
				width: 64
			},
			{
				title: 'Name',
				key: 'name',
				sortable: true
			},
			{
				title: 'Current',
				key: 'currentstation',
				sortable: true,
				width: 100,
				render: (h, params) => {
					return h('div', [
						h('Icon', {
							props: {
								type: 'person'
							}
						}),
						h('strong', params.row.currentstation)
					]);
				}
			},
			{
				title: 'Sending Date',
				key: 'sendingdate',
				sortable: true,
				width: 160
			},
			{
				title: 'Process',
				key: 'sendingdate',
				sortable: true,
				width: 110,
				render: (h, params) => {
					return h('div', 
						// params.row.sendingdate + 'xxx'
						new Date().diff(params.row.sendingdate) + ' day(s)'
					)
				}
			},
			{
				title: 'Creator',
				key: 'creator',
				sortable: true,
				width: 96,
				render: (h, params) => {
					return h('div', [
						h('Icon', {
							props: {
								type: 'person'
							}
						}),
						h('span', params.row.currentstation)
					]);
				}
			},
			{
				title: 'Progress',
				key: 'progress',
				sortable: true,
				width: 130,
				render: (h, params) => {
					return h('i-progress', {
						props: {
							percent: params.row.progress,
							status: 'active',
							// 'hide-info': '',
							'stroke-width': '5'
						}
					});
				}
			},
			
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 170,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'primary',
								size: 'small',
								icon: 'eye'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.showperson(params.index)
								}
							}
						}, 'View'),
						h('Button', {
							props: {
								type: 'error',
								size: 'small',
								icon: 'trash-a'
							},
							on: {
								click: () => {
									vm_app.removeperson(params.index)
								}
							}
						}, 'Delete')
					]);
				}
			}
		],
		tabledata: [
			// {
				// id: '1',
				// name: 'John Brown',
				// currentstation: 'user1',
				// sendingdate: '2018-07-12 01:01:01',
				// daysinprocess: '36',
				// creator: 'admin',
				// progress: '10%'
			// },
			// {
				// id: '2',
				// name: 'John Brown2',
				// currentstation: 'user2',
				// sendingdate: '2018-07-11 01:01:01',
				// daysinprocess: '36',
				// creator: 'admin',
				// progress: '10%'
			// }
		],
		// 页码
		pagecurrent: 1,
		pagetotal: 1,
		pagepagesize: {{ $config['PERPAGE_RECORDS_FOR_CIRCULATION'] }},
		pagesizeopts: [1, 5, 10, 20],

		
	},
	methods: {
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
		
		// 切换每页条数
		onpagesizechange: function (pagesize) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_CIRCULATION'] = pagesize;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.pagepagesize = pagesize;
					_this.circulationgets(1, 1);
				} else {
					_this.warning(false, '更新失败', '请确保网络通畅，并稍后再试！');
				}
			})
			.catch(function (error) {
				_this.error(false, '更新失败', '请确保网络通畅，并稍后再试！');
			})			
		},
		
		// 切换当前页
		oncurrentpagechange: function (currentpage) {
			this.circulationgets(currentpage, this.pagetotal);
		},

		// circulation列表 ok
		circulationgets: function(page, last_page){
			var _this = this;
			var url = "{{ route('main.circulation.circulationgets') }}";
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			
			_this.loadingbarstart();
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.pagepagesize,
					page: page
				}
			})
			.then(function (response) {
				// return false;
				// if (typeof(response.data.data) == "undefined") {
					// _this.alert_exit();
				// }
				// _this.spinShow = !_this.spinShow;
				
				_this.pagecurrent = response.data.current_page;
				_this.pagetotal = response.data.last_page;
				
				_this.tabledata = response.data.data;
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
				_this.loadingbarerror();
			})
		},
		dropdownuser: function (text) {
			alert(text);
			if (text == 'admin') {
				alert('xxx'+text);
			} else if () {
				
			}
			
		}
		
	},
	mounted: function () {
		var _this = this;
		_this.circulationgets(1, 1); // page: 1, last_page: 1
		
	}
})
</script>
@endsection