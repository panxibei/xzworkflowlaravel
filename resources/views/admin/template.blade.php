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
<div id="template_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Template Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Template 管理
				</div>
				<div class="panel-body">
					<div class="row">

					<div class="panel-body">
						<tabs v-model="currenttabs">
							<tab title="Template List">
								<!--template列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->
									<div class="table-responsive">
										<table class="table table-condensed">
											<thead>
												<tr>
													<th>id</th>
													<th>name</th>
													<th>created_at</th>
													<th>updated_at</th>
													<th>操作</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="(val, index) in gets.data">
													<td><div>@{{ val.id }}</div></td>
													<td><div>@{{ val.name }}</div></td>
													<td><div>@{{ val.created_at }}</div></td>
													<td><div>@{{ val.updated_at }}</div></td>
													<td><div>
													<btn @click="template_detail(index)" type="primary" size="xs"><i class="fa fa-edit fa-fw"></i></btn>&nbsp;
													<btn @click="template_delete(val.id)" type="danger" size="xs"><i class="fa fa-times fa-fw"></i></btn></div></td>
												</tr>
											</tbody>
										</table>

										<div class="dropup">
											<tr>
												<td colspan="9">
													<div>
														<nav>
															<ul class="pagination pagination-sm">
																<li><a aria-label="Previous" @click="templategets(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

																<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
																	<a v-if="n==1" @click="templategets(1, gets.last_page)" href="javascript:;">1</a>
																	<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="templategets(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
																	<a v-else-if="n==2||n==gets.last_page">...</a>
																</li>&nbsp;

																<li><a aria-label="Next" @click="templategets(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
																<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

																	<div class="col-xs-2">
																	<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="templategets($event.target.value, gets.last_page)">
																	</div>

																<div class="btn-group">
																<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ perpage }}条<span class="caret"></span></button>
																<ul class="dropdown-menu">
																<li><a @click="configperpagefortemplate(2)" href="javascript:;"><small>2条记录</small></a></li>
																<li><a @click="configperpagefortemplate(5)" href="javascript:;"><small>5条记录</small></a></li>
																<li><a @click="configperpagefortemplate(10)" href="javascript:;"><small>10条记录</small></a></li>
																<li><a @click="configperpagefortemplate(20)" href="javascript:;"><small>20条记录</small></a></li>
																</ul>
																</div>
															</ul>
														</nav>
													</div>
												</td>
											</tr>
										</div>

									</div>
								</div>
							</tab>
							<tab title="Create/Edit Template">
								<!--操作1-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div><br>-->

									<div class="col-lg-12">
										<div class="panel panel-default">
											<div class="panel-heading"><label>新建/编辑元素</label></div>
											<div class="panel-body">

												<div class="col-lg-4">
													<div class="form-group">
														<label>名称</label>
														<input v-model="template_add_id" type="hidden" class="form-control input-sm">
														<input v-model="template_add_name" type="text" class="form-control input-sm">
													</div>

													<div class="form-group">
														<btn type="primary" @click="templatecreateorupdate('create')" size="sm">Create</btn>&nbsp;
														<btn type="primary" @click="templatecreateorupdate('update')" size="sm">Update</btn>&nbsp;
														<btn type="default" @click="templatereset" size="sm">Reset</btn>
													</div>
												</div>

											</div>
										</div>
									</div>
									
									
									
									
									
								</div>
							</tab>
							
						</tabs>

					</div>
					</div>
					
				</div>

			</div>
		</div>
	</div>
</div>
</div>
@endsection

@section('my_footer')
@parent
<script>
var vm_template = new Vue({
    el: '#template_list',
    data: {
		gets: {},
		perpage: {{ $config['PERPAGE_RECORDS_FOR_SLOT'] }},
		// 创建ID
		template_add_id: '',
		// 创建名称
		template_add_name: '',

		// tabs索引
		currenttabs: 0
    },
	methods: {
		// 显示当前template并切换到编辑界面
		template_detail: function (index) {
			var _this = this;
			
			_this.template_add_id = _this.gets.data[index].id;
			_this.template_add_name = _this.gets.data[index].name;

			// 切换到第二个面板
			_this.currenttabs = 1;
		},
		templatereset: function () {
			this.template_add_id = '';
			this.template_add_name = '';
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
		// 创建或更新template
		templatecreateorupdate: function (createorupdate) {
			var _this = this;
			var postdata = {};
			postdata['id'] = _this.template_add_id;
			postdata['name'] = _this.template_add_name;
			postdata['createorupdate'] = createorupdate;
			
			if(postdata['name'].length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please input the template name!';
				_this.notification_message();
				return false;
			}

			var url = "{{ route('admin.template.createorupdate') }}";
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
					_this.notification_content = 'template failed to ' + createorupdate + ' !';
					_this.notification_message();
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'template ' + createorupdate + ' successfully!';
					_this.notification_message();

					if (createorupdate=='create') {_this.templatereset()}

				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				// _this.notification_content = error.response.data.message;
					_this.notification_content = 'Error! template failed to ' + createorupdate + ' !';
				_this.notification_message();
			})
		},
		// 删除template
		template_delete: function (id) {
			var _this = this;
			
			if (id == undefined) {
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please select the template(s)!';
				_this.notification_message();
				return false;
			}
			
			var url = "{{ route('admin.template.templatedelete') }}";
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
					_this.notification_content = 'template(s) failed to delete!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'template(s) deleted successfully!';
					_this.notification_message();
					
					// 刷新
					_this.templategets(_this.gets.current_page, _this.gets.last_page);
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// template列表
		templategets: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.template.templategets') }}";
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.perpage,
					page: page
				}
			})
			.then(function (response) {
				if (typeof(response.data.data) == "undefined") {
					// alert(response);
					_this.alert_exit();
				}
				_this.gets = response.data;
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		configperpagefortemplate: function (value) {
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
					_this.templategets(1, 1);
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
		// 显示所有template
		this.templategets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection