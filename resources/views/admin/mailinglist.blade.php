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
<div id="mailinglist_list">
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Mailinglist Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Mailinglist 管理
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<btn type="default" @click="open_querymailinglist=!open_querymailinglist" size="sm">Query Filter</btn>&nbsp;
								<btn type="default" @click="mailinglistexport()" size="sm"><i class="fa fa-external-link fa-fw"></i> 导出</btn>&nbsp;
								<btn type="default" @click="show_createmailinglist=true" size="sm">Create Mailinglist</btn>
								
								
							</div>
						</div>
						<div class="col-lg-12">
							<collapse v-model="open_querymailinglist">
								<div class="well" style="margin-bottom: 0">
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label class="control-label">账号</label>
												<input v-model.lazy="queryfilter_name" class="form-control input-sm" type="text" placeholder="账号">
								<br><btn type="default" size="sm"  @click="queryfilter()">Query</btn>
								&nbsp;<btn type="default" size="sm"  @click="queryfilter_name=queryfilter_email='';queryfilter_datefrom=queryfilter_dateto=null;queryfilter()">Clear</btn>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label class="control-label">Email</label>
												<input v-model.lazy="queryfilter_email" class="form-control input-sm" type="text" placeholder="邮箱">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label class="control-label">最近登录时间（始）</label>
												<dropdown class="form-group">
													<div class="input-group">
														<input class="form-control" type="text" v-model.lazy="queryfilter_datefrom" placeholder="开始时间">
														<div class="input-group-btn">
															<btn class="dropdown-toggle"><i class="fa fa-calendar fa-fw"></i></btn>
														</div>
													</div>
													<template slot="dropdown">
														<li>
															<date-picker v-model="queryfilter_datefrom"/>
														</li>
													</template>
												</dropdown>
											</div>
										</div>
										<div class="col-lg-3">
											<label class="control-label">最近登录时间（终）</label>
											<dropdown class="form-group">
												<div class="input-group">
													<input class="form-control" type="text" v-model.lazy="queryfilter_dateto" placeholder="结束时间">
													<div class="input-group-btn">
														<btn class="dropdown-toggle"><i class="fa fa-calendar fa-fw"></i></btn>
													</div>
												</div>
												<template slot="dropdown">
													<li>
														<date-picker v-model="queryfilter_dateto"/>
													</li>
												</template>
											</dropdown>
										</div>
										<div class="col-lg-3">
										</div>
									</div>
								</div>
							</collapse>						
						</div>

						<div v-show="show_progress" class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div><br>
							<div class="col-lg-4">
							</div>
							<div class="col-lg-4">
								<progress-bar v-model="progress" striped active/>
							</div>
							<div class="col-lg-4">
							</div>
						</div>
						
						<div v-show="show_table" class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div>
								
							<div class="table-responsive" v-cloak>
								<table class="table table-condensed">
									<thead>
										<tr>
											<th>ID</th>
											<th>name</th>
											<th>template_name</th>
											<th>isdefault</th>
											<th>slot2user_id</th>
											<th>created_at</th>
											<th>updated_at</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody id="tbody_mailinglist_query">
				
										<tr v-for="val in gets.data">
											<td><div>@{{ val.id }}</div></td>
											<td><div>@{{ val.name }}</div></td>
											<td><div>@{{ val.template_name }}</div></td>
											<td><div>@{{ val.isdefault==1?'★':'' }}</div></td>
											<td><div>@{{ val.slot2user_id }}</div></td>
											<td><div>@{{ val.created_at }}</div></td>
											<td><div>@{{ val.updated_at }}</div></td>
											<td><div>
											&nbsp;<btn type="primary" size="xs" @click="editmailinglist(val)" :id="'btneditmailinglist'+val.id"><i class="fa fa-edit fa-fw"></i></btn>
											<tooltip text="编辑" :target="'#btneditmailinglist'+val.id"/>
											&nbsp;<btn type="danger" size="xs" @click="deletemailinglist(val)" :id="'btndeletemailinglist'+val.id"><i class="fa fa-times fa-fw"></i></btn>
											<tooltip text="删除" :target="'#btndeletemailinglist'+val.id"/>
											</div></td>
										</tr>

									</tbody>
								</table>
								<div id="div_mailinglist_query" class="dropup">
								
									<tr><td colspan="9"><div><nav>

										<ul class="pagination pagination-sm">
											<li><a aria-label="Previous" @click="mailinglistlist(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

											<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
												<a v-if="n==1" @click="mailinglistlist(1, gets.last_page)" href="javascript:;">1</a>
												<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="mailinglistlist(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
												<a v-else-if="n==2||n==gets.last_page">...</a>
											</li>&nbsp;

											<li><a aria-label="Next" @click="mailinglistlist(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
											<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

												<div class="col-xs-2">
												<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="mailinglistlist($event.target.value, gets.last_page)">
												</div>

											<div class="btn-group">
											<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ perpage }}条<span class="caret"></span></button>
											<ul class="dropdown-menu">
											<li><a @click="configperpageformailinglist(2)" href="javascript:;"><small>2条记录</small></a></li>
											<li><a @click="configperpageformailinglist(5)" href="javascript:;"><small>5条记录</small></a></li>
											<li><a @click="configperpageformailinglist(10)" href="javascript:;"><small>10条记录</small></a></li>
											<li><a @click="configperpageformailinglist(20)" href="javascript:;"><small>20条记录</small></a></li>
											</ul>
											</div>
										</ul>

									</nav></div></td></tr>

								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal mailinglist edit -->
