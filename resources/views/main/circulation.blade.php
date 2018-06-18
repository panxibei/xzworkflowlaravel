@extends('main.layouts.mainbase')

@section('my_title')
Main(circulation) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="circulation_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<br>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel panel-default">
				<div class="panel-heading" role="button" @click="show_circulation=!show_circulation;">
					<h4 class="panel-title"><i class="fa fa-refresh fa-fw"></i> Circulation</h4>
				</div>
				<collapse v-model="show_circulation">
					<div class="panel-body">


						<tabs v-model="currenttabs">
							<tab title="Circulation List">
								<!--circulation 列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->
									<div class="table-responsive">
										<table class="table table-condensed">
											<thead>
												<tr>
													<th>id</th>
													<th>name</th>
													<th>Days in process</th>
													<th>created_at</th>
													<th>Creator</th>
													<th>Progress</th>
													<th>Operation</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="val in gets.data">
													<td><div>@{{ val.id }}</div></td>
													<td><div>@{{ val.name }}</div></td>
													<td><div>@{{ parseInt((Date.parse(new Date()) - Date.parse(val.created_at))/86400000) + ' day(s)' }}</div></td>
													<td><div>@{{ val.created_at }}</div></td>
													<td><div>@{{ val.creator }}</div></td>
													<td><div>@{{ val.progress }}</div></td>
													<td><div><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit fa-fw"></i></button>
													&nbsp;<button class="btn btn-danger btn-xs"><i class="fa fa-times fa-fw"></i></button></div></td>
												</tr>
											</tbody>
										</table>

										<div class="dropup">
											<tr>
												<td colspan="9">
													<div>
														<nav>
															<ul class="pagination pagination-sm">
																<li><a aria-label="Previous" @click="circulationgets(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

																<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
																	<a v-if="n==1" @click="circulationgets(1, gets.last_page)" href="javascript:;">1</a>
																	<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="circulationgets(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
																	<a v-else-if="n==2||n==gets.last_page">...</a>
																</li>&nbsp;

																<li><a aria-label="Next" @click="circulationgets(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
																<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

																	<div class="col-xs-2">
																	<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="circulationgets($event.target.value, gets.last_page)">
																	</div>

																<div class="btn-group">
																<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ perpage }}条<span class="caret"></span></button>
																<ul class="dropdown-menu">
																<li><a @click="configperpageforcirculation(2)" href="javascript:;"><small>2条记录</small></a></li>
																<li><a @click="configperpageforcirculation(5)" href="javascript:;"><small>5条记录</small></a></li>
																<li><a @click="configperpageforcirculation(10)" href="javascript:;"><small>10条记录</small></a></li>
																<li><a @click="configperpageforcirculation(20)" href="javascript:;"><small>20条记录</small></a></li>
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
							<tab title="Create Circulation">
								<!--操作1-->
								<div class="col-lg-12">
								<br>

									<div class="col-lg-3">
										<div class="form-group">
											<label>Select a Template</label><br>
											<multi-select @change="change_template()" v-model="template_select" :options="template_options" :limit="1" filterable collapse-selected size="sm" />
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Select a Mailing List</label><br>
											<multi-select @change="change_mailinglist()" v-model="mailinglist_select" :options="mailinglist_options" :limit="1" filterable collapse-selected size="sm" />
										</div>
									</div>
									<div class="col-lg-3">
										<btn @click="review_create_circulation()" type="default" size="sm"><i class="fa fa-magic fa-fw"></i> Review & Create a circulation</btn>&nbsp;
									</div>
									<div class="col-lg-3">
									</div>
									
								</div>
								
								<div class="col-lg-12">
								<!--流程信息-->
									<div style="background-color:#c9e2b3;height:1px"></div><br>

									<div class="panel panel-default">
										<div class="panel-heading" role="button" @click="show_review_template=!show_review_template;">
											<h4 class="panel-title"><i class="fa fa-bookmark fa-fw"></i> Circulation Information</h4>
										</div>
										<collapse v-model="show_review_template">
											<div class="panel-body">									

												<div class="row">
													<div class="col-lg-2">
														<div class="form-group">
														<label>流程名称</label>
														<p class="form-control-static">@{{ create_template }}</p>
														</div>
													</div>
													<div class="col-lg-2">
														<div class="form-group">
															<label>创建日期</label>
															<p class="form-control-static">@{{ create_created_at }}</p>
														</div>
													</div>
													<div class="col-lg-2">
														<div class="form-group">
														<label>创建者</label>
														<p class="form-control-static">@{{ create_creator }}</p>
														</div>
													</div>
													<div class="col-lg-6">
														<div class="form-group">
															<label>详细描述</label>
															<p class="form-control-static">@{{ create_description }}</p>
														</div>
													</div>
												</div>

											</div>
										</collapse>
									</div>
									
								</div>
								
								<div class="col-lg-12">
								<!--人员-->

									<div class="panel panel-default">
										<div class="panel-heading" role="button" @click="show_review_group=!show_review_group;">
											<h4 class="panel-title"><i class="fa fa-group fa-fw"></i> Peoples</h4>
										</div>
										<collapse v-model="show_review_group">
											<div class="panel-body">									

												<div class="col-lg-12">
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
												
												<div class="col-lg-12" v-for="val in gets_peoples">
													<div v-if="val.user!='-'">
														<div class="col-lg-3">
															<p>@{{ val.user }}</p>
														</div>
														<div class="col-lg-3">
															<p>@{{ val.substitute }}</p>
														</div>
														<div class="col-lg-3">
															<p>@{{ val.email }}</p>
														</div>
														<div class="col-lg-3">
															<btn type="link" size="xs"><i class="fa fa-envelope fa-fw"></i></btn>&nbsp;
															<btn type="link" size="xs"><i class="fa fa-mail-forward fa-fw"></i></btn>&nbsp;
															<btn type="link" size="xs"><i class="fa fa-group fa-fw"></i></btn>&nbsp;
															<btn type="link" size="xs"><i class="fa fa-send fa-fw"></i></btn>&nbsp;
														</div>
													</div>
													<div v-else style="background-color:#c9e2b3;height:1px"></div><br>
												</div>

											</div>
										</collapse>
									</div>
								</div>

								<div class="col-lg-12">
								<!--流程表单-->

									<div class="panel panel-default">
										<div class="panel-heading" role="button" @click="show_review_form=!show_review_form;">
											<h4 class="panel-title"><i class="fa fa-file-text-o fa-fw"></i> Form</h4>
										</div>
										<collapse v-model="show_review_form">
											<div class="panel-body">									

												<!--slot-->
												<div class="panel panel-default" v-for="(value, key) in gets_fields">
													<div class="panel-heading" role="button" @click="show_review_slot=!show_review_slot;">
														<h4 class="panel-title"><i class="fa fa-flag-o fa-fw"></i> @{{ key }}</h4>
													</div>
													<collapse v-model="show_review_slot">
														<div class="panel-body">
														
															<div v-for="val in value">
																<div class="col-lg-3">
																	<!--1-Text-->
																	<div v-if="val.type=='1-Text'" class="form-group">
																		<label>@{{val.name||'未命名'}}</label>
																		<!--<input type="text" class="form-control input-sm" :style="background: val.bgcolor" :readonly="val.readonly||false" :value="val.value" :placeholder="val.placeholder">-->
																		<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :value="val.value" :placeholder="val.placeholder">
																		<p class="help-block">@{{val.helpblock}}</p>
																	</div>
																	<!--2-True/False-->
																	<div v-else-if="val.type=='2-True/False'" class="form-group">
																		<div class="checkbox">
																			<label :style="{background: val.bgcolor}">
																				<input type="checkbox" :checked="val.value==1||false" :disabled="val.readonly||false">@{{val.name||'未命名'}}
																			</label>
																			<p class="help-block">@{{val.helpblock}}</p>
																		</div>
																	</div>
																	<!--3-Number-->
																	<div v-else-if="val.type=='3-Number'" class="form-group">
																		<label>@{{val.name||'未命名'}}</label>
																		<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :value="val.value" :placeholder="val.placeholder">
																		<p class="help-block">@{{val.helpblock}}</p>
																	</div>
																	<!--4-Date-->
																	<div v-else-if="val.type=='4-Date'" class="form-group">
																		<label>@{{val.name||'未命名'}}</label>
																		<input type="text" class="form-control input-sm" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :value="val.value" :placeholder="val.placeholder">
																		<p class="help-block">@{{val.helpblock}}</p>
																	</div>
																	<!--5-Textfield-->
																	<div v-else-if="val.type=='5-Textfield'" class="form-group">
																		<label>@{{val.name||'未命名'}}</label>
																		<textarea class="form-control" rows="3" style="resize:none;" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :value="val.value" :placeholder="val.placeholder"></textarea>
																		<p class="help-block">@{{val.helpblock}}</p>
																	</div>
																	<!--6-Radiogroup-->
																	<div v-else-if="val.type=='6-Radiogroup'" class="form-group">
																		<label>@{{val.name||'未命名'}}</label>
																		<div class="form-group">
																			<div v-for="(item,index) in val.value.split('---')" v-if="index%2 === 0" class="radio">
																				<label :style="{background: val.bgcolor}">
																					<input type="radio" :name="'name_radiogroup_'+val.name" :checked="val.value.split('---')[index+1]==1||false" :disabled="val.readonly||false">
																					@{{item}}
																				</label>
																			</div>
																			<p class="help-block">@{{val.helpblock}}</p>
																		</div>
																	</div>
																</div>
															</div>

														</div>
													</collapse>
												</div>
											
											</div>
										</collapse>
									</div>

								</div>
								
								
								
							</tab>
							<tab title="Review Circulation">
								<!--操作2-->


							</tab>
						</tabs>




					</div>
				</collapse>
			</div>
			
			
			
			
		</div>
	</div>
