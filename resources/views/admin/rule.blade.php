@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Rule) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
/*$(document).ready(function(){
	// 一打开就加载所有组信息
	$.get("{:U('Admin/Index/get_group')}", function(jdata){
		$("#rule_add_group").append("<option value=''>---选择群组---</option>");
		var select_value;
		$.each(jdata.all_group,function(i,val){
			select_value='<option value="'+val.id+'" title="'+val.title+'">'+val.title+'</option>';
			$("#rule_add_group").append(select_value);
		});
	});

	// 一打开就加载所有PID信息
	$.get("{:U('Admin/Index/get_rule_pid')}",function(jdata){
		$("#get_rule_pid").append('<option value="0"></option>');
		var select_value;
		$.each(jdata.all_rule_pid,function(i,val){
			select_value='<option value="'+val.id+'" title="'+val.title+'">'+val.title+'</option>';
			$("#get_rule_pid").append(select_value);
			$("#rule_edit_pid").append(select_value);
		});
	});

	//选择用户组，显示相应的权限
	$("#rule_add_group").change(function(){
		var group_id=this.value;
		var all_checkbox=$('#rule_all input');
		
		all_checkbox.each(function(){$(this).prop('checked',false);});
		if(group_id.length==0){return true}
		
		$.get("{:U('Admin/Index/get_rule_id')}", {group_id:group_id},function(jdata){
			//填充用户组相应的权限，权限前面打钩。
			if(jdata.ajax_status==0){ //返回成功后，遍历数组（权限id）
				var rules_id = jdata.all_rule_id.rules.split(',');
				all_checkbox.each(function(){
					var tmpstr2 = $(this).attr('id');
					for(var j=0;j<rules_id.length;j++){
						if(rules_id[j]==tmpstr2){
							$(this).prop('checked',true);
						}
					}
				});
			} else {
				alert('权限查询失败!！');
			}	
		});
	});
	
	//规则添加
	$('form#form_rule_add').on('submit',function(e){
		e.preventDefault();
		var rule_name = $("#rule_name").val();
		var rule_title = $("#rule_title").val();
		var rule_pid = $("#get_rule_pid").val();
		var rule_status = $("#rule_status").prop("checked")?1:0;
		if(rule_name.length==0||rule_title.length==0){
			alert('内容不能为空');return false;
		}
		var rule_hash=$("#form_rule_add input[name='{$Think.config.token_name}']").val();
		var url="{:U('Admin/Index/rule_add')}";
		$.post(url,{rule_name:rule_name,rule_title:rule_title,rule_pid:rule_pid,rule_status:rule_status,{$Think.config.token_name}:rule_hash},function(jdata){
			if(jdata.ajax_status==0){
				BootstrapDialog.show({
					type:BootstrapDialog.TYPE_SUCCESS,
					message:'规则添加成功！',
					onshown:function(dialogRef){
						setTimeout(function(){
							dialogRef.close();
						}, 500);
					},
					onhidden:function(dialogRef){
						window.location.reload();
					}});
			}else{
				BootstrapDialog.show({message:'规则添加失败！'+'<br>原因：'+jdata.ajax_msg});
			}
			$("form#form_rule_add input[name='{$Think.config.token_name}']").val(jdata.ajax_token);
		});
		return false;
	});

	
	//规则删除（动态）
	$("div").on('click','button[id^=rule_del]',function(){
		var id = $(this).attr('value');
		var rule_del = $('#'+id).attr('name');
		BootstrapDialog.show({
			title: 'WARNING',
			message: '警告！确定要删除规则 <b>' + rule_del + '</b> 吗？<br>（注意：其子权限会同时被删除！）',
			type: BootstrapDialog.TYPE_WARNING,
			draggable: true,
			buttons: [{
				icon: 'glyphicon glyphicon-trash',
				label: 'Drop the rule',
				cssClass: 'btn-warning',
				autospin: true,
				action: function(dialogRef){
					dialogRef.enableButtons(false);
					dialogRef.setClosable(false);
					//dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
					setTimeout(function(){
						$.get("{:U('Admin/Index/rule_del')}",{id:id}, function(jdata){
							if(jdata.ajax_status==0){
								BootstrapDialog.show({
									type:BootstrapDialog.TYPE_SUCCESS,
									message:'删除规则成功！',
									onshown:function(dialogRef){
										setTimeout(function(){
											dialogRef.close();
										}, 500);
									},
									onhidden:function(dialogRef){
										window.location.reload();
									}});
							} else {
								BootstrapDialog.show({message:'删除规则失败！'+'<br>原因：'+jdata.ajax_msg});
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
	
	
	//规则编辑显示（动态）
	$("div").on('click','button[id^=rule_edit]',function(){
		var id=$(this).attr('value'); //.substr(9);
		$.get("{:U('Admin/Index/rule_edit')}",{id:id}, function(jdata){
			if(jdata.ajax_status==0){
				$("#rule_edit_id").val(jdata.ajax_data.id);
				$("#rule_edit_name").val(jdata.ajax_data.name);
				$("#rule_edit_title").val(jdata.ajax_data.title);
				jdata.ajax_data.status==1?$("#rule_edit_status").prop("checked",true):$("#rule_edit_status").prop("checked",false);
				$("#rule_edit_pid").val(jdata.ajax_data.pid);
			} else {
				BootstrapDialog.show({message:'查询规则失败！'+'<br>原因：'+jdata.ajax_msg});
			}
		});
	});
	
	//规则编辑更新（动态）
	$('#form_rule_edit_update').on('submit',function(e){
		e.preventDefault();
		var rule_id = $("#rule_edit_id").val();
		var rule_name = $("#rule_edit_name").val();
		var rule_title = $("#rule_edit_title").val();
		var rule_status = $("#rule_edit_status").prop("checked")?1:0;
		var rule_pid = $("#rule_edit_pid").val();
		var rule_token=$("#form_rule_edit_update input[name='{$Think.config.token_name}']");
		var rule_hash = rule_token.val();
		var url = "{:U('Admin/Index/rule_edit_update')}";
		$.post(url,{rule_id:rule_id,rule_name:rule_name,rule_title:rule_title,rule_status:rule_status,rule_pid:rule_pid,{$Think.config.token_name}:rule_hash},function(jdata){
			if(jdata.ajax_status==0){
				BootstrapDialog.show({
					type:BootstrapDialog.TYPE_SUCCESS,
					message:'规则更新成功！！',
					onshown:function(dialogRef){
						setTimeout(function(){
							dialogRef.close();
						},500);
					},
					onhidden:function(dialogRef){
						window.location.reload();
					}});
			}else{
				BootstrapDialog.show({message:'规则更新失败！'+'<br>原因：'+jdata.ajax_msg});
			}
			rule_token.val(jdata.ajax_token);
		});
		return false;
	});
	
	
	//用户组的规则更新
	$('button#rule_update').click(function(){
		var rule_checked = '';
		var all_checkbox = $('#rule_all input');
		var array_rules=[];

		all_checkbox.each(function(){
			//var tmpstr2 = $(this).val().split('_');
			var checked_id = $(this).attr('id');
			
			if($(this).prop('checked')==true){
				array_rules.push(checked_id);
			}
		});
		
		//排序
		array_rules = array_rules.sort(function(a,b){
			return a-b;
		});
		
		//数组变字符串
		rule_checked = array_rules.join(',');
		//alert(array_rules.join(','));return false;
		
		//去掉最后一个字符逗号
		//var reg=/,$/gi;
		//rule_checked = rule_checked.replace(reg,"");
		//alert(rule_checked);return false;
		
		var group_id = $("#rule_add_group").val();
		
		var url = "{:U('Admin/Index/rule_update')}";
		$.post(url,{group_id:group_id,rule_checked:rule_checked},function(jdata){
			if(jdata.ajax_status==0){
				BootstrapDialog.show({
					type:BootstrapDialog.TYPE_SUCCESS,
					message:'规则更新成功！',
					onshown:function(dialogRef){
						setTimeout(function(){
							dialogRef.close();
						},500);
					}
					//onhidden: function(dialogRef){
					//window.location.reload();
					//}
				});
			}else{
				BootstrapDialog.show({message:'规则更新失败！'+'<br>原因：'+jdata.ajax_msg});
			}
		});
	});
	
	//选择顶级checkbox，子checkbox都选中
	$('#rule_all input').click(function(){
		var current_checkbox = $(this);
		var current_checkbox_id = current_checkbox.attr('id');

		var all_checkbox = $('#rule_all input');
		all_checkbox.each(function(){
			var all_checkbox_id = $(this).val();
			
			//凡是allpid中含有的上一级ID，即被选中或去除
			all_checkbox_id = all_checkbox_id.split(',');
			for(var i=0;i<all_checkbox_id.length;i++){
				if (current_checkbox_id == all_checkbox_id[i]) {
					$(this).prop('checked',current_checkbox.prop('checked'));
				}
			}
		});
		return true;
	});

	//复位控件内容
	$('#button_rule_new').click(function(){
		$('#rule_title').val('');
		$('#rule_name').val('');
	});

});*/
</script>	
@endsection

