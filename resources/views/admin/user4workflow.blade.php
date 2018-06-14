@extends('admin.layouts.adminbase')

@section('my_title')
Admin(user4workflow) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="user4workflow_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">User4workflow Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					User4workflow 管理
				</div>
				<div class="panel-body">
					<div class="row">

					<div class="panel-body">
						<tabs v-model="currenttabs">
							<tab title="User4workflow List">
								<!--user4workflow列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->

									<div class="col-lg-12">
										<div class="panel panel-default">
											<div class="panel-heading"><label>编辑 Slot2Field</label></div>
											<div class="panel-body">

												<div class="col-lg-4">
													<div class="form-group">
														<label>Select a User</label><br>
														<multi-select @change="change_user()" v-model="user_select" :options="user_options" :limit="1" filterable collapse-selected size="sm" />
													</div>
													
													
													<div class="form-group">
														<label>选择权限</label>
														<div class="checkbox">
															<label>
																<input name="checkbox_name_right" value="1" type="checkbox">Administrator
															</label>
														</div>
														<div class="checkbox">
															<label>
																<input name="checkbox_name_right" value="2" type="checkbox">Sender
															</label>
														</div>
														<div class="checkbox">
															<label>
																<input name="checkbox_name_right" value="4" type="checkbox">Receiver
															</label>
														</div>
														<div class="checkbox">
															<label>
																<input name="checkbox_name_right" value="8" type="checkbox">ReadOnly
															</label>
														</div>
													</div>
													
													
													<div class="form-group">
														<label>Substitute Time (minute)</label>
														<input id="substitute_time_input" class="form-control">
													</div>
													
													
													
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label>Select Substitute User(s)</label><br>
														<multi-select v-model="substituteuser_select" :options="substituteuser_options"  filterable collapse-selected size="sm" />
													</div>
													<btn @click="user4workflow_add" type="primary" size="sm">Add</btn>&nbsp;
												</div>

												<div class="col-lg-5">

													<div class="table-responsive">
														<table class="table table-condensed">
															<thead>
																<tr>
																	<th>sort</th>
																	<th>name</th>
																	<th>操作</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="(val, index) in gets">
																	<td><div>@{{ index }}</div></td>
																	<td><div>@{{ val.name }}</div></td>
																	<td><div>
																	<btn @click="user_down(val.id,index)" type="primary" size="xs"><i class="fa fa-arrow-down fa-fw"></i></btn>&nbsp;
																	<btn @click="user_up(val.id,index)" type="primary" size="xs"><i class="fa fa-arrow-up fa-fw"></i></btn>&nbsp;
																	<btn @click="user4workflow_remove(index)" type="danger" size="xs"><i class="fa fa-times fa-fw"></i></btn></div></td>
																</tr>
															</tbody>
														</table>
													</div>
												
												</div>
											</div>
										</div>
										
									</div>
									<div class="col-lg-4">

									
									
									
									</div>									
									
									
								</div>
							</tab>
							<tab title="Review User4workflow">
								<!--操作1-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div><br>-->


									
									
									
									
									
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
var vm_user4workflow = new Vue({
    el: '#user4workflow_list',
    data: {
		gets: {},
		// perpage: {{ $config['PERPAGE_RECORDS_FOR_SLOT'] }},
		user_select: [],
        user_options: [],
		substituteuser_select: [],
        substituteuser_options: [],

		// tabs索引
		currenttabs: 0
    },
	methods: {
		// 把laravel返回的结果转换成select能接受的格式
		json2selectvalue: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push({ value: key, label: json[key] });
			}
			// return arr.reverse();
			return arr;
		},
		json2gets: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push({ id: key, name: json[key] });
			}
			return arr;
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
		// user4workflow列表
		user4workflowgets: function(){
			var _this = this;
			var url = "{{ route('admin.user4workflow.user4workflowgets') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url)
			.then(function (response) {
				// console.log(response.data);
				// return false;
				var json = response.data;
				_this.user_options = _this.json2selectvalue(json);
				// json = response.data.slot;
				// _this.slot_options = _this.json2selectvalue(json);
				
				// if (typeof(response.data) == "undefined") {
					// alert(response);
					// _this.alert_exit();
				// }
				// _this.gets = response.data;
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 通过mailinglist选择slot
		change_user: function () {
			var _this = this;
			var userid = _this.user_select[0];
			// console.log(userid);return false;
			if (userid==undefined) {
				return false;
			}
			
			var url = "{{ route('admin.user4workflow.changeuser') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
				}
			})
			.then(function (response) {
				// if (response.data != undefined) {
					// var json = response.data;
					// _this.slot_options = _this.json2selectvalue(json);
				// }
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
		},
		// sort向前
		user_up: function (userid, index) {
			var _this = this;
			if (userid==undefined || index==0) return false;
			var slotid = _this.slot_select[0];
			var url = "{{ route('admin.user4workflow.usersort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					userid: userid,
					index: index,
					slotid: slotid,
					sort: 'up'
				}
			})
			.then(function (response) {
				if (response.data != undefined) {
					_this.change_slot();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// sort向后
		user_down: function (userid, index) {
			var _this = this;
			if (userid==undefined || index==_this.gets.length-1) return false;
			var slotid = _this.slot_select[0];
			var url = "{{ route('admin.user4workflow.usersort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					userid: userid,
					index: index,
					slotid: slotid,
					sort: 'down'
				}
			})
			.then(function (response) {
				if (response.data != undefined) {
					_this.change_slot();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		user4workflow_remove: function (index) {
			var _this = this;
			var slotid = _this.slot_select[0];
			
			if (slotid == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.user4workflow.user4workflowremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					slotid: slotid,
					index: index
				}
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data != undefined) {
					_this.change_slot();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
			
		},
		user4workflow_add: function () {
			var _this = this;
			var slotid = _this.slot_select[0];
			var userid = _this.user_select;
			// console.log(slotid);
			// console.log(userid);
			// return false;
			
			if (slotid == undefined || userid == undefined) return false;
			
			var url = "{{ route('admin.user4workflow.user4workflowadd') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					slotid: slotid,
					userid: userid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data != undefined) {
					_this.user_select = [];
					_this.change_slot();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		user4workflow_review: function () {
			
		}
	},
	mounted: function(){
		// 显示所有user4workflow
		this.user4workflowgets();
	}
});
</script>
@endsection