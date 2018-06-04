@extends('admin.layouts.adminbase')

@section('my_title')
Admin(Field) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="field_list" v-cloak>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Field Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Field 管理
				</div>
				<div class="panel-body">
					<div class="row">

					<div class="panel-body">
						<tabs>
							<tab title="Field List">
								<!--field列表-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div>-->
									<div class="table-responsive">
										<table class="table table-condensed">
											<thead>
												<tr>
													<th>id</th>
													<th>name</th>
													<th>type</th>
													<th>bgcolor</th>
													<th>readonly</th>
													<th>value</th>
													<th>placeholder</th>
													<th>regexp</th>
													<th>helpblock</th>
													<th>created_at</th>
													<th>updated_at</th>
													<th>操作（保留）</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="val in gets.data">
													<td><div>@{{ val.id }}</div></td>
													<td><div>@{{ val.name }}</div></td>
													<td><div>@{{ val.type }}</div></td>
													<td><div>@{{ val.bgcolor }}</div></td>
													<td><div>@{{ val.readonly }}</div></td>
													<td><div>@{{ val.value }}</div></td>
													<td><div>@{{ val.placeholder }}</div></td>
													<td><div>@{{ val.regexp }}</div></td>
													<td><div>@{{ val.helpblock }}</div></td>
													<td><div>@{{ val.created_at }}</div></td>
													<td><div>@{{ val.updated_at }}</div></td>
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
																<li><a aria-label="Previous" @click="fieldgets(--gets.current_page, gets.last_page)" href="javascript:;"><i class="fa fa-chevron-left fa-fw"></i>上一页</a></li>&nbsp;

																<li v-for="n in gets.last_page" v-bind:class={"active":n==gets.current_page}>
																	<a v-if="n==1" @click="fieldgets(1, gets.last_page)" href="javascript:;">1</a>
																	<a v-else-if="n>(gets.current_page-3)&&n<(gets.current_page+3)" @click="fieldgets(n, gets.last_page)" href="javascript:;">@{{ n }}</a>
																	<a v-else-if="n==2||n==gets.last_page">...</a>
																</li>&nbsp;

																<li><a aria-label="Next" @click="fieldgets(++gets.current_page, gets.last_page)" href="javascript:;">下一页<i class="fa fa-chevron-right fa-fw"></i></a></li>&nbsp;&nbsp;
																<li><span aria-label=""> 共 @{{ gets.total }} 条记录 @{{ gets.current_page }}/@{{ gets.last_page }} 页 </span></li>

																	<div class="col-xs-2">
																	<input class="form-control input-sm" type="text" placeholder="到第几页" v-on:keyup.enter="fieldgets($event.target.value, gets.last_page)">
																	</div>

																<div class="btn-group">
																<button class="btn btn-sm btn-default dropdown-toggle" aria-expanded="false" aria-haspopup="true" type="button" data-toggle="dropdown">每页@{{ perpage }}条<span class="caret"></span></button>
																<ul class="dropdown-menu">
																<li><a @click="configperpageforfield(2)" href="javascript:;"><small>2条记录</small></a></li>
																<li><a @click="configperpageforfield(5)" href="javascript:;"><small>5条记录</small></a></li>
																<li><a @click="configperpageforfield(10)" href="javascript:;"><small>10条记录</small></a></li>
																<li><a @click="configperpageforfield(20)" href="javascript:;"><small>20条记录</small></a></li>
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
							<tab title="Create Field">
								<!--操作1-->
								<div class="col-lg-12">
									<br><!--<br><div style="background-color:#c9e2b3;height:1px"></div><br>-->
									<div class="col-lg-8">
										<div class="panel panel-default">
											<div class="panel-heading"><label>选择元素</label></div>
											<div class="panel-body">

												<div class="col-lg-6">
													<div class="form-group">
														<label>名称</label>
														<input v-model="field_add_name" type="text" class="form-control input-sm">
													</div>
													<div class="form-group">
														<label>类型</label><br>
														<multi-select v-model="field_selected_add_type" :options="field_options_add_type" :limit="1" @change="field_add_type_change" filterable collapse-selected size="sm" placeholder="Select the type ..."/>
													</div>
													<div class="form-group">
														<label>背景色</label>
														<div id="field_add_bgcolor_id" class="input-group colorpicker-component" title="Select background color">
															<input v-model="field_add_bgcolor" type="text" class="form-control input-sm input-group colorpicker-component" pattern0="^#[0-9a-fA-F]{6}$" placeholder="#ffffff">
															<span class="input-group-addon"><i></i></span>
														</div>
													</div>
													<script type="text/javascript">
													$(function() {
														$('#field_add_bgcolor_id').colorpicker({
															format:"hex"
															// format:"rgb"
														}).on('changeColor',function(e){
															vm_field.field_add_bgcolor = e.color.toString();
															// alert(vm_field.field_add_bgcolor);
															
															// var field_type = $("#field_add_type").val();
															// var obj_field_add=new Object();
															// switch(field_type.charAt(0))
															// {
																// case '1': //text
																	// obj_field_add = $('#field_add_example_text');
																	// break;
														
																// case '2': //true/false
																	// obj_field_add = $('#field_add_example_true_or_false');
																	// break;
															
																// case '3': //number
																	// obj_field_add = $('#field_add_example_number');
																	// break;
															
																// case '4': //date
																	// obj_field_add = $('#field_add_example_date');
																	// break;
															
																// case '5': //textfield
																	// obj_field_add = $('#field_add_example_textfield');
																	// break;
															
																// case '6': //radiogroup
																	// obj_field_add=$('label[id^=field_add_example_radiogroup');
																	// break;
															
																// case '7': //checkboxgroup
																	// obj_field_add=$('label[id^=field_add_example_checkboxgroup');
																	// break;
															
																// case '8': //combobox
																	// obj_field_add=$('#field_add_example_combobox');
																	// break;
															
																// case '9': //true/false
																	// obj_field_add=$('#field_add_example_file');
																	// break;
															// }
															
															// if(field_type.charAt(0)=='6'||field_type.charAt(0)=='7'){
																// obj_field_add.each(function(i){
																	// this.style.backgroundColor = e.color.toString('rgba');
																// });
															// }else{
																// obj_field_add[0].style.backgroundColor = e.color.toString('rgba');
															// }
															
															// var field_bgcolor = $("#field_add_bgcolor").val();
															// if(field_bgcolor=='') obj_field_add.removeAttr("style");
														
														});
													});
													</script>
													<div class="form-group">
														<label>帮助文本</label>
														<input v-modal="field_add_helpblock" type="text" class="form-control input-sm" placeholder="帮助文本或提示信息">
													</div>
													<div class="checkbox">
														<label><input v-modal="field_add_readonly" type="checkbox" value=""><b>只读</b></label>
													</div>
													
												</div>

												<div class="col-lg-6">
												<!--field others-->
													<!--1-text-->
													<div v-show="show_text">
													<div class="form-group">
														<label>默认值</label>
														<input id="field_add_value_text" type="text" class="form-control input-sm">
													</div>
													<div class="form-group">
														<label>占位符</label>
														<input id="field_add_placeholder" type="text" class="form-control input-sm" placeholder="请输入文字">
													</div>
													<div class="form-group">
														<label>正则表达式</label>
														<input id="field_add_regexp" type="text" class="form-control input-sm">
													</div>
													</div>
													
													<!--2-True/False-->
													<div v-show="show_trueorfalse">
													<div class="form-group">
														<label>默认值</label>
														<div class="checkbox">
														<label><input id="field_add_value_true_or_false" type="checkbox">是否选中？</label>
														</div>
													</div>
													</div>

													<!--3-Number-->
													<div v-show="show_number">
													<div class="form-group">
														<label>默认值</label>
														<input id="field_add_value_number" type="text" class="form-control input-sm">
													</div>
													<div class="form-group">
														<label>占位符</label>
														<input id="field_add_placeholder" type="text" class="form-control input-sm" placeholder="请输入数字">
													</div>
													<div class="form-group">
														<label>正则表达式</label>
														<input id="field_add_regexp" type="text" class="form-control input-sm" value="^[1-9]\d*$">
													</div>
													</div>

													<!--4-Date-->
													<div v-show="show_date">
													<div class="form-group">
														<label>默认值</label>
														<input id="field_edit_value_date" type="text" class="form-control input-sm" value="">
													</div>
													<div class="form-group">
														<label>占位符</label>
														<input id="field_edit_placeholder" type="text" class="form-control input-sm" placeholder="例：请输入日期">
													</div>
													<div class="form-group">
														<label>正则表达式</label>
														<input id="field_edit_regexp" type="text" class="form-control input-sm" value="^\\d{4}(\\-|\\/|\\.)\\d{1,2}\\1\\d{1,2}$">
													</div>
													</div>
													
													<!--5-Textfield-->
													<div v-show="show_textfield">
													<div class="form-group">
														<label>默认值</label>
														<input id="field_edit_value_date" type="text" class="form-control input-sm" value="">
													</div>
													<div class="form-group">
														<label>占位符</label>
														<input id="field_edit_placeholder" type="text" class="form-control input-sm" placeholder="例：请输入日期">
													</div>
													<div class="form-group">
														<label>正则表达式</label>
														<input id="field_edit_regexp" type="text" class="form-control input-sm" value="^\\d{4}(\\-|\\/|\\.)\\d{1,2}\\1\\d{1,2}$">
													</div>
													</div>
													
													<!--6-Radiogroup-->
													<div v-show="show_radiogroup">
													<div id="radio_plus_or_minus" class="form-group">
														<label>(only input fields with valid values will be saved)</label>
														<br><button id="radio_plus" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus fa-fw"></i></button>&nbsp;
														<button id="radio_minus" type="button" class="btn btn-success btn-xs"><i class="fa fa-minus fa-fw"></i></button>&nbsp;
														<button id="radio_reset" type="button" class="btn btn-success btn-xs"><i class="fa fa-undo fa-fw"></i></button>
														<div id="radio_div_1" class="radio">
															<input id="radio_radio_1" name="name_radiogroup" type="radio">
															<input id="radio_input_1" type="text" class="form-control input-sm">
														</div>
														<div id="radio_div_2" class="radio">
															<input id="radio_radio_2" name="name_radiogroup" type="radio">
															<input id="radio_input_2" type="text" class="form-control input-sm">
														</div>
														<div id="radio_div_3" class="radio">
															<input id="radio_radio_3" name="name_radiogroup" type="radio">
															<input id="radio_input_3" type="text" class="form-control input-sm">
														</div>
													</div>
													</div>
													
													<!--7-Checkboxgroup-->
													<div v-show="show_checkboxgroup">
													<div id="checkbox_plus_or_minus" class="form-group">
														<label>(only input fields with valid values will be saved)</label>
														<br><button id="checkbox_plus" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus fa-fw"></i></button>&nbsp;
														<button id="checkbox_minus" type="button" class="btn btn-success btn-xs"><i class="fa fa-minus fa-fw"></i></button>&nbsp;
														<button id="checkbox_reset" type="button" class="btn btn-success btn-xs"><i class="fa fa-undo fa-fw"></i></button>
														<div id="checkbox_div_1" class="checkbox">
															<input id="checkbox_checkbox_1" type="checkbox">
															<input id="checkbox_input_1" type="text" class="form-control input-sm">
														</div>
														<div id="checkbox_div_2" class="checkbox">
															<input id="checkbox_checkbox_2" type="checkbox">
															<input id="checkbox_input_2" type="text" class="form-control input-sm">
														</div>
														<div id="checkbox_div_3" class="checkbox">
															<input id="checkbox_checkbox_3" value="option1" type="checkbox">
															<input id="checkbox_input_3" type="text" class="form-control input-sm">
														</div>
													</div>
													</div>
													
													<!--8-Combobox-->
													<div v-show="show_combobox">
													<div id="combobox_plus_or_minus" class="form-group">
														<label>(only input fields with valid values will be saved)</label>
														<br><button id="combobox_plus" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus fa-fw"></i></button>&nbsp;
														<button id="combobox_minus" type="button" class="btn btn-success btn-xs"><i class="fa fa-minus fa-fw"></i></button>&nbsp;
														<button id="combobox_reset" type="button" class="btn btn-success btn-xs"><i class="fa fa-undo fa-fw"></i></button>
														<div id="combobox_div_1" class="radio">
															<input id="combobox_radio_1" name="name_combobox" type="radio">
															<input id="combobox_input_1" type="text" class="form-control input-sm">
														</div>
														<div id="combobox_div_2" class="radio">
															<input id="combobox_radio_2" name="name_combobox" type="radio">
															<input id="combobox_input_2" type="text" class="form-control input-sm">
														</div>
														<div id="combobox_div_3" class="radio">
															<input id="combobox_radio_3" name="name_combobox" type="radio">
															<input id="combobox_input_3" type="text" class="form-control input-sm">
														</div>
													</div>
													</div>
													
													<!--9-File-->
													<div v-show="show_file">
													</div>

												
												
												
												</div>
											</div>
										</div>
										
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<btn type="primary" @click="" size="sm">Create</btn>&nbsp;
											<btn type="default" @click="" size="sm">Reset</btn>
										</div>
										<div class="panel panel-default">
											<div class="panel-heading"><label>示例/结果</label></div>
											<div class="panel-body">

											<!--field example-->
												<!--1-text-->
												<div v-show="show_text">
													<label>@{{field_add_name}}</label>
													<input type="text" class="form-control input-sm" :style="{background: field_add_bgcolor}" value="aaaaaaaaaa">
												@{{field_add_bgcolor}}
												</div>
												
												<!--2-True/False-->
												<div v-show="show_trueorfalse">
													<div class="checkbox">
														<label id="field_add_example_true_or_false">
															<input type="checkbox" :style="{background: field_add_bgcolor}">@{{field_add_name}}
														</label>
													</div>
												</div>

												<!--3-Number-->
												<div v-show="show_number">
													<label>@{{field_add_name}}</label>
													<input id="field_add_example_number" type="text" class="form-control input-sm" :style="{background: field_add_bgcolor}">
												</div>

												<!--4-Date-->
												<div v-show="show_date">
													<label>@{{field_add_name}}</label>
													<input id="field_edit_example_date" type="text" class="form-control input-sm" :style="{background: field_add_bgcolor}">
												</div>
												
												<!--5-Textfield-->
												<div v-show="show_textfield">
													<label>@{{field_add_name}}</label>
													<textarea id="field_edit_example_textfield" class="form-control" rows="3" style="resize:none;" :style="{background: field_add_bgcolor}"></textarea>
												</div>
												
												<!--6-Radiogroup-->
												<div v-show="show_radiogroup">
													<label>@{{field_add_name}}</label>
													<div class="form-group">
														<div class="radio">
														<label id="field_edit_example_radiogroup1">
														<input name="name_radiogroup_example" type="radio">radio1
														</label>
														</div>
														<div class="radio">
														<label id="field_edit_example_radiogroup2">
														<input name="name_radiogroup_example" type="radio">radio2
														</label>
														</div>
														<div class="radio">
														<label id="field_edit_example_radiogroup3">
														<input name="name_radiogroup_example" type="radio">radio3
														</label>
														</div>
													</div>
												</div>
												
												<!--7-Checkboxgroup-->
												<div v-show="show_checkboxgroup">
													<label>@{{field_add_name}}</label>
													<div class="form-group">
														<div class="checkbox">
														<label id="field_edit_example_checkboxgroup1">
														<input type="checkbox">checkbox1
														</label>
														</div>
														<div class="checkbox">
														<label id="field_edit_example_checkboxgroup2">
														<input type="checkbox">checkbox2
														</label>
														</div>
														<div class="checkbox">
														<label id="field_edit_example_checkboxgroup3">
														<input type="checkbox">checkbox3
														</label>
														</div>
													</div>
												</div>
												
												<!--8-Combobox-->
												<div v-show="show_combobox">
													<label>@{{field_add_name}}</label>
													<select id="field_edit_example_combobox" class="form-control input-sm" :style="{background: field_add_bgcolor}">
														<option value=""></option>
														<option value="">combobox1</option>
														<option value="">combobox2</option>
														<option value="">combobox3</option>
													</select>
												</div>
												
												<!--9-File-->
												<div v-show="show_file">
													<label>@{{field_add_name}}</label>
													<input id="field_edit_example_file" type="file" :style="{background: field_add_bgcolor}">
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
@endsection