</div>
</div>
@endsection

@section('my_footer')
@parent
<script>
var vm_circulation = new Vue({
    el: '#circulation_list',
    data: {
		show_circulation: true,
		show_review_template: true,
		show_review_group: true,
		show_review_form: true,
		show_review_slot: true,
		notification_type: '',
		notification_title: '',
		notification_content: '',
		gets: {},
		gets_peoples: {},
		gets_fields: {},
		perpage: {{ $config['PERPAGE_RECORDS_FOR_CIRCULATION'] }},
		template_select: [],
		template_options: [],
		mailinglist_select: [],
		mailinglist_options: [],
		// 创建相关元素
		create_template: '',
		create_created_at: '',
		create_creator: '',
		create_description: '',
		// tabs索引
		currenttabs: 1
    },
	methods: {
		// 把laravel返回的结果转换成select能接受的格式
		json2selectvalue: function (json, reverse) {
			var arr = [];
			for (var key in json) {
				arr.push({ value: key, label: json[key] });
			}
			if (reverse) {
				return arr.reverse();
			} else {
				return arr;
			}
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
		// circulation列表 ok
		circulationgets: function(page, last_page){
			var _this = this;
			var url = "{{ route('main.circulation.circulationgets') }}";
			
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
				// if (typeof(response.data.data) == "undefined") {
					// _this.alert_exit();
				// }
				_this.gets = response.data;
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 配置页数 ok
		configperpageforcirculation: function (value) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_CIRCULATION'] = value;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.perpage = value;
					_this.circulationgets(1, 1);
				} else {
					alert('failed');
				}
			})
			.catch(function (error) {
				alert('failed');
				// console.log(error);
			})
		},
		// gettemplateoptions ok
		gettemplateoptions: function () {
			var _this = this;
			var url = "{{ route('main.circulation.gettemplateoptions') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url)
			.then(function (response) {
				// if (typeof(response.data.data) == "undefined") {
					// _this.alert_exit();
				// }
				var json = response.data;
				_this.template_options = _this.json2selectvalue(json, true);

			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// ok
		change_template: function () {
			var _this = this;
			var template_id = _this.template_select[0];
			if (template_id==undefined) {
				_this.mailinglist_select = [];
				_this.mailinglist_options = [];
				return false;
			}
			
			var url = "{{ route('main.circulation.changetemplate') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					template_id: template_id
				}
			})
			.then(function (response) {
				// if (typeof(response.data.data) == "undefined") {
					// _this.alert_exit();
				// }
				var json = response.data;
				_this.mailinglist_options = _this.json2selectvalue(json, true);

			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// ok
		change_mailinglist: function () {
			var _this = this;
			var template_id = _this.template_select[0];
			var mailinglist_id = _this.mailinglist_select[0];
			if (template_id = undefined || mailinglist_id == undefined) {
				return false;
			}
			
			var url = "{{ route('main.circulation.changemailinglist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					// template_id: template_id,
					mailinglist_id: mailinglist_id
				}
			})
			.then(function (response) {
				// if (typeof(response.data.data) == "undefined") {
					// _this.alert_exit();
				// }
				// var json = response.data;
				// _this.mailinglist_options = _this.json2selectvalue(json, true);
				
				// 以下是需要的内容
				// console.log(response.data);
				_this.gets_peoples = response.data.userinfo;
				_this.gets_fields = response.data.field;

			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 预览创建circulation
		review_create_circulation: function () {
			var _this = this;
			var template_id = _this.template_select[0];
			var mailinglist_id = _this.mailinglist_select[0];
			
			console.log(template_id);
			console.log(mailinglist_id);
			
			if (template_id == undefined || mailinglist_id == undefined) {
				_this.$notify('Nothing selected!');
				return false;
			}
			
			
			
			
		}
	},
	mounted: function () {
		var _this = this;
		_this.circulationgets(1, 1); // page: 1, last_page: 1
		_this.gettemplateoptions();
		// _this.rolelistdelete();
		// _this.rolelist();
		// _this.permissionlist();
	}
});
</script>
@endsection