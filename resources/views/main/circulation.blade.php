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

<Tag type="dot" @click.native="homeclick()">首页</Tag>
<Tag v-for="(item, index) in tagcount" type="dot" closable @on-close="tagclose(index)" @click.native="tagclick(item.id)">@{{ item.name }}</Tag>

@endsection

@section('my_body')
@parent

<span v-if="showtable">
<i-table height="360" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
<br>
<Page :current="pagecurrent" :total="pagetotal" :page-size="pagepagesize" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" size="small" show-total show-elevator show-sizer :page-size-opts="pagesizeopts"></Page>
</span>

<span v-if="showcirculation">
<Collapse v-model="valuecollapsemain">
	<Panel name="c1">
		Circulation Info
		<div slot="content">
			<i-table :columns="infocolumns" :data="infodata" size="small"></i-table>
		</div>
	</Panel>
	<Panel name="c2">
		Slot Users
		<div slot="content">
			<!--<i-table :columns="usercolumns" :data="userdata" size="small"></i-table>-->
			<span v-for="item in userdata">
			<i-table :columns="usercolumns" :data="item" size="small"></i-table>
			</span>
			
			<!--<div class="col-lg-12">
				<div class="col-lg-3">
					<label>用户名</label>
				</div>
				<div class="col-lg-3">
					<label>代理人</label>
				</div>
				<div class="col-lg-3">
					<label>邮箱</label>
				</div>
				<div class="col-lg-3">
					<label>操作</label>
				</div>
			</div>
			
			<div class="col-lg-12" v-for="(value, key) in gets_review_users">
					<div class="col-lg-12">
						<p></p><p><i class="fa fa-user fa-fw"></i><strong>Step @{{ parseInt(key)+1 }}</strong></p>
					</div>
				
				<div v-for="(val, k) in value">
					<div class="col-lg-3">
						<p>@{{ val.name }}</p>
					</div>
					<div class="col-lg-3">
					
						<multi-select v-model="select_substitute_review[key][k]" :options="options_substitute_review[key][k]" :limit="1" placeholder="Substitute" filterable collapse-selected size="sm"/>
					
					</div>
					<div class="col-lg-3">
						<p>@{{ val.email }}</p>
					</div>
					<div class="col-lg-3">
						<p>
							<btn type="link" size="xs"><i class="fa fa-envelope fa-fw"></i></btn>&nbsp;
							<btn type="link" size="xs"><i class="fa fa-mail-forward fa-fw"></i></btn>&nbsp;
							<btn type="link" size="xs"><i class="fa fa-group fa-fw"></i></btn>&nbsp;
							<btn type="link" size="xs"><i class="fa fa-send fa-fw"></i></btn>&nbsp;
						</p>
					</div>
				</div>
				<div style="background-color:#c9e2b3;height:1px"></div><p></p>
			</div>-->
			
			
			
			
		</div>
	</Panel>
	<Panel name="c3">
		Slot Fields
		<div slot="content">
		
			<Collapse v-model="valuecollapsefield">
				<Panel name="c1">
					Circulation field
					<p slot="content">

					</p>
				</Panel>
			</Collapse>
		
		</div>
	</Panel>
