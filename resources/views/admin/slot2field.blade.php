@extends('admin.layouts.adminbase')

@section('my_title')
Admin(slot2field) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="slot2field_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Slot2field Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Slot2field 管理
				</div>
				<div class="panel-body">
					<div class="row">

					<div class="panel-body">
						<tabs v-model="currenttabs">
							<tab title="Slot2field List">
								<!--slot2field列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->

									<div class="col-lg-12">
										<div class="panel panel-default">
											<div class="panel-heading"><label>编辑 Slot2Field</label></div>
											<div class="panel-body">

												<div class="col-lg-3">
													<div class="form-group">
														<input placeholder="过滤器: Slot 创建开始时间" type="text" class="form-control"><br>
														<input placeholder="过滤器: Slot 创建结束时间" type="text" class="form-control">
													</div>
													<div class="form-group">
														<label>Select Slot</label><br>
														<multi-select @change="change_slot()" v-model="slot_select" :options="slot_options"  :limit="1" filterable collapse-selected size="sm" />
													</div>
													<btn @click="slot2field_review" type="primary" size="sm">Review</btn>&nbsp;
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<input placeholder="过滤器: Field 创建开始时间" type="text" class="form-control"><br>
														<input placeholder="过滤器: Field 创建结束时间" type="text" class="form-control">
													</div>
													<div class="form-group">
														<label>Select field(s)</label><br>
														<multi-select v-model="field_select" :options="field_options"  filterable collapse-selected size="sm" />
													</div>
													<btn @click="slot2field_add" type="primary" size="sm">Add</btn>&nbsp;
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
																	<btn @click="field_down(val.id,index)" type="primary" size="xs"><i class="fa fa-arrow-down fa-fw"></i></btn>&nbsp;
																	<btn @click="field_up(val.id,index)" type="primary" size="xs"><i class="fa fa-arrow-up fa-fw"></i></btn>&nbsp;
																	<btn @click="slot2field_remove(index)" type="danger" size="xs"><i class="fa fa-times fa-fw"></i></btn></div></td>
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
							<tab title="Review Slot2field">
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
var vm_slot2field = new Vue({
    el: '#slot2field_list',
    data: {
		gets: {},
		// perpage: {{ $config['PERPAGE_RECORDS_FOR_SLOT'] }},
		slot_select: [],
        slot_options: [],
		field_select: [],
        field_options: [],

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
		// slot2field列表
		slot2fieldgets: function(){
			var _this = this;
			var url = "{{ route('admin.slot2field.slot2fieldgets') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url)
			.then(function (response) {
				// console.log(response.data);
				// return false;
				var json = response.data.slot;
				_this.slot_options = _this.json2selectvalue(json);
				json = response.data.field;
				_this.field_options = _this.json2selectvalue(json);
				
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
		// 选择slot
		change_slot: function () {
			var _this = this;
			var slotid = _this.slot_select[0];
			// console.log(slotid);//return false;
			if (slotid==undefined) {
				_this.gets = {};
				return false;
			}
			
			var url = "{{ route('admin.slot2field.changeslot') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					slotid: slotid
				}
			})
			.then(function (response) {
				// console.log(_this.gets);return false;
				if (response.data != undefined && response.data != null) {
					_this.gets = response.data;
				} else {
					_this.gets = '';
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
		},
		// sort向前
		field_up: function (fieldid, index) {
			var _this = this;
			if (fieldid==undefined || index==0) return false;
			var slotid = _this.slot_select[0];
			var url = "{{ route('admin.slot2field.fieldsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					fieldid: fieldid,
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
		field_down: function (fieldid, index) {
			var _this = this;
			if (fieldid==undefined || index==_this.gets.length-1) return false;
			var slotid = _this.slot_select[0];
			var url = "{{ route('admin.slot2field.fieldsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					fieldid: fieldid,
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
		slot2field_remove: function (index) {
			var _this = this;
			var slotid = _this.slot_select[0];
			// console.log(slotid);
			// console.log(fieldid);
			// return false;
			
			if (slotid == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.slot2field.slot2fieldremove') }}";
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
		slot2field_add: function () {
			var _this = this;
			var slotid = _this.slot_select[0];
			var fieldid = _this.field_select;
			// console.log(slotid);
			// console.log(fieldid);
			// return false;
			
			if (slotid == undefined || fieldid == undefined) return false;
			
			var url = "{{ route('admin.slot2field.slot2fieldadd') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					slotid: slotid,
					fieldid: fieldid
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
		slot2field_review: function () {
			
		}
	},
	mounted: function(){
		// 显示所有slot2field
		this.slot2fieldgets();
	}
});
</script>
@endsection