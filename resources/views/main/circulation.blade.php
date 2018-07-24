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
			<!--<i-table :show-header="showHeader" :columns="usercolumns" :data="userdata" size="small"></i-table>-->
			
			<i-row>
				<i-col span="4">步骤</i-col>
				<i-col span="5">用户</i-col>
				<i-col span="5">代理人</i-col>
				<i-col span="5">邮箱</i-col>
				<i-col span="5">操作</i-col>
			</i-row>
			
			
			<div v-for="(value, key) in gets_review_users">
				<i-row>
					<i-col span="24">
						<br><div style="background-color:#c9e2b3;height:1px"></div>
						<i class="fa fa-user fa-fw"></i><strong>Step @{{ parseInt(key)+1 }}</strong>
					</i-col>
				</i-row>
				
				<i-row v-for="(val, k) in value">
					<i-col span="4">
						&nbsp;
					</i-col>
					<i-col span="5">
						<Icon type="ios-person"></Icon> @{{ val.name }}
					</i-col>
					<i-col span="5">
						<span @click="getsubstitute(val.id)"><a href="javascript:;"><Icon type="ios-eye"></Icon> 查看</a></span>
						<Modal title="Substitute" v-model="substitute_modal" @on-ok="substitute_ok()" class-name="vertical-center-modal" width="200">
							<p>
							转送至代理人：
							<Checkbox-group v-model="substitute_checkbox">
								<span v-for="item in substitute_user">
								<Checkbox :label="item.name"></Checkbox><br>
								</span>
							</Checkbox-group>
							</p>
						</Modal>
					</i-col>
					<i-col span="5">
						<Icon type="ios-email"></Icon> @{{ val.email }}
					</i-col>
					<i-col span="5">
						<Icon type="ios-email-outline" size="18"></Icon>&nbsp;&nbsp;
						<Icon type="ios-redo-outline" size="18"></Icon>&nbsp;&nbsp;
						<Icon type="ios-people-outline" size="18"></Icon>&nbsp;&nbsp;
						<Icon type="ios-paperplane-outline" size="18"></Icon>&nbsp;&nbsp;
					</i-col>
				</i-row>
			</div>
			<br>&nbsp;
			
		</div>
	</Panel>
	<Panel name="c3">
		Slot Fields
		<div slot="content">
		
			<!--slot，有field时显示，否则显示空的slot-->
			<Collapse v-model="valuecollapsefield[key]" v-for="(value, key) in gets_review_fields" v-if="value.field[0]!=null">
				<Panel :name="valuecollapsefieldname[key]">
					@{{ value.name }}
					<div slot="content">

					<i-row :gutter="16">
					<span v-for="(val, k) in value.field">
					<i-col span="6">
					
						<div>
							<!--1-Text-->
							<div v-if="val.type=='1-Text'" style="height: 128px">
								<strong>@{{val.name||'未命名'}}</strong><br>
								<!--<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :value="val.value" :placeholder="val.placeholder">-->
								<!--<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" v-model.lazy="val.value" :placeholder="val.placeholder">-->
								<!--<i-input v-model.lazy="val.value" size="small" placeholder="small size" clearable :style="{background: val.bgcolor}" style="width: 200px" :readonly="val.readonly||false" placeholder="val.placeholder"></i-input>-->
								<i-input v-model.lazy="formItem.input[key+'_'+k]" size="small" clearable :style="{background: val.bgcolor}" style="width: 200px" :readonly="val.readonly||false" :placeholder="val.placeholder"></i-input>
								<p style="color: #80848f">@{{val.helpblock}}</p>
							</div>
							<!--2-True/False-->
							<div v-else-if="val.type=='2-True/False'" style="height: 128px">
								<!--
								<label :style="{background: val.bgcolor}">
									<input type="checkbox" v-model.lazy="val.value==1||false" @change="val.value=val.value?0:1" :disabled="val.readonly||false">@{{val.name||'未命名'}}
								</label>-->
								
								<strong :style="{background: val.bgcolor}">@{{val.name||'未命名'}}</strong><br>
								<i-switch v-model.lazy="formItem.switch[key+'_'+k]" :disabled="val.readonly||false">
									<Icon type="android-done" slot="open"></Icon>
									<Icon type="android-close" slot="close"></Icon>
								</i-switch>
								
								<p style="color: #80848f">@{{val.helpblock}}</p>
							</div>
							<!--3-Number-->
							<div v-else-if="val.type=='3-Number'" style="height: 128px">
								<strong>@{{val.name||'未命名'}}</strong><br>
								<!--<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" v-model.lazy="val.value" :placeholder="val.placeholder">-->
								<Input-number v-model.lazy="formItem.number[key+'_'+k]" :style="{background: val.bgcolor}" :readonly="val.readonly" placeholder="val.placeholder" size="small" style="width: 200px"></Input-number>
								<p style="color: #80848f">@{{val.helpblock}}</p>
							</div>
							<!--4-Date-->
							<div v-else-if="val.type=='4-Date'" style="height: 128px">
								<strong>@{{val.name||'未命名'}}</strong><br>
								<!--<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" v-model.lazy="val.value" :placeholder="val.placeholder">-->
								<Date-picker v-model.lazy="formItem.date[key+'_'+k]" type="datetime" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :placeholder="val.placeholder" style="width: 200px" size="small"></Date-picker>
								<p style="color: #80848f">@{{val.helpblock}}</p>
							</div>
							<!--5-Textfield-->
							<div v-else-if="val.type=='5-Textfield'" style="height: 128px">
								<strong>@{{val.name||'未命名'}}</strong><br>
								<!--<textarea class="form-control" rows="3" style="resize:none;" :style="{background: val.bgcolor}" :readonly="val.readonly||false" v-model.lazy="val.value" :placeholder="val.placeholder"></textarea>-->
								<i-input type="textarea" :rows="2" v-model.lazy="formItem.textarea[key+'_'+k]" style="width:200px;" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :placeholder="val.placeholder" size="small" clearable></i-input>
								<p style="color: #80848f">@{{val.helpblock}}</p>
							</div>
							<!--6-Radiogroup-->
							<div v-else-if="val.type=='6-Radiogroup'" style="height: 128px">
								<!--<label>@{{val.name||'未命名'}}</label>
								<div class="form-group">
									<div v-for="(item,index) in val.value.split('---')" v-if="index%2 === 0" class="radio">
										<label :style="{background: val.bgcolor}">
											<input type="radio" @change="val.value=radiochecked_change(val.value, index)" :name="'name_radiogroup_'+val.name" :checked="val.value.split('---')[index+1]==1||false" :disabled="val.readonly||false">
											@{{item}}
										</label>
									</div>
									<p class="help-block">@{{val.helpblock}}</p>
								</div>-->
								
								<strong>@{{val.name||'未命名'}}</strong><br>
								<Radio-group v-model.lazy="formItem.radiogroup[key+'_'+k]">
									<Radio v-for="(item,index) in val.value.split('|')[0].split('---')" :label="item" :style="{background: val.bgcolor}" :disabled="val.readonly||false"></Radio>
								</Radio-group>
								<p style="color: #80848f">@{{val.helpblock}}</p>
								
							</div>
							<!--7-Checkboxgroup-->
							<div v-else-if="val.type=='7-Checkboxgroup'" style="height: 128px">
								<!--
								<label>@{{val.name||'未命名'}}</label>
								<div class="form-group">
									<div v-for="(item,index) in val.value.split('---')" v-if="index%2 === 0">
										<label :style="{background: val.bgcolor}">
											<input type="checkbox" @change="val.value=checkboxchecked_change(val.value, index)" :name="'name_checkboxgroup_'+val.name" :checked="val.value.split('---')[index+1]==1||false" :disabled="val.readonly||false">
											@{{item}}
										</label>
									</div>
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>
								-->
								
								<strong>@{{val.name||'未命名'}}</strong><br>
								<Checkbox-group v-model.lazy="formItem.checkboxgroup[key+'_'+k]">
									<Checkbox v-for="(item,index) in val.value.split('|')[0].split('---')" :label="item" :style="{background: val.bgcolor}" :disabled="val.readonly||false"></Checkbox>
								</Checkbox-group>
								<p style="color: #80848f">@{{val.helpblock}}</p>

							</div>
							<!--8-Combobox-->
							<div v-else-if="val.type=='8-Combobox'" style="height: 128px">
								<!--<label :style="{background: val.bgcolor}">@{{val.name||'未命名'}}</label>
								<div class="form-group">
										
										<multi-select @change="val.value=options_change(val.value, key, i)" v-model="select_tmp[key][i]" :options="options_value(val.value)" :placeholder="val.placeholder" :disabled="val.readonly||false" :limit="1" filterable collapse-selected size="sm"/>
										
								</div>
								<p class="help-block">@{{val.helpblock}}</p>-->
								
								<strong :style="{background: val.bgcolor}">@{{val.name||'未命名'}}</strong><br>
								<i-select v-model.lazy="formItem.select[key+'_'+k]" :placeholder="val.placeholder" :disabled="val.readonly||false" clearable multiple size="small" style="width:200px">
									<i-option v-for="item in formItem.option[key+'_'+k]" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
								</i-select>
								<p style="color: #80848f">@{{val.helpblock}}</p>
								
							</div>
						</div>
					
					</i-col>
					</span>
					<i-col :span="value.field.length%4*6">&nbsp;</i-col>
					</i-row>

					&nbsp;<br>&nbsp;
					
					</div>
				</Panel>
			</Collapse>
					
					
			<!--slot，否则显示空的slot-->
			<Collapse v-model="valuecollapsefield[key]" v-else>
				<Panel :name="valuecollapsefieldname[key]">
					@{{ value.name }}
					<div slot="content">
					
						<div class="alert alert-warning">
							These's no fields ... <a href="{{ route('admin.slot2field.index') }}" class="alert-link">Goto add field now</a>.
						</div>
					
					&nbsp;<br>&nbsp;
					</div>
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
		showHeader: false,
		gets_review_users: {},
		gets_review_fields: {},
		
		// 代理select
		// substitute_select: [],
		// substitute_options: [],
		substitute_modal: false,
		substitute_checkbox: [],
		substitute_user: [],
		
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
		valuecollapsefield: [],
		valuecollapsefieldname: [],
		
		// slot表单
		formItem: {
			input: [],
			switch: [],
			number: [],
			date: [],
			time: [],
			textarea: [],
			radiogroup: [],
			checkboxgroup: [],
			select: [],
			option: [],
			file: [],
			
		},

		
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
				// 1.info
				_this.infodata = [response.data.infodata];
				
				// 2.user

				var json = '';
				_this.valuecollapsefield = [];
				_this.valuecollapsefieldname = [];
				for (var i in response.data.slotdata) {
					// user
					_this.$set(_this.gets_review_users, i, response.data.slotdata[i]['user']);
					
					// field
					_this.$set(_this.gets_review_fields, i, response.data.slotdata[i]['slot']);
					
					// console.log(_this.gets_review_fields[i]);
					// return false;
					// collapse
					_this.$set(_this.valuecollapsefield, i, 's'+i);
					_this.$set(_this.valuecollapsefieldname, i, 's'+i);
					
					if (_this.gets_review_fields[i].field[i] != null) {
						
						
						for (var key in _this.gets_review_fields[i].field) {
								
							
							// 各字段
								// console.log(_this.gets_review_fields[i].field[key]);
							
							if (_this.gets_review_fields[i].field[key].type == '1-Text') {
								_this.$set(_this.formItem.input, i + '_' + key, _this.gets_review_fields[i].field[key].value);
							}
							else if (_this.gets_review_fields[i].field[key].type == '2-True/False') {
								_this.$set(_this.formItem.switch, i + '_' + key, _this.gets_review_fields[i].field[key].value==1||false);
							}
							else if (_this.gets_review_fields[i].field[key].type == '3-Number') {
								_this.$set(_this.formItem.number, i + '_' + key, _this.gets_review_fields[i].field[key].value);
							}
							else if (_this.gets_review_fields[i].field[key].type == '4-Date') {
								_this.$set(_this.formItem.date, i + '_' + key, _this.gets_review_fields[i].field[key].value);
							}
							else if (_this.gets_review_fields[i].field[key].type == '5-Textfield') {
								_this.$set(_this.formItem.textarea, i + '_' + key, _this.gets_review_fields[i].field[key].value);
							}
							else if (_this.gets_review_fields[i].field[key].type == '6-Radiogroup') {
								_this.$set(_this.formItem.radiogroup, i + '_' + key, _this.gets_review_fields[i].field[key].value.split('|')[1]);
							}
							else if (_this.gets_review_fields[i].field[key].type == '7-Checkboxgroup') {
								_this.$set(_this.formItem.checkboxgroup, i + '_' + key, _this.gets_review_fields[i].field[key].value.split('|')[1].split(','));
							}
							else if (_this.gets_review_fields[i].field[key].type == '8-Combobox') {
								var v = _this.gets_review_fields[i].field[key].value;
								var o = v.split('|')[0].split('---');
								var s = v.split('|')[1].split('---');

								var oo = [];
								for (j in o) {
									oo.push({value: o[j], label: o[j]});
								}
								_this.$set(_this.formItem.option, i + '_' + key, oo);
								
								var ss = [];
								if (typeof(s) != 'undefined' || s != '') {
									for (j in s) {
										ss.push(s[j]);
										_this.$set(_this.formItem.select, i + '_' + key, ss);
									}
								}
							}
							else if (_this.gets_review_fields[i].field[key].type == '9-File') {
								_this.$set(_this.formItem.file, i + '_' + key, _this.gets_review_fields[i].field[key].value);
							}
								
								
						}
						
					}
					
					
					
				}
				
				
				
				return false;
				

				
				
				
				
				
				
				
				


			})
			.catch(function (error) {
				console.log(error);
			})
		},
		
		// 查看代理人
		getsubstitute: function (id) {
			var _this = this;
			// console.log(id);
			
			var url = "{{ route('main.circulation.getsubstitute') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					id: id
				}
			})
			.then(function (response) {
				console.log(response.data);
				
				_this.substitute_user = response.data;
				_this.substitute_modal = !_this.substitute_modal;

			})
			.catch(function (error) {
				console.log(error);
			})
		},
		
		// 选择代理后转向
		substitute_ok: function () {
			var _this = this;
			console.log(_this.substitute_checkbox);
			// Array [ "user4", "user3" ]
			
		}
		


		
	},
	mounted: function () {
		var _this = this;
		_this.circulationgets(1, 1); // page: 1, last_page: 1
		


	}
})
</script>
@endsection