@section('my_footer')
@parent
<script>
var vm_field = new Vue({
    el: '#field_list',
    data: {
		gets: {},
		perpage: {{ $config['PERPAGE_RECORDS_FOR_ROLE'] }},
		// 创建名称
		field_add_name: '',
		// 创建类型
		field_selected_add_type: [],
        field_options_add_type: [
			{value: 1, label:'1-Text'},
			{value: 2, label:'2-True/False'},
			{value: 3, label:'3-Number'},
			{value: 4, label:'4-Date'},
			{value: 5, label:'5-Textfield'},
			{value: 6, label:'6-Radiogroup'},
			{value: 7, label:'7-Checkboxgroup'},
			{value: 8, label:'8-Combobox'},
			{value: 9, label:'9-File'}
		],
		// 创建背景色
		field_add_bgcolor: '#ffffff',
		// 创建帮助文本
		field_add_helpblock: '',
		// 创建只读
		field_add_readonly: false,
		// field其他项目
		// field_add_others: '',
		// field动态示例
		// field_add_example: '',
		show_text: false,
		show_trueorfalse: false,
		show_number: false,
		show_date: false,
		show_textfield: false,
		show_radiogroup: false,
		show_checkboxgroup: false,
		show_combobox: false,
		show_file: false,
		// select样例
		selected: [],
        options: [
			{value: 1, label:'Option1'},
			{value: 2, label:'Option2'},
			{value: 3, label:'Option3333333333'},
			{value: 4, label:'Option4'},
			{value: 5, label:'Option5'}
        ]
    },
	methods: {
		// 把laravel返回的结果转换成select能接受的格式
		json2selectvalue: function (json) {
			var arr = [];
			for (var key in json) {
				// alert(key);
				// alert(json[key]);
				// arr.push({ obj.['value'] = key, obj.['label'] = json[key] });
				arr.push({ value: key, label: json[key] });
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
		// 1.选择不同类型
		field_add_type_change: function (value) {
			var _this = this;
			// alert(value[0]);
			_this.show_text=_this.show_trueorfalse=_this.show_number=_this.show_date=_this.show_textfield=_this.show_radiogroup=_this.show_checkboxgroup=_this.show_combobox=_this.show_file=false;

			switch(value[0])
			{
				case 1: //text
				
					_this.show_text = true;
					// var text0 = _this.field_add_name;
					// if(text0.length == 0) { text0='未命名'; }
					// _this.field_add_example =
					// '<label>' + @{{field_add_name}} + '</label>' +
					// '<label><span v-modal="field_add_name"></span></label>' +
					// '<input id="field_add_example_text" type="text" class="form-control input-sm">';

					// _this.field_add_others =
					// '<div class="form-group">' +
						// '<label>默认值</label>' +
						// '<input id="field_add_value_text" type="text" class="form-control input-sm">' +
					// '</div>' +
					// '<div class="form-group">' +
						// '<label>占位符</label>' +
						// '<input id="field_add_placeholder" type="text" class="form-control input-sm" placeholder="请输入文字">' +
					// '</div>' +
					// '<div class="form-group">' +
						// '<label>正则表达式</label>' +
						// '<input id="field_add_regexp" type="text" class="form-control input-sm">' +
					// '</div>';
					break;

				case 2: //True/False
				
					_this.show_trueorfalse = true;
					// var true_or_false = $('#field_add_name').val();
					// select_example+='<div class="checkbox">';
					// select_example+='<label id="field_add_example_true_or_false">';
					// if(true_or_false==''){
						// select_example+='<input type="checkbox">未命名';
					// }else{
						// select_example+='<input type="checkbox">'+true_or_false;
					// }
					// select_example+='</label>';
					// select_example+='</div>';

					// _this.field_add_others =
					// '<div class="form-group">' +
						// '<label>默认值</label>' +
						// '<div class="checkbox">' +
						// '<label><input id="field_add_value_true_or_false" type="checkbox">是否选中？</label>' +
						// '</div>';
					// '</div>';

					break;

				case 3: //Number
				
					_this.show_number = true;
					// var number0 = $('#field_add_name').val();
					// if(number0==''){number0='未命名'};
					// select_example=
					// '<label>' + number0 + '</label>' +
					// '<input id="field_add_example_number" type="text" class="form-control input-sm">';
				
					// _this.field_add_others =
					// '<div class="form-group">' +
						// '<label>默认值</label>' +
						// '<input id="field_add_value_number" type="text" class="form-control input-sm">' +
					// '</div>' +
					// '<div class="form-group">' +
						// '<label>占位符</label>' +
						// '<input id="field_add_placeholder" type="text" class="form-control input-sm" placeholder="请输入数字">' +
					// '</div>' +
					// '<div class="form-group">' +
						// '<label>正则表达式</label>' +
						// '<input id="field_add_regexp" type="text" class="form-control input-sm" value="^[1-9]\d*$">' +
					// '</div>';
				
					break;

				case 4: //Date
					_this.show_date = true;

					break;

				case 5: //Textfield
					_this.show_textfield = true;
				
					// var textfield0 = $('#field_add_name').val();
					// if(textfield0==''){textfield0='未命名'};
					// select_example=
					// '<label>' + textfield0 + '</label>' +
					// '<textarea id="field_add_example_textfield" class="form-control" rows="3" style="resize:none;"></textarea>';
				
					// _this.field_add_others =
					// '<div class="form-group">' +
						// '<label>默认值</label>' +
						// '<textarea id="field_add_value_textfield" class="form-control" rows="3" style="resize:none;"></textarea>' +
					// '</div>'+
					// '<div class="form-group">' +
						// '<label>占位符</label>' +
						// '<input id="field_add_placeholder" type="text" class="form-control input-sm" placeholder="例：请输入XXX">' +
					// '</div>';
				
					break;

				case 6: //Radiogroup
					_this.show_radiogroup = true;
				
					// var radiogroup0 = $('#field_add_name').val();
					// if(radiogroup0==''){radiogroup0='未命名'};
					// select_example=
					// '<label>' + radiogroup0 + '</label>' +
					// '<div class="form-group">' +
						// '<div class="radio">' +
						// '<label id="field_add_example_radiogroup1">' +
						// '<input name="name_radiogroup_example" type="radio">radio1' +
						// '</label>' +
						// '</div>' +
						// '<div class="radio">' +
						// '<label id="field_add_example_radiogroup2">' +
						// '<input name="name_radiogroup_example" type="radio">radio2' +
						// '</label>' +
						// '</div>' +
						// '<div class="radio">' +
						// '<label id="field_add_example_radiogroup3">' +
						// '<input name="name_radiogroup_example" type="radio">radio3' +
						// '</label>' +
						// '</div>' +
					// '</div>';
				
					// _this.field_add_others =
					// '<div id="radio_plus_or_minus" class="form-group">' +
						// '<label>(only input fields with valid values will be saved)</label>' +
						// '<br><button id="radio_plus" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus fa-fw"></i></button>&nbsp;' +
						// '<button id="radio_minus" type="button" class="btn btn-success btn-xs"><i class="fa fa-minus fa-fw"></i></button>&nbsp;' +
						// '<button id="radio_reset" type="button" class="btn btn-success btn-xs"><i class="fa fa-undo fa-fw"></i></button>' +
						// '<div id="radio_div_1" class="radio">' +
							// '<input id="radio_radio_1" name="name_radiogroup" type="radio">' +
							// '<input id="radio_input_1" type="text" class="form-control input-sm">' +
						// '</div>' +
						// '<div id="radio_div_2" class="radio">' +
							// '<input id="radio_radio_2" name="name_radiogroup" type="radio">' +
							// '<input id="radio_input_2" type="text" class="form-control input-sm">' +
						// '</div>' +
						// '<div id="radio_div_3" class="radio">' +
							// '<input id="radio_radio_3" name="name_radiogroup" type="radio">' +
							// '<input id="radio_input_3" type="text" class="form-control input-sm">' +
						// '</div>' +
					// '</div>';

					break;

				case 7: //Checkboxgroup
					_this.show_checkboxgroup = true;
				
					// var checkboxgroup0 = $('#field_add_name').val();
					// if(checkboxgroup0==''){checkboxgroup0='未命名'};
					// select_example=
					// '<label>' + checkboxgroup0 + '</label>' +
					// '<div class="form-group">' +
						// '<div class="checkbox">' +
						// '<label id="field_add_example_checkboxgroup1">' +
						// '<input type="checkbox">checkbox1' +
						// '</label>' +
						// '</div>' +
						// '<div class="checkbox">' +
						// '<label id="field_add_example_checkboxgroup2">' +
						// '<input type="checkbox">checkbox2' +
						// '</label>' +
						// '</div>' +
						// '<div class="checkbox">' +
						// '<label id="field_add_example_checkboxgroup3">' +
						// '<input type="checkbox">checkbox3' +
						// '</label>' +
						// '</div>' +
					// '</div>';					
				
					// _this.field_add_others =
					// '<div id="checkbox_plus_or_minus" class="form-group">' +
						// '<label>(only input fields with valid values will be saved)</label>' +
						// '<br><button id="checkbox_plus" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus fa-fw"></i></button>&nbsp;' +
						// '<button id="checkbox_minus" type="button" class="btn btn-success btn-xs"><i class="fa fa-minus fa-fw"></i></button>&nbsp;' +
						// '<button id="checkbox_reset" type="button" class="btn btn-success btn-xs"><i class="fa fa-undo fa-fw"></i></button>' +
						// '<div id="checkbox_div_1" class="checkbox">' +
							// '<input id="checkbox_checkbox_1" type="checkbox">' +
							// '<input id="checkbox_input_1" type="text" class="form-control input-sm">' +
						// '</div>' +
						// '<div id="checkbox_div_2" class="checkbox">' +
							// '<input id="checkbox_checkbox_2" type="checkbox">' +
							// '<input id="checkbox_input_2" type="text" class="form-control input-sm">' +
						// '</div>' +
						// '<div id="checkbox_div_3" class="checkbox">' +
							// '<input id="checkbox_checkbox_3" value="option1" type="checkbox">' +
							// '<input id="checkbox_input_3" type="text" class="form-control input-sm">' +
						// '</div>' +
					// '</div>';
				
					break;

				case 8: //Combobox
					_this.show_combobox = true;
				
					// var combobox0 = $('#field_add_name').val();
					// if(combobox0==''){combobox0='未命名'};
					// select_example=
					// '<label>' + combobox0 + '</label>' +
					// '<select id="field_add_example_combobox" class="form-control input-sm">' +
						// '<option value=""></option>' +
						// '<option value="">combobox1</option>' +
						// '<option value="">combobox2</option>' +
						// '<option value="">combobox3</option>' +
					// '</select>';
					
					// _this.field_add_others =
					// '<div id="combobox_plus_or_minus" class="form-group">' +
						// '<label>(only input fields with valid values will be saved)</label>' +
						// '<br><button id="combobox_plus" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus fa-fw"></i></button>&nbsp;' +
						// '<button id="combobox_minus" type="button" class="btn btn-success btn-xs"><i class="fa fa-minus fa-fw"></i></button>&nbsp;' +
						// '<button id="combobox_reset" type="button" class="btn btn-success btn-xs"><i class="fa fa-undo fa-fw"></i></button>' +
						// '<div id="combobox_div_1" class="radio">' +
							// '<input id="combobox_radio_1" name="name_combobox" type="radio">' +
							// '<input id="combobox_input_1" type="text" class="form-control input-sm">' +
						// '</div>' +
						// '<div id="combobox_div_2" class="radio">' +
							// '<input id="combobox_radio_2" name="name_combobox" type="radio">' +
							// '<input id="combobox_input_2" type="text" class="form-control input-sm">' +
						// '</div>' +
						// '<div id="combobox_div_3" class="radio">' +
							// '<input id="combobox_radio_3" name="name_combobox" type="radio">' +
							// '<input id="combobox_input_3" type="text" class="form-control input-sm">' +
						// '</div>' +
					// '</div>';
				
					break;

				case 9: //File
					_this.show_file = true;
				
					// var file0 = $('#field_add_name').val();
					// if(file0==''){file0='上传文件'};

					// _this.field_add_others =
					// '<label>' + file0 + '</label>' +
					// '<input id="field_add_example_file" type="file">';
				
					break;
				
				default:
				
					// _this.show_text=_this.show_trueorfalse=_this.show_number=_this.show_date=_this.show_textfield=_this.show_radiogroup=_this.show_checkboxgroup=_this.show_combobox=_this.show_file=false;

			}			
			
			
			
			
			
		},
		// 2.创建field
		fieldcreate: function () {
			var _this = this;
			var fieldname = _this.$refs.fieldcreateinput.value;
			var url = "{{ route('admin.field.create') }}";

			if(fieldname.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please input the field name!';
				_this.notification_message();
				return false;
			}
			
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					fieldname: fieldname
				}
			})
			.then(function (response) {
				// console.log(response);
				if (typeof(response.data) == "undefined") {
					// _this.alert_message('WARNING', 'Field [' + fieldname + '] failed to create!');
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Field [' + fieldname + '] failed to create!';
					_this.notification_message();
				} else {
					// _this.alert_message('SUCCESS', 'Field [' + fieldname + '] created successfully!');
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Field [' + fieldname + '] created successfully!';
					_this.notification_message();

					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				// console.log(error);
				// alert(error.response.data.message);
				// _this.alert_message('ERROR', error.response.data.message);
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 3.删除field
		fielddelete: function () {
			var _this = this;
			var fieldname = _this.selected_selectfieldtodelete;
			// alert(fieldname);return false;
			
			if(fieldname.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please select the field(s)!';
				_this.notification_message();
				return false;
			}
			
			var url = "{{ route('admin.field.fielddelete') }}";
			// alert(url);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				params: {
					fieldname: fieldname
				}
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Field(s) failed to delete!';
					_this.notification_message();
					
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Field(s) deleted successfully!';
					_this.notification_message();
					
					// 刷新
					_this.refreshview();
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
		// 11.每次操作后的各部分刷新
		refreshview: function () {
			var _this = this;
			_this.changeuser(_this.selected_selecteduser);
			_this.fieldlistdelete();
			_this.fieldlist();
			_this.permissionlist();
		},
		// 12.field列表
		fieldgets: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.field.fieldgets') }}";
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
		configperpageforfield: function (value) {
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_FIELD'] = value;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.perpage = value;
					_this.fieldgets(1, 1);
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

		// 显示所有角色
		_this.fieldgets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection