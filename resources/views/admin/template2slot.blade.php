@extends('admin.layouts.adminbase')

@section('my_title')
Admin(template2slot) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="template2slot_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Template2slot Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Template2slot 管理
				</div>
				<div class="panel-body">
					<div class="row">

					<div class="panel-body">
						<tabs v-model="currenttabs">
							<tab title="Template2slot List">
								<!--template2slot列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->

									<div class="col-lg-12">
										<div class="panel panel-default">
											<div class="panel-heading"><label>编辑 Template2Slot</label></div>
											<div class="panel-body">

												<div class="col-lg-3">
													<div class="form-group">
														<input placeholder="过滤器: Template 创建开始时间" type="text" class="form-control"><br>
														<input placeholder="过滤器: Template 创建结束时间" type="text" class="form-control">
													</div>
													<div class="form-group">
														<label>Select Template</label><br>
														<multi-select @change="change_template()" v-model="template_select" :options="template_options"  :limit="1" filterable collapse-selected size="sm" />
													</div>
													<btn @click="template2slot_review" type="primary" size="sm">Review</btn>&nbsp;
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<input placeholder="过滤器: Slot 创建开始时间" type="text" class="form-control"><br>
														<input placeholder="过滤器: Slot 创建结束时间" type="text" class="form-control">
													</div>
													<div class="form-group">
														<label>Select slot(s)</label><br>
														<multi-select v-model="slot_select" :options="slot_options"  filterable collapse-selected size="sm" />
													</div>
													<btn @click="template2slot_add" type="primary" size="sm">Add</btn>&nbsp;
												</div>

												<div class="col-lg-6">

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
																	<btn @click="slot_down(val.id,index)" type="primary" size="xs"><i class="fa fa-arrow-down fa-fw"></i></btn>&nbsp;
																	<btn @click="slot_up(val.id,index)" type="primary" size="xs"><i class="fa fa-arrow-up fa-fw"></i></btn>&nbsp;
																	<btn @click="template2slot_remove(index)" type="danger" size="xs"><i class="fa fa-times fa-fw"></i></btn></div></td>
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
							<tab title="Review Template2slot">
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
var vm_template2slot = new Vue({
    el: '#template2slot_list',
    data: {
		gets: {},
		// perpage: {{ $config['PERPAGE_RECORDS_FOR_TEMPLATE'] }},
		template_select: [],
        template_options: [
			// {value: 1, label:'Option1'},
			// {value: 2, label:'Option2'},
			// {value: 3, label:'Option3333333333'},
			// {value: 4, label:'Option4'},
			// {value: 5, label:'Option5'}
        ],
		slot_select: [],
        slot_options: [
			// {value: 1, label:'Option1'},
			// {value: 2, label:'Option2'},
			// {value: 3, label:'Option3333333333'},
			// {value: 4, label:'Option4'},
			// {value: 5, label:'Option5'}
        ],

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
			return arr.reverse();
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
		// template2slot列表
		template2slotgets: function(){
			var _this = this;
			var url = "{{ route('admin.template2slot.template2slotgets') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url)
			.then(function (response) {
				if (typeof(response.data) != "undefined") {
					var json = response.data.template;
					_this.template_options = _this.json2selectvalue(json);
					json = response.data.slot;
					_this.slot_options = _this.json2selectvalue(json);
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// 选择template
		change_template: function () {
			var _this = this;
			var templateid = _this.template_select[0];
			// console.log(slotid);//return false;
			if (templateid==undefined) {
				_this.gets = {};
				return false;
			}
			
			var url = "{{ route('admin.template2slot.changetemplate') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					templateid: templateid
				}
			})
			.then(function (response) {
				if (response.data != undefined) {
					_this.gets = response.data;
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
		},
		// sort向前
		slot_up: function (slotid, index) {
			var _this = this;
			if (slotid==undefined || index==0) return false;
			var templateid = _this.template_select[0];
			
			var url = "{{ route('admin.template2slot.slotsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					slotid: slotid,
					index: index,
					templateid: templateid,
					sort: 'up'
				}
			})
			.then(function (response) {
				if (response.data != undefined) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		// sort向后
		slot_down: function (slotid, index) {
			var _this = this;
			if (slotid==undefined || index==_this.gets.length-1) return false;
			var templateid = _this.template_select[0];
			var url = "{{ route('admin.template2slot.slotsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					slotid: slotid,
					index: index,
					templateid: templateid,
					sort: 'down'
				}
			})
			.then(function (response) {
				if (response.data != undefined) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		template2slot_remove: function (index) {
			var _this = this;
			var templateid = _this.template_select[0];
			// console.log(slotid);
			// console.log(fieldid);
			// return false;
			
			if (templateid == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.template2slot.template2slotremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					templateid: templateid,
					index: index
				}
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data != undefined) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
			
		},
		template2slot_add: function () {
			var _this = this;
			var templateid = _this.template_select[0];
			var slotid = _this.slot_select;
			// console.log(slotid);
			// console.log(fieldid);
			// return false;
			
			if (slotid == undefined || templateid == undefined) return false;
			
			var url = "{{ route('admin.template2slot.template2slotadd') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					slotid: slotid,
					templateid: templateid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data != undefined) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		template2slot_review: function () {
			
		}
	},
	mounted: function(){
		// 显示所有template2slot
		this.template2slotgets();
	}
});
</script>
@endsection