</Collapse>
</span>

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
									// vm_app.viewcirculation(params.index)
									vm_app.viewcirculation(params.row.id, params.row.name)
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
									vm_app.removecirculation(params.row.id)
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
		
		// circulation info
		infocolumns: [
			{
				title: '流程名称',
				key: 'name'
			},
			{
				title: 'GUID',
				key: 'guid'
			},
			{
				title: '创建日期',
				key: 'created_at'
			},
			{
				title: '创建者',
				key: 'creator'
			},
			{
				title: '详细描述',
				key: 'description'
			}
		],
		infodata: [
			// {
				// name: 'John Brown',
				// guid: 18,
				// created_at: 'New York No. 1 Lake Park',
				// creator: '2016-10-03',
				// description: '2016-10-03'
			// }
		],

		// circulation user
		usercolumns: [
			{
				title: '用户',
				key: 'name'
			},
			{
				title: '代理人',
				key: 'substitute'
			},
			{
				title: '邮箱',
				key: 'email'
			}
		],
		// usercolumns: [
			// {
				// title: 'Slot1',
				// key: 'slot1',
				// align: 'center',
				// children: [
					// {
						// title: '用户',
						// key: 'name'
					// },
					// {
						// title: '代理人',
						// key: 'substitute'
					// },
					// {
						// title: '邮箱',
						// key: 'email'
					// }
				// ]
			// }
		// ],
		userdata: [
			{
				name: 'John Brown',
				substitute: 18,
				email: 'New York No. 1 Lake Park'
			}
		],
		
		// 页码
		pagecurrent: 1,
		pagetotal: 1,
		pagepagesize: {{ $config['PERPAGE_RECORDS_FOR_CIRCULATION'] }},
		pagesizeopts: [1, 5, 10, 20],
		
		// 显示表格或预览流程
		showtable: false,
		showcirculation: true,
		
		// tag
		tagcount: [],
		
		// 折叠面板
		valuecollapsemain: ['c1', 'c2', 'c3'],
		valuecollapseuser: ['c1'],
		valuecollapsefield: ['c1'],

		
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
		
		// 下拉菜单（user）
		dropdownuser: function (text) {
			if (text == '') {
				return false;
			} else if (text == "{{$user['name']}}") {
				alert("{{$user['name']}}");
			} else if (text == "Home") {
				window.location.href = "{{route('main.circulation.index')}}";
			} else if (text == "Logout") {
				window.location.href = "{{route('admin.logout')}}";
			}
			
		},
		
		// 查看流程
		viewcirculation: function (id, name) {
			// console.log(id);
			var _this = this;
			
			// 显示tag
			_this.tagcount.push({id: id, name: name+' [ID: '+id+']'});
			_this.tagclick(id);
		},
		
		// 删除流程
		removecirculation: function (id) {
			
		},
		
		// 关闭Tag
		tagclose: function (index) {
			this.tagcount.splice(index, 1);
		},
		
		// click tag
		tagclick: function (id) {
			this.showtable = false;
			this.showcirculation = true;
			
			this.review_circulation(id);
		},
		
		// click home
		homeclick: function () {
			this.showtable = true;
			this.showcirculation = false;
		},
		
		// 读取流程内容
		review_circulation: function (id) {
			var _this = this;

			if (id == undefined) {
				// _this.gets_review_peoples = {};
				// _this.gets_review_fields = {};
				return false;
			}
			
			// alert('here is ' + id);
			// return false;
			
			var url = "{{ route('main.circulation.reviewcirculation') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					id: id
				}
			})
			.then(function (response) {
				// console.log(response.data.slotdata);
				// info
				// _this.infodata = [response.data.infodata];
				
				// user
				// _this.usercolumns = [];
				_this.userdata = {};
				var slotdata = response.data.slotdata;
				for (var i in slotdata) {
					
					console.log(slotdata[i]['slot']['name']);
					// _this.usercolumns.push(
						// {
							// title: 'slotx',
							// key: 'slot1',
							// align: 'center',
							// children: [
								// {title: '用户', key: 'name'},
								// {title: '代理人', key: 'substitute'},
								// {title: '邮箱', key: 'email'}
							// ]
						// }
					// );
					

					_this.$set(_this.userdata, i, slotdata[i]['user']);
					console.log(_this.userdata);
					// for (var i in slotdata[item]['user']) {
						// console.log(slotdata[item]['user'][i]);
						// _this.userdata.push(slotdata[item]['user'][i]); 
						// _this.userdata = slotdata[item]['user']; 
					// }
				}
				
				
				// field
				
				
				return false;
				// if (typeof(response.data.data) == "undefined") {
					// _this.alert_exit();
				// }

				_this.review_guid = response.data.circulation.guid;
				_this.review_template = response.data.circulation.name;
				_this.review_created_at = response.data.circulation.created_at;
				_this.review_creator = response.data.circulation.creator;
				_this.review_description = response.data.circulation.description;
				_this.review_current_user = response.data.circulation.current_station;
				_this.review_current_slot = response.data.circulation.slot_id;
				
				
				// _this.gets_review_peoples = response.data.userinfo;
				// _this.gets_review_fields = response.data.field;
				
				// return false;
				var json = '';
				for (i in response.data.slot) {
					_this.$set(_this.gets_review_peoples, i, response.data.slot[i]['user']);
					
					// _this.select_substitute_create[i] = [];
					// _this.options_substitute_create[i] = [];
					
					for (j in _this.gets_review_peoples[i]) {
						_this.$set(_this.select_substitute_review[i], j, []);
						if (_this.gets_review_peoples[i][j]['substitute'] != null) {
							json = _this.gets_review_peoples[i][j]['substitute'];
							_this.$set(_this.options_substitute_review[i], j, JSON.parse(json));
						} else {
							_this.$set(_this.options_substitute_review[i], j, []);
						}
					}
					// _this.$set(_this.options_substitute_create, i, _this.json2selectvalue(json, true));
					
					_this.$set(_this.gets_review_fields, i, response.data.slot[i]['slot']);
				}				
				
				
				
				
				
				
				
				
				

				// 动态设定slot收放变量，直接使用gets_create_fields绑定v-model吧
				for (var index in _this.gets_review_fields) {
					_this.$set(_this.show_review_slot, index, {'slot_id': true});
				}

			})
			.catch(function (error) {
				console.log(error);
			})
		},


		
	},
	mounted: function () {
		var _this = this;
		_this.circulationgets(1, 1); // page: 1, last_page: 1
		


	}
})
</script>
@endsection