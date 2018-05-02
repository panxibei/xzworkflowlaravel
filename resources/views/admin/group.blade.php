@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Group) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
/*
$(document).ready(function(){
	// 一打开就默认显示所有群组列表
	$.cookie('cookie_wf_group_title','');
	group_query(1);

	//用户组添加
	$('form#form_group_add').on('submit',function(e){
		e.preventDefault();
		var group_title = $("#group_add_title").val();
		var group_status = $("#group_add_status").prop("checked")?1:0;
		if(group_title.length==0){alert('内容不能为空');return false;}
		var group_hash = $("#form_group_add input[name='{$Think.config.token_name}']").val();
		var url = "{:U('Admin/Index/group_add')}";
		$.post(url,{group_title:group_title,group_status:group_status,{$Think.config.token_name}:group_hash},function(jdata){
			if(jdata.ajax_status==0){
				BootstrapDialog.show({
					type:BootstrapDialog.TYPE_SUCCESS,
					message:'新建用户组成功！',
					onshown:function(dialogRef){
						setTimeout(function(){
							dialogRef.close();
						}, 500);
					},
					onhidden: function(dialogRef){
						window.location.reload();
					}});
			}else{
				BootstrapDialog.show({message:'新建用户组失败！'+'<br>原因：'+jdata.ajax_msg});
			}
			$("form#form_group_add input[name='{$Think.config.token_name}']").val(jdata.ajax_token);
		});
		return false;
	});

	//用户组删除（动态）
	$("div").on('click','button[id^=group_del]',function(){
		var id = $(this).attr('value'); //.substr(8);
		var group_del_title = $('div#group_del_title'+id).html();
		BootstrapDialog.show({
			title: 'WARNING',
			message: '警告！确定要删除用户组 <b>' + group_del_title + '</b> 吗？',
			type: BootstrapDialog.TYPE_WARNING,
			draggable: true,
			buttons: [{
				icon: 'glyphicon glyphicon-trash',
				label: '删除用户组',
				cssClass: 'btn-warning',
				autospin: true,
				action: function(dialogRef){
					dialogRef.enableButtons(false);
					dialogRef.setClosable(false);
					//dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
					setTimeout(function(){
						$.get("{:U('Admin/Index/group_del')}",{id:id}, function(jdata){
							if(jdata.ajax_status==0){
								BootstrapDialog.show({
									type:BootstrapDialog.TYPE_SUCCESS,
									message:'删除用户组成功！',
									onshown:function(dialogRef){
										setTimeout(function(){
											dialogRef.close();
										}, 500);
									},
									onhidden: function(dialogRef){
										window.location.reload();
									}});
							} else {
								BootstrapDialog.show({message:'删除用户组失败！'+'<br>原因：'+jdata.ajax_msg});
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

	//用户组编辑显示（动态）
	$("div").on('click','button[id^=group_edit]',function(){
		var id=$(this).attr('value'); //.substr(9);
		$.get("{:U('Admin/Index/group_edit')}",{id:id}, function(jdata){
			if(jdata.ajax_status==0){
				$("#group_id").val(jdata['id']);
				$("#group_title").val(jdata['title']);
				jdata['status']==1?$("#group_update_status").prop("checked",true):$("#group_update_status").prop("checked",false);
			} else {
				alert('查询用户组失败'); 
			}
		});
	});

	//用户组更新（动态）
	$('#form_group_update').on('submit', function(e) {
		e.preventDefault();
		var group_id = $("#group_id").val();
		var group_title = $("#group_title").val();
		var group_status = $("#group_update_status").prop("checked") ? 1 : 0;
		
		var group_hash = $("#form_group_update input[name='{$Think.config.token_name}']").val();
		
		if(group_title.length==0){alert('内容不能为空');return false;}
		var url = "{:U('Admin/Index/group_update')}";
		$.post(url,{group_id:group_id,group_title:group_title,group_status:group_status,{$Think.config.token_name}:group_hash},function(jdata){
			if(jdata.ajax_status==0){
				BootstrapDialog.show({
					type:BootstrapDialog.TYPE_SUCCESS,
					message:'用户组更新成功！',
					onshown:function(dialogRef){
						setTimeout(function(){
							dialogRef.close();
						}, 500);
					},
					onhidden: function(dialogRef){
						window.location.reload();
					}});
			} else {
				BootstrapDialog.show({message:'用户组更新失败！'+'<br>原因：'+jdata.ajax_msg});
			}
			$("#form_group_update input[name='{$Think.config.token_name}']").val(jdata.ajax_token);
		});
		return false;
	});

	//群组查询
	$('#form_group_query').on('submit',function(e){
		e.preventDefault();
		var group_title = $("#group_query_title").val();
		//把要查询的项目都存放到cookie中，查询条件到ajax的控制器中组合即可
		//$.cookie('xxx','xxx');
		$.cookie('cookie_wf_group_title',group_title);
		group_query(1); //调用user_query函数（下面），默认为页数为1即首页
	});
	
	//group每页显示多少记录的选择
	$("div").on('click','a[id^=page_]',function(){
		var per_page = $(this).attr('value');
		//var config_file = 'listrow_group.php';
		//var customized = '{"LISTROWS_GROUP":' + per_page + '}';
		var config_item='cfg_listrows_group';

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
			}else{
				BootstrapDialog.show({message:'更新配置失败！'+'<br>原因：'+jdata.ajax_msg});
			}
		});
		return false;
	});
	
});

function group_query(page_id){    //news函数名 一定要和action中的第三个参数一致上面有
	var page_id = page_id;
	var url = "{:U('Admin/Index/group_query')}";
	$.get(url,{'p':page_id},function(jdata){
		//session失效时ajax提交后无法正常获取jdata返回时用下面方法处理
		if(typeof(jdata.ajax_status)!='number'){
			alert('会话超时，请重新登录！');
			window.location.href = "{:U('Home/Index/login')}";
			return false;
		}
		if(jdata.ajax_status==0){
			$("#tbody_group_query").html('');
			var query_value;
			$.each(jdata.ajax_data,function(i,val){      
				query_value=
				'<tr><td><div>'+val.id+'</div></td>'+
				'<td><div id="group_del_title'+val.id+'">'+val.title+'</div></td>'+
				'<td><div>'+(val.status==1?"已启用":"已禁用")+'</div></td>'+
				'<td><div><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal_group_edit" id="group_edit'+val.id+'" value="'+val.id+'"><i class="fa fa-edit fa-fw"></i></button>&nbsp;'+
				'<button class="btn btn-danger btn-xs" id="group_del'+val.id+'" value="'+val.id+'"><i class="fa fa-times fa-fw"></i></button></td></tr>';
				
				$("#tbody_group_query").append(query_value);
			});
			var other_value=jdata.ajax_listrows-jdata.ajax_data.length;
			if(other_value>0){
				for(var j=0;j<other_value;j++){
					query_value='<tr><td colspan=4>&nbsp;<br>&nbsp;</td></tr>';
				}
				$("#tbody_group_query").append(query_value);
			}
			
			// 分页 
			query_value='<tr><td colspan=4><div><nav><ul class="pagination pagination-sm">'+jdata.ajax_page+'</ul></nav></div></td></tr>';
			$("#div_group_query").html('').append(query_value);
		
		} else {
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
			<h1 class="page-header">Group Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					群组管理
				</div>
				<div class="panel-body">
					<div class="row">				
						<form id="form_group_query" action="{:U('Admin/Index/group_query')}" method="post">
						<div class="col-lg-3">
							<div class="form-group">
								<input class="form-control input-sm" type="text" name="title" id="group_query_title" placeholder="用户组" />
							</div>
							</div>
							<div class="col-lg-9">
								<button type="submit" id="submit" class="btn btn-default btn-sm">查询</button>&nbsp;
								<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">新建</button>
							</div>
						</form>
						<div class="col-lg-12">
							<br><div style="background-color:#c9e2b3;height:1px"></div>
							<div id="group_list" class="table-responsive" v-cloak>
								<table class="table table-condensed">
									<thead>
										<tr>
											<th>ID</th>
											<th>角色/组</th>
											<th>状态</th>
											<th>创建时间</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody id="tbody_group_query">

										<tr v-for="val in gets.data">
											<td><div>@{{ val.id }}</div></td>
											<td><div v-bind:id="'user_edit_account' + val.id">@{{ val.title }}</div></td>
											<td><div>@{{ val.delete_at ? "禁用" : "启用" }}</div></td>
											<td><div>@{{ val.created_at }}</div></td>
											<td><div><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal_user_edit" v-bind:id="'user_edit' + val.id" v-bind:value="val.id"><i class="fa fa-edit fa-fw"></i></button>
											&nbsp;<button class="btn btn-danger btn-xs" v-bind:id="'user_del' + val.id" v-bind:value="val.id"><i class="fa fa-times fa-fw"></i></button></div></td>
										</tr>

									</tbody>
								</table>

								<div id="div_group_query" class="dropup">
									<tr><td colspan="9"><div><nav>

										<ul class="pagination pagination-sm">
											<li><a aria-label="Previous" @click="grouplist(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

											<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
												<a v-if="n==1" @click="grouplist(1, gets.last_page)" href="javascript:;">1</a>
												<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="grouplist(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
												<a v-else-if="n==2||n==gets.last_page" href="javascript:;">...</a>
											</li>&nbsp;

											<li><a aria-label="Next" @click="grouplist(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
											<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

												<div class="col-xs-2">
												<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="grouplist($event.target.value, gets.last_page)">
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

<!-- Modal group edit -->
<form id="form_group_update" action="{:U('Admin/Index/group_update')}" method="post">
<div class="modal fade" id="myModal_group_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">编辑用户组</h4>
	  </div>
	  <div class="modal-body">
		<div class="form-group">
			<label for="group_id" class="control-label">用户组ID</label>
			<input type="text" id="group_id" class="form-control input-sm">
		</div>
		<div class="form-group">
			<label for="group_title" class="control-label">用户组名称</label>
			<input type="text" id="group_title" class="form-control input-sm">
		</div>
		<div class="form-group">
			<label for="group_title" class="control-label">拥有权限</label>
			<input type="text" id="group_rule" class="form-control input-sm">
		</div>
	  </div>
	  <div class="modal-footer">
		<label><input id="group_update_status" type="checkbox">启用</label>&nbsp;&nbsp;
		<button type="submit" id="group_add_or_update" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"></span> 更 新</button>
		<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>{__TOKEN__}
</form>

<!-- Modal create group-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
		<form id="form_group_add" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">新建用户组</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="group_add_title" class="control-label">用户组名称</label>
					<input id="group_add_title" type="text" class="form-control input-sm">
				</div>
				<div class="checkbox">
					<label><input id="group_add_status" type="checkbox" checked="checked" value=""><b>启用</b></label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" id="group_add"><span class="glyphicon glyphicon-asterisk"></span> 新建</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		{__TOKEN__}</form>
		</div>
	</div>
</div>
@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_group = new Vue({
    el: '#group_list',
    data: {
		gets: {}
    },
	methods: {
		"grouplist": function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.group.list') }}";
			var perPage = 1; // 有待修改，将来使用配置项
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			_this.gets.current_page = page;
			axios.get(url, {
					params: {
						perPage: perPage,
						page: page
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
	},
	mounted: function(){
		var _this = this;
		var url = "{{ route('admin.group.list') }}";
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