@extends('admin.layouts.adminbase')

@section('my_title', "Admin(User) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
/*
	$(function(){
		// 一打开就默认显示所有用户列表
		$.cookie('cookie_wf_user_account','');
		user_query(1); //调用user_query函数（下面），默认为页数为1即首页
	
		// 一打开就加载所有组信息
		$.get("{:U('Admin/Index/get_group')}",function(jdata){
			$("#user_add_group").append('<option value=""></option>');
			var select_value;
			$.each(jdata.all_group,function(i,val){
				select_value='<option value="'+val.id+'" title="'+val.title+'">'+val.title+'</option>';
				$("#user_add_group").append(select_value);
			});
		});
	
		//用户添加
		$('form#form_user_add').on('submit',function(e){
			e.preventDefault();
			var user_account = $("#user_add_account").val();
			var user_password = $("#user_add_password").val();
			var user_group = $("#user_add_group").val();
			var user_email = $("#user_add_email").val();
			var user_mobile = $("#user_add_mobile").val();
			var user_status = $("#user_add_status").prop("checked")?1:0;
			if(user_group.length==0||user_email.length==0||user_mobile.length==0){
				alert('内容不能为空');return false;
			}
			var user_hash = $("form#form_user_add input[name='{$Think.config.token_name}']").val();
			var url = "{:U('Admin/Index/user_add')}";
			//必须要传到$_POST['__hash__']中，如：$_POST[C('TOKEN_NAME')] = $_POST['user_hash'];
			$.post(url,{user_account:user_account,user_password:user_password,user_group:user_group,user_email:user_email,user_mobile:user_mobile,user_status:user_status,{$Think.config.token_name}:user_hash},function(jdata){
				if(jdata.ajax_status==0){
					BootstrapDialog.show({
						type:BootstrapDialog.TYPE_SUCCESS,
						message:'用户添加成功！',
						onshown:function(dialogRef){
							setTimeout(function(){
								dialogRef.close();
							}, 500);
						},
						onhidden: function(dialogRef){
							window.location.reload();
						}});
				} else {
					BootstrapDialog.show({message:'用户添加失败！'+'<br>原因：'+jdata.ajax_msg});
				}
				$("form#form_user_add input[name='{$Think.config.token_name}']").val(jdata.ajax_token);
				return false;
			});
		});

		//用户删除（动态）
		$("div").on('click','button[id^=user_del]',function(){
			var id = $(this).attr('value');
			var user_edit_account = $('#user_edit_account'+id).html();
			BootstrapDialog.show({
				title: 'WARNING',
				message: '警告！确定要删除用户 <b>' + user_edit_account + '</b> 吗？',
				type: BootstrapDialog.TYPE_WARNING,
				draggable: true,
				buttons: [{
					icon: 'glyphicon glyphicon-trash',
					label: 'Drop the user',
					cssClass: 'btn-warning',
					autospin: true,
					action: function(dialogRef){
						dialogRef.enableButtons(false);
						dialogRef.setClosable(false);
						//dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
						setTimeout(function(){
							$.get("{:U('Admin/Index/user_del')}",{id:id}, function(jdata){
								if(jdata.ajax_status == 0){
									BootstrapDialog.show({
										type:BootstrapDialog.TYPE_SUCCESS,
										message:'删除用户成功！',
										onshown:function(dialogRef){
											setTimeout(function(){
												dialogRef.close();
											}, 500);
										},
										onhidden: function(dialogRef){
											window.location.reload();
										}});
								} else {
									BootstrapDialog.show({message:'删除用户失败！'+'<br>原因：'+jdata.ajax_msg});
								}
							});
							dialogRef.close();
							return false;
						}, 1000);
					}
				}, {
					label: 'Close',
					action: function(dialogRef){
						dialogRef.close();
					}
				}]
			});			
			return false;				
		});

		//用户编辑显示（动态）
		$("div").on('click','button[id^=user_edit]',function(){
			var id=$(this).attr('value'); //.substr(9);
			$.get("{:U('Admin/Index/user_edit')}",{id:id},function(jdata){
				if(jdata.ajax_status==0){
					$("#user_edit_id").val(jdata['ajax_data']['id']);
					$("#user_edit_account").val(jdata['ajax_data']['account']);
					$("#user_edit_group_id").empty(); //清空下拉列表
					var select_value="<option value="+jdata['ajax_data']['current_group']['group_id']+">"+jdata['ajax_data']['current_group']['title']+"</option>";
					$("#user_edit_group_id").prepend(select_value); // 2.为Select插入一个Option(第一个位置) 
					$("#user_edit_group_id").append("<option value='' disabled>---- 请选择所属组 ----</option>");
					$.each(jdata.ajax_data.all_group,function(i,val){
						var select_value='<option value="'+val.id+'" title="'+val.title+'">'+val.title+'</option>';
						$("#user_edit_group_id").append(select_value);
					});
					$("#user_edit_login_time").val(date('Y-m-d H:i:s',jdata.ajax_data.login_time));
					//$("#user_edit_login_ip").val(jdata['ajax_data']['login_ip']);
					//$("#user_edit_login_count").val(jdata['ajax_data']['login_count']);
					//$("#user_edit_create_time").val(date('Y-m-d H:i:s',jdata['ajax_data']['create_time']));
					$("#user_edit_email").val(jdata.ajax_data.email);
					$("#user_edit_mobile").val(jdata.ajax_data.mobile);
					jdata.ajax_data.status==1?$("#user_edit_status").prop("checked",true):$("#user_edit_status").prop("checked",false);
				} else {
					BootstrapDialog.show({message:'查询用户失败！'+'<br>原因：'+jdata.ajax_msg});
				}
			});
			return true;
		});
		
		//用户更新
		$('#form_user_update').on('submit',function(e){
			e.preventDefault();
			var user_id = $("#user_edit_id").val();
			var user_password = $("#user_edit_password").val();
			var user_email = $("#user_edit_email").val();
			var user_mobile = $("#user_edit_mobile").val();
			var user_status = $("#user_edit_status").prop("checked")?1:0;
			var user_group = $('#user_edit_group_id').val();
			if(user_email.length==0||user_mobile.length==0){
				alert('内容不能为空');return false;
			}
			var user_hash = $("form#form_user_update input[name='{$Think.config.token_name}']").val();
			var url = "{:U('Admin/Index/user_update')}";
			$.post(url,{user_id:user_id,user_password:user_password,user_email:user_email,user_mobile:user_mobile,user_status:user_status,user_group:user_group,{$Think.config.token_name}:user_hash},function(jdata){
				if(jdata.ajax_status==0){
					BootstrapDialog.show({
						type:BootstrapDialog.TYPE_SUCCESS,
						message:'用户更新成功！',
						onshown:function(dialogRef){
							setTimeout(function(){
								dialogRef.close();
							}, 500);
						},
						onhidden: function(dialogRef){
							window.location.reload();
						}});
				}else{
					BootstrapDialog.show({message:'用户更新失败！'+'<br>原因：'+jdata.ajax_msg});
				}
				$("form#form_user_update input[name='{$Think.config.token_name}']").val(jdata.ajax_token);
				return false;
			});
		});
		
		//用户查询
		$('#button_user_query').on('click',function(){
			var user_account = $("#user_query_account").val();
			//alert(user_account);
			//把要查询的项目都存放到cookie中，查询条件到ajax的控制器中组合即可
			//$.cookie('xxx','xxx');
			$.cookie('cookie_wf_user_account',user_account);
			user_query(1); //调用user_query函数（下面），默认为页数为1即首页
		});
		
		//用户信息excel导出
		$('button#user_excel_export').on('click',function(){
			var url = "{:U('Admin/Index/user_excel_export')}";
			location.href = url;
		});
		
		//user每页显示多少记录的选择
		$("div").on('click','a[id^=page_]',function(){
			var per_page = $(this).attr('value');
			//var config_file = 'listrow_user.php';
			//var customized = '{"LISTROWS_USER":'+per_page+'}';
			var config_item='cfg_listrows_user';

			$.post("{:U('Admin/Index/config_update_from_db')}",{config_item:config_item,config_value:per_page},function(jdata){
				if(jdata.ajax_status==0){
					BootstrapDialog.show({
						type:BootstrapDialog.TYPE_SUCCESS,
						message:'更新配置成功！',
						onshown:function(dialogRef){
							setTimeout(function(){
								dialogRef.close();
							}, 500);
						},
						onhidden: function(dialogRef){
							window.location.reload();
					}});
				} else {
					BootstrapDialog.show({message:'更新配置失败！'+'<br>原因：'+jdata.ajax_msg});
				}
			});
			return false;
		});
	});

	function user_query(page_id = 1){    //news函数名 一定要和action中的第三个参数一致上面有
		var page_id = page_id;
		var url = "{:U('Admin/Index/user_query')}";
		$.get(url,{'p':page_id},function(jdata){
			//session失效时ajax提交后无法正常获取jdata返回时用下面方法处理
			if(typeof(jdata.ajax_status)!='number'){
				alert('会话超时，请重新登录！');
				window.location.href = "{:U('Home/Index/login')}";
				return false;
			}
			if(jdata.ajax_status==0){
				$("#tbody_user_query").html('');
				var query_value;
				$.each(jdata.ajax_data,function(i,val){
					query_value=
					'<tr><td><div>'+val.id+'</div></td>'+
					'<td><div id="user_edit_account'+val.id+'">'+val.account+'</div></td>'+
					'<td><div>'+val.group+'</div></td>'+
					'<td><div>'+val.login_ip+'</div></td>'+
					'<td><div>'+val.login_count+'</div></td>'+
					'<td><div>'+date('Y-m-d H:i:s',val.login_time)+'</div></td>'+
					'<td><div>'+(val.status==1?"启用":"禁用")+'</div></td>'+
					'<td><div>'+date('Y-m-d H:i:s',val.create_time)+'</div></td>'+
					'<td><div><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal_user_edit" id="user_edit'+val.id+'" value="'+val.id+'"><i class="fa fa-edit fa-fw"></i></button>'+
					'&nbsp;<button class="btn btn-danger btn-xs" id="user_del'+val.id+'" value="'+val.id+'"><i class="fa fa-times fa-fw"></i></button></td></tr>';
					
					$("#tbody_user_query").append(query_value);
				});
				var other_value=jdata.ajax_listrows-jdata.ajax_data.length;
				if(other_value>0){
					for(var j=0;j<other_value;j++){
						query_value='<tr><td colspan=9>&nbsp;<br>&nbsp;</td></tr>';
					}
					$("#tbody_user_query").append(query_value);
				}
				
				// 分页 
				//query_value = '<div class="page">' + jdata['ajax_page'] + '</div>';
				query_value='<tr><td colspan=9><div><nav><ul class="pagination pagination-sm">'+jdata.ajax_page+'</ul></nav></div></td></tr>';
				$("#div_user_query").html('').append(query_value);
			
			}else{
				alert('查询失败！');
			}
		});
	}
*/
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
											<li><a><small>5条记录</small></a></li>
											<li><a><small>10条记录</small></a></li>
											<li><a><small>20条记录</small></a></li>
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
		gets: {}
    },
	methods: {
		"userlist": function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.user.list') }}";
			var perPage = 1; // 有待修改，将来使用配置项
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;
			axios.get(url,{
				params: {
					perPage: perPage,
					page: page
				},
				headers: {'X-Requested-With': 'XMLHttpRequest'}
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
	},
	mounted: function(){
		var _this = this;
		var url = "{{ route('admin.user.list') }}";
		axios.get(url, {
				params: {
					perPage: 1,
					page: 1
				}
			})
			.then(function (response) {
				//console.log(response);
				_this.gets = response.data;
				// alert(_this.gets);
			})
			.catch(function (error) {
				console.log(error);
			})
	}
});
</script>
@endsection