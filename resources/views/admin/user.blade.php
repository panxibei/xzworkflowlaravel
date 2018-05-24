@extends('admin.layouts.adminbase')

@section('my_title', "Admin(User) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">User Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					用户管理
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<button type="button" id="button_user_query_open" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#collapse_user_query" aria-expanded="false" aria-controls="collapse_user_query">打开查询</button>&nbsp;
								<button type="button" id="user_excel_export" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span> 导出</button>&nbsp;
								<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">新 建</button>&nbsp;
								
								
								
							</div>
						</div>
						<div class="col-lg-12">
							<div class="collapse" id="collapse_user_query">
								<form id="form_user_query" class="form-inline">
									<div class="form-group">
										<label for="user_query_account" class="control-label">帐号</label>
										<input class="form-control input-sm" type="text" id="user_query_account" placeholder="帐号" />
									</div>
									<div class="form-group">
										<label for="user_query_login_time" class="control-label">最近登录时间</label>
										<input class="form-control input-sm" type="text" id="user_query_login_time" />
										<script type="text/javascript">
										/*$(function() {
											moment.lang('ja');
											$('input#user_query_login_time').daterangepicker({
												singleDatePicker: false,
												showDropdowns: true,
												timePicker: true,
												timePicker24Hour: true,
												timePickerSeconds: true,
												linkedCalendars: false,
												//startDate: "08/13/2016",
												//endDate: "08/19/2016",
												//minDate: "01/01/2000",
												maxDate: "12/31/2199",
												locale: {
													format: 'MM/DD/YYYY HH:mm:ss'
												}
												 
												//function(start, end, label) {
												//	var years = moment().diff(start, 'years');
												//	alert("You are " + years + " years old.");
												//});
											});
											//$('div.calendar-table').remove();
										});*/
										</script>
									</div>
									<button type="button" id="button_user_query" class="btn btn-default btn-sm">查询</button>
								</form>
							</div>
						</div>
						
						
						
						
						
						<div class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div>
							<div id="user_list" class="table-responsive" v-cloak>
								<table class="table table-condensed">
									<thead>
										<tr>
											<th>ID</th>
											<th>用户名</th>
											<th>用户组</th>
											<th>最近登录IP</th>
											<th>登录次数</th>
											<th>最近登录时间</th>
											<th>状态</th>
											<th>创建时间</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody id="tbody_user_query">
				
										<tr v-for="val in gets.data">
											<td><div>@{{ val.id }}</div></td>
											<td><div v-bind:id="'user_edit_account' + val.id">@{{ val.name }}</div></td>
											<td><div>@{{ val.group }}</div></td>
											<td><div>@{{ val.login_ip }}</div></td>
											<td><div>@{{ val.login_counts }}</div></td>
											<td><div>@{{ date('Y-m-d H:i:s', val.login_time) }}</div></td>
											<td><div>@{{ val.delete_at ? "禁用" : "启用" }}</div></td>
											<td><div>@{{ date('Y-m-d H:i:s', val.create_at) }}</div></td>
											<td><div><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal_user_edit" v-bind:id="'user_edit' + val.id" v-bind:value="val.id"><i class="fa fa-edit fa-fw"></i></button>
											&nbsp;<button class="btn btn-danger btn-xs" v-bind:id="'user_del' + val.id" v-bind:value="val.id"><i class="fa fa-times fa-fw"></i></button></div></td>
										</tr>

									</tbody>
								</table>
								<div id="div_user_query" class="dropup">
								
									<tr><td colspan="9"><div><nav>

										<ul class="pagination pagination-sm">
											<li><a aria-label="Previous" @click="userlist(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

											<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
												<a v-if="n==1" @click="userlist(1, gets.last_page)" href="javascript:;">1</a>
												<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="userlist(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
												<a v-else-if="n==2||n==gets.last_page">...</a>
											</li>&nbsp;

											<li><a aria-label="Next" @click="userlist(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
											<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

												<div class="col-xs-2">
												<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="userlist($event.target.value, gets.last_page)">
												</div>

											<div class="btn-group">
											<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ gets.per_page }}条<span class="caret"></span></button>
											<ul class="dropdown-menu">
											<li><a @click="configperpageforuser(2)" href="javascript:;"><small>2条记录</small></a></li>
											<li><a @click="configperpageforuser(5)" href="javascript:;"><small>5条记录</small></a></li>
											<li><a @click="configperpageforuser(10)" href="javascript:;"><small>10条记录</small></a></li>
											<li><a @click="configperpageforuser(20)" href="javascript:;"><small>20条记录</small></a></li>
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