<modal v-model="show_editmailinglist" @hide="callback_editmailinglist" size="sm">
	<span slot="title"><i class="fa fa-mailinglist fa-fw"></i> Edit Mailinglist</span>

	<div class="container">
		<div class="row">
			<div  class="col-lg-3">
				<input v-model="edit_id" ref="ref_edit_id" type="hidden" class="form-control input-sm">
				<div class="form-group">
					<label>Name</label>
					<input v-model="edit_name" ref="ref_edit_name" type="text" class="form-control input-sm">
				</div>
				<div class="form-group">
					<label>Template ID</label><br>
					<multi-select v-model="selected_edit_template" :options="options_edit_template" :limit="1" filterable collapse-selected size="sm" placeholder="请选择templateid..." />
				</div>
			</div>
		</div>
	</div>

	<div slot="footer">
		<btn @click="show_editmailinglist=false">Cancel</btn>
		<btn @click="updatemailinglist()" type="primary">Update</btn>
	</div>	
</modal>

<!-- Modal create mailinglist-->
<modal v-model="show_createmailinglist" @hide="callback_createmailinglist" size="sm">
	<span slot="title"><i class="fa fa-mailinglist fa-fw"></i> Create Mailinglist</span>

	<div class="container">
		<div class="row">
			<div  class="col-lg-3">
				<div class="form-group">
					<label>Mailinglist Name</label>
					<input v-model="createmailinglist_name" type="text" class="form-control input-sm">
				</div>
				<div class="form-group">
					<label>Template ID</label><br>
					<multi-select v-model="selected_create_template" :options="options_create_template" :limit="1" filterable collapse-selected size="sm" placeholder="请选择templateid..." />
				</div>
			</div>
		</div>
	</div>

	<div slot="footer">
		<btn @click="show_createmailinglist=false">Cancel</btn>
		<btn @click="createmailinglist()" type="primary">Create Mailinglist</btn>
	</div>	
</modal>