@section('my_body')
@parent
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Rule Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					权限管理
				</div>
				<div class="panel-body">
					<div class="row">
						<form role="form">
							<div class="col-lg-3">
								<div class="form-group">
									<select name="group_id" id="rule_add_group" class="form-control input-sm" placeholder="用户组"></select>
								</div>
							</div>
							<div class="col-lg-9">
								<button type="button" class="btn btn-default btn-sm" id="rule_update">更新</button>&nbsp;&nbsp;
								<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">新 建</button>&nbsp;&nbsp;
							</div>
						</form>
						<div class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div>
							<div class="table-responsive">
								<table class="table table-condensed">
									<thead>
										<tr>
											<th><div>ID</div></th>
											<th><div>规则名称</div></th>
											<th><div>控制器/方法</div></th>
											<th><div>状态</div></th>
											<th><div>创建时间</div></th>
											<th><div>操作</div></th>
										</tr>
									</thead>
									<tbody id='rule_all'>
									<foreach name="data" item="vo">
										<tr>
											<td><div align="center">{$vo.id}</div></td>
											<td><div><input id="{$vo.id}" type="checkbox" value="{$vo.allpid}" name="{$vo.title}">&nbsp;&nbsp;<if condition=" $vo.level eq 0 ">{$vo['title']}(顶级)<else/>{$vo['level']*2|str_repeat="&nbsp;",###}├{$vo['level']|str_repeat="─",###}{$vo.title}</if></div>
											</td>
											<td><div>{$vo.name}</div></td>
											<td><div><if condition=" $vo.status eq 1 ">有效<else/><span style="color:#F00">无效</span></if></div></td>
											<td><div>{$vo.create_time|date="Y-m-d H:i:s",###}</div></td>
											<td><div><button id="rule_edit{$vo.id}" value="{$vo.id}" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal_rule_edit"><i class="fa fa-edit fa-fw"></i></button>&nbsp;<button id="rule_del{$vo.id}" value="{$vo.id}" class="btn btn-danger btn-xs"><i class="fa fa-times fa-fw"></i></button></div></td>
										</tr>
									</foreach>
									</tbody>
								</table>
								<div id="div_group_query" class="dropup"></div>
								
								<!-- 分页 -->
								<div class="page">
								  <div align="center">{$page} </div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="rule_list" v-cloak>
					<div class="form-group">
						<label class="control-label">create role</label>
						<input class="form-control input-sm" type="text" ref="rolecreateinput" />
						<button @click="rolecreate" class="btn btn-primary btn-sm">create role</button>
					</div>
					<div class="form-group">
						<label class="control-label">create permission</label>
						<input class="form-control input-sm" type="text" ref="permissioncreateinput" />
						<button @click="permissioncreate" class="btn btn-primary btn-sm">create permission</button>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">permissiongive role</label>
							<input class="form-control input-sm" type="text" ref="permissionrole" />
							<label class="control-label">permissiongive permission</label>
							<input class="form-control input-sm" type="text" ref="permissionpermission" />
							<button @click="permissiongive" class="btn btn-primary btn-sm">give permission</button>
							<button @click="permissionrevoke" class="btn btn-primary btn-sm">revoke permission</button>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">rolegive user</label>
							<input class="form-control input-sm" type="text" ref="roleuser" />
							<label class="control-label">rolegive role</label>
							<input class="form-control input-sm" type="text" ref="rolerole" />
							<button @click="rolegive" class="btn btn-primary btn-sm">give role</button>
							<button @click="roleremove" class="btn btn-primary btn-sm">remove role</button>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">user has roles show</label>
							<input class="form-control input-sm" type="text" ref="roleshow"/>
							<button @click="roleshow" class="btn btn-primary btn-sm">input user to show roles</button>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label">role has permissions show</label>
							<input class="form-control input-sm" type="text" ref="permissionshow"/>
							<button @click="permissionshow" class="btn btn-primary btn-sm">input role to show permissions</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal rule edit START-->
<form id="form_rule_edit_update" method="post">
<div class="modal fade" id="myModal_rule_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">编辑规则</h4>
		</div>
		<div class="modal-body">
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<div class="form-group">
						<label>规则ID</label>
						<input type="text" id="rule_edit_id" class="form-control input-sm" disabled="">
					</div>
					<div class="form-group">
						<label>规则控制器/方法</label>
						<input type="text" id="rule_edit_name" class="form-control input-sm">
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<label>规则名称</label>
						<input type="text" id="rule_edit_title" class="form-control input-sm">
					</div>
					<div class="form-group">
						<label>父级</label>
						<!-- <input type="text" id="rule_edit_pid" class="form-control input-sm"> -->
						<select id="rule_edit_pid" class="form-control input-sm"></select>
					</div>
				</div>
			</div>
		</div>
		</div>
		<div class="modal-footer">
			<label><input id="rule_edit_status" type="checkbox">启用</label>&nbsp;&nbsp;
			<button type="submit" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"></span> 更 新</button>
			<button type="button" id="button_rule_new" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
		</div>
	</div>
  </div>
</div>{__TOKEN__}
</form>
<!-- Modal rule edit END-->

<!-- Modal create rule start-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
	<div class="modal-content">
		<form id="form_rule_add" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">新建规则</h4>
			</div>
			<div class="modal-body">
					<div class="form-group">
						<label>父级</label>
						<select id="get_rule_pid" class="form-control input-sm"></select>
					</div>
					<div class="form-group">
						<label>名称</label>
						<input id="rule_title" type="text" class="form-control input-sm">
					</div>
					<div class="form-group">
						<label>控制器/方法</label>
						<input id="rule_name" type="text" class="form-control input-sm">
					</div>
					<div class="checkbox">
						<label><input id="rule_status" type="checkbox" checked="checked" value=""><b>启用</b></label>
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" id="button_rule_add" class="btn btn-primary"><span class="glyphicon glyphicon-asterisk"></span> 新建</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		{__TOKEN__}
		</form>
	</div>
  </div>
</div>
<!-- Modal create rule end-->
@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_rule = new Vue({
    el: '#rule_list',
    data: {
		gets: {}
    },
	methods: {
		"rulelist": function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.rule.list') }}";
			var perPage = 1; // 有待修改，将来使用配置项
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: perPage,
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
		alert_message: function (title, content) {
			this.$alert({
				title: title,
				content: content,
				backdrop: true
			// }, (msg) => {
			}, function (msg) {
				// callback after modal dismissed
				// this.$notify(`You selected ${msg}.`);
				// this.$notify('You selected ${msg}.');
				// window.setTimeout(function(){
					// window.location.href = "{{ route('admin.config.index') }}";
				// },1000);
			})
		},
		rolecreate: function () {
			// alert(event);
			// alert(this.$refs.rolecreateinput.value);
			// if(event.target.value.length==0){return false;}
			var rolename = this.$refs.rolecreateinput.value;
			if(rolename.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.role.create') }}";
			
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					rolename: rolename
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Role [' + rolename + '] failed to create!');
				} else {
					_this.alert_message('SUCCESS', 'Role [' + rolename + '] created successfully!');
				}
			})
			.catch(function (error) {
				// console.log(error);
				// alert(error.response.data.message);
				_this.alert_message('ERROR', error.response.data.message);
			})
		},
		permissioncreate: function () {
			var permissionname = this.$refs.permissioncreateinput.value;
			if(permissionname.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.permission.create') }}";
			
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					permissionname: permissionname
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Permission [' + permissionname + '] failed to create!');
				} else {
					_this.alert_message('SUCCESS', 'Permission [' + permissionname + '] created successfully!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
			})
		},
		permissiongive: function () {
			var rolename = this.$refs.permissionrole.value;
			var permissionname = this.$refs.permissionpermission.value;
			
			if(rolename.length==0||permissionname.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.permission.give') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					rolename: rolename,
					permissionname: permissionname
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Permission [' + permissionname + '] failed to update!');
				} else {
					_this.alert_message('SUCCESS', 'Permission [' + permissionname + '] updated successfully!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		},
		permissionrevoke: function () {
			var rolename = this.$refs.permissionrole.value;
			var permissionname = this.$refs.permissionpermission.value;
			
			if(rolename.length==0||permissionname.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.permission.revoke') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					rolename: rolename,
					permissionname: permissionname
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Permission [' + permissionname + '] failed to revoke!');
				} else {
					_this.alert_message('SUCCESS', 'Permission [' + permissionname + '] revoked successfully!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		},
		rolegive: function () {
			var username = this.$refs.roleuser.value;
			// var rolename = this.$refs.rolerole.value;
			// 提交为数组
			var rolename = [];
			rolename.push(this.$refs.rolerole.value);
			
			if(username.length==0||rolename.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.role.give') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					username: username,
					rolename: rolename
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Role [' + rolename + '] failed to update!');
				} else {
					_this.alert_message('SUCCESS', 'Role [' + rolename + '] updated successfully!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		},
		roleremove: function () {
			var username = this.$refs.roleuser.value;
			var rolename = this.$refs.rolerole.value;
			// var rolename = [];
			// rolename.push(this.$refs.rolerole.value);

			if(username.length==0||rolename.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.role.remove') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					username: username,
					rolename: rolename
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'Role [' + rolename + '] failed to remove!');
				} else {
					_this.alert_message('SUCCESS', 'Role [' + rolename + '] removed successfully!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		},
		roleshow: function () {
			var user = this.$refs.roleshow.value;

			if(user.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.role.show') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					user: user
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', '[' + user + '] role failed to read!');
				} else {
					_this.alert_message('SUCCESS', '['+ user + '] has roles: [' + response.data + ']!');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		},
		permissionshow: function () {
			var role = this.$refs.permissionshow.value;

			if(role.length==0){return false;}
			var _this = this;
			var url = "{{ route('admin.permission.show') }}";
			// alert(permissionname);return false;
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					role: role
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					_this.alert_message('WARNING', 'permission [' + role + '] failed to read!');
				} else {
					_this.alert_message('SUCCESS', '[' + role + '] permission is [' + response.data[0].name + '] !');
				}
			})
			.catch(function (error) {
				_this.alert_message('ERROR', error.response.data.message);
				_this.alert_message('ERROR', '已经存在！不要重复追加！');
			})
		}
	},
	mounted: function(){
		// var _this = this;
		// var url = "{{ route('admin.rule.list') }}";
		// axios.get(url, {
				// params: {
					// perPage: 1,
					// page: 1
				// }
			// })
			// .then(function (response) {
				// console.log(response);
				// _this.gets = response.data;
				// alert(_this.gets);
			// })
			// .catch(function (error) {
				// console.log(error);
				// alert(error);
			// })
	}
});
</script>
@endsection