<!-- Modal user edit -->
<form id="form_user_update" action="{:U('Admin/Index/user_update')}" method="post">
	<div class="modal fade" id="myModal_user_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">编辑用户</h4>
		  </div>
		  <div class="modal-body">
			  <div class="container">
				<div class="row">
					<div class="col-lg-3">
						<!-- <div class="form-group"> -->
							<!-- <label>ID</label> -->
							<!-- <input type="text" id="user_edit_id" class="form-control input-sm"> -->
							<span hidden="hidden"><input type="text" id="user_edit_id"></span>
						<!-- </div> -->
						<div class="form-group">
							<label>最近登录时间</label>
							<input type="text" id="user_edit_login_time" class="form-control input-sm">
						</div>
						<!-- <div class="form-group"> -->
							<!-- <label>最近登录IP</label> -->
							<!-- <input type="text" id="user_edit_login_ip" class="form-control input-sm"> -->
						<!-- </div> -->
						<!-- <div class="form-group"> -->
							<!-- <label>登录次数</label> -->
							<!-- <input type="text" id="user_edit_login_count" class="form-control input-sm"> -->
						<!-- </div> -->
						<div class="form-group">
							<label>密码</label>
							<input type="text" id="user_edit_password" class="form-control input-sm">
						</div>
						<div class="form-group">
							<label>Email</label>
							<input type="text" id="user_edit_email" class="form-control input-sm">
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label>用户名</label>
							<input type="text" id="user_edit_account" class="form-control input-sm">
						</div>
						<div class="form-group">
							<label>用户组</label>
							<select name="group" id="user_edit_group_id" class="form-control input-sm"></select>
						</div>
						<div class="form-group">
							<label>手机号</label>
							<input type="text" id="user_edit_mobile" class="form-control input-sm">
						</div>
					</div>
				</div>
			</div>
		  </div>
		  <div class="modal-footer">
			<label><input id="user_edit_status" type="checkbox">启用</label>&nbsp;&nbsp;
			<button type="submit" id="user_add_or_update" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"></span> 更 新</button>
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>{__TOKEN__}
</form>

<!-- Modal create user-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
		<form id="form_user_add" method="post">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">新建用户</h4>
		</div>
		<div class="modal-body">
			<div class="container">
				<div class="row">
					<div  class="col-lg-3">
						<div class="form-group">
							<label>账号</label>
							<input id="user_add_account" type="text" class="form-control input-sm">
						</div>
						<div class="form-group">
							<label>密码</label>
							<input id="user_add_password" type="text" class="form-control input-sm">
						</div>
						<div class="form-group">
							<label>用户组</label>
							<select id="user_add_group" class="form-control input-sm"></select>
						</div>
					</div>
					<div  class="col-lg-3">
						<div class="form-group">
							<label>Email</label>
							<input id="user_add_email" type="text" class="form-control input-sm">
						</div>
						<div class="form-group">
							<label>手机号</label>
							<input id="user_add_mobile" type="text" class="form-control input-sm">
						</div>
						<div class="checkbox">
							<label><input id="user_add_status" type="checkbox" checked="checked" value=""><b>启用</b></label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" id="button_rule_add" class="btn btn-primary"><span class="glyphicon glyphicon-asterisk"></span> 新建</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
		{__TOKEN__}
		</form>
	</div>
  </div>
</div>

@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_user = new Vue({
    el: '#user_list',
    data: {
		gets: {},
		perpage: {{ $PERPAGE_RECORDS_FOR_USER }}
    },
	methods: {
		userlist: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.user.list') }}";
			// var perPage = 1; // 有待修改，将来使用配置项
			
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
				// console.log(response);
				// alert(response.data);
				if (typeof(response.data.data) == "undefined") {
					// alert('toekn失效，跳转至登录页面');
					_this.alert_exit();
					// window.setTimeout(function(){
						// window.location.href = "{{ route('admin.config.index') }}";
					// },1000);
				}
				// return false;
				_this.gets = response.data;
				// alert(_this.gets);
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
		configperpageforuser: function (value) {
			var _this = this;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_name: 'PERPAGE_RECORDS_FOR_USER',
				cfg_value: value
			})
			.then(function (response) {
				if (response.data) {
					_this.perpage = value;
					_this.userlist(1, 1);
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
		_this.userlist(1, 1);
	}
});
</script>
@endsection