</div>
@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_mailinglist = new Vue({
    el: '#mailinglist_list',
    data: {
		show_progress: true,
		progress: 100,
		show_table: false,
		// show_update: false,
		gets: {},
		perpage: {{ $config['PERPAGE_RECORDS_FOR_MAILINGLIST'] }},
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
		// 查询过滤器
		queryfilter_name: "{{ $config['FILTERS_USER_NAME'] }}",
		queryfilter_email: "{{ $config['FILTERS_USER_EMAIL'] }}",
		queryfilter_datefrom: "{{ $config['FILTERS_USER_LOGINTIME_DATEFROM'] }}" || null,
		queryfilter_dateto: "{{ $config['FILTERS_USER_LOGINTIME_DATETO'] }}" || null
    },
	methods: {
		// 把laravel返回的结果转换成select能接受的格式
		json2selectvalue: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push({ value: key, label: json[key] });
			}
			return arr.reverse();
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
			_this.gets.current_page = page;

			var url = "{{ route('admin.mailinglist.list') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.perpage,
					page: page,
					queryfilter_name: queryfilter_name,
					queryfilter_email: queryfilter_email,
					queryfilter_datefrom: queryfilter_datefrom,
					queryfilter_dateto: queryfilter_dateto
				}
			})
			.then(function (response) {
				// console.log(response.data);return false;
				if (response.data != undefined) {
					// alert('toekn失效，跳转至登录页面');
					// _this.alert_exit();
				// return false;
					_this.gets = response.data;
					_this.show_progress = false;
					_this.show_table = true;
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
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
		// 加载templateid
		load_templateid: function () {
			var _this = this;
			var url = "{{ route('admin.mailinglist.loadtemplateid') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url)
			.then(function (response) {
				if (response.data) {
					var json = response.data;
					_this.options_edit_template = _this.options_create_template = _this.json2selectvalue(json);

				} else {
					_this.$notify('Templateid load failed!');
				}
			})
			.catch(function (error) {
				_this.$notify('Error! Templateid load failed!');
				// console.log(error);
			})			
			
			
		},
		configperpageformailinglist: function (value) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_MAILINGLIST'] = _this.perpage = value;
			_this.changeconfig(cfg_data);			
			_this.mailinglistlist(1, 1);
		},
		callback_createmailinglist: function (msg) {
			var _this = this;
			// _this.$notify(`Modal dismissed with msg '${msg}'.`)
			_this.createmailinglist_name = _this.createmailinglist_templateid = '';
		},
		createmailinglist: function () {
			var _this = this;
			var name = _this.createmailinglist_name;
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
		callback_editmailinglist: function (msg) {
			// this.$notify(`Modal dismissed with msg '${msg}'.`)
		},
		editmailinglist: function (val) {
			var _this = this;
			_this.edit_id = val.id;
			_this.edit_name = val.name;
			_this.selected_edit_template=[val.template_id.toString()];
			_this.show_editmailinglist=true;
		},
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
		callback_deletemailinglist: function (msg) {
			// this.$notify(`Modal dismissed with msg '${msg}'.`)
		},
		deletemailinglist: function (mailinglistid, mailinglistname) {
			var _this = this;
			if (mailinglistid == undefined || mailinglistid.length == 0) {return false;}
			
			_this.$confirm({
				okText: '删除',
				okType: 'danger',
				cancelText: '取消',
				title: '危险',
				content: '即将完全删除用户 [' + mailinglistname + ']，确认吗？'
			})
			.then(function () {
				// this.$notify({
					// type: 'success',
					// content: 'Delete completed.'
				// })
				
				var url = "{{ route('admin.mailinglist.delete') }}";
				axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
				axios.post(url, {
					mailinglistid: mailinglistid
				})
				.then(function (response) {
					if (response.data) {
						_this.$notify('Mailinglist deleted successfully!');
						_this.mailinglistlist(_this.gets.current_page, _this.gets.last_page);
					} else {
						_this.$notify('Mailinglist deleted failed!');
					}
				})
				.catch(function (error) {
					_this.$notify('Error! Mailinglist deleted failed!');
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
			
			_this.mailinglistlist(1, 1);
		},
		mailinglistexport: function(){
			// var _this = this;
			// var queryfilter_name = _this.queryfilter_name;
			// var queryfilter_email = _this.queryfilter_email;
			// var queryfilter_datefrom = new Date(_this.queryfilter_datefrom);
			// var queryfilter_dateto = new Date(_this.queryfilter_dateto);
			
			// if (queryfilter_datefrom > queryfilter_dateto) {
				// _this.$notify('Date is incorrect!');
				// return false;
			// }

			var url = "{{ route('admin.mailinglist.excelexport') }}";
			
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
		_this.mailinglistlist(1, 1);
		_this.load_templateid();
	}
});
</script>
@endsection