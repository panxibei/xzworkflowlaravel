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

<div>

	<Divider orientation="left">Field Management</Divider>

	<Tabs type="card">
		<Tab-pane label="Field List">
		
		<i-table height="200" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
		<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
		
		</Tab-pane>

		<Tab-pane label="Create/Edit Field">
		
		</Tab-pane>

	</Tabs>


</div>






@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
@parent
<script src="{{ asset('js/vue-color.min.js') }}"></script>
<script>
var vm_app = new Vue({
    el: '#app',
    data: {
		current_nav: '',
		current_subnav: '',
		
		sideractivename: '2-1-1',
		sideropennames: ['2', '2-1'],

		
		tablecolumns: [
			{
				title: 'id',
				key: 'id',
				sortable: true
			},
			{
				title: 'name',
				key: 'name'
			},
			{
				title: 'type',
				key: 'type',
				sortable: true
			},
			{
				title: 'created_at',
				key: 'created_at',
			},
			{
				title: 'updated_at',
				key: 'updated_at',
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'primary',
								size: 'small'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.showperson(params.index)
								}
							}
						}, 'View'),
						h('Button', {
							props: {
								type: 'error',
								size: 'small'
							},
							on: {
								click: () => {
									vm_app.removeperson(params.index)
								}
							}
						}, 'Delete')
					]);
				}
			}
		],
		tabledata: [],
		
		//分页
		page_current: 1,
		page_total: 1, // 记录总数，非总页数
		page_size: {{ $config['PERPAGE_RECORDS_FOR_FIELD'] }},
		page_last: 1,

		
		
		
		
		
		
		
		
		
		
		
		gets: {},
		perpage: {{ $config['PERPAGE_RECORDS_FOR_FIELD'] }},
		// 创建ID
		field_add_id: '',
		// 创建名称
		field_add_name: '',
		// 创建类型
		field_selected_add_type: [],
        field_options_add_type: [
			{value: '1-Text', label: '1-Text'},
			{value: '2-True/False', label: '2-True/False'},
			{value: '3-Number', label: '3-Number'},
			{value: '4-Date', label: '4-Date'},
			{value: '5-Textfield', label: '5-Textfield'},
			{value: '6-Radiogroup', label: '6-Radiogroup'},
			{value: '7-Checkboxgroup', label: '7-Checkboxgroup'},
			{value: '8-Combobox', label: '8-Combobox'},
			{value: '9-File', label: '9-File'}
		],
		// 创建背景色
		field_add_bgcolor: '',
		field_add_bgcolor_hex: '',
		// 创建帮助文本
		field_add_helpblock: '',
		// 创建只读
		field_add_readonly: false,
		// 创建默认值
		field_add_defaultvalue: '',
		// 创建占位符
		field_add_placeholder: '',
		// 创建正则
		field_add_regexp: '',
		// 创建是否选中
		field_add_ischecked: false,
		// 创建radiochecked
		radiochecked: [
			{value: '', ischecked: false},
			{value: '', ischecked: false}
		],
		// 创建checkbox
		checkboxchecked: [
			{value: '', ischecked: false},
			{value: '', ischecked: false}
		],
		// 创建combobox
		comboboxchecked_select: [],
		comboboxchecked: [
			{value: 1, label: '', ischecked: false},
			{value: 2, label: '', ischecked: false}
		],
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
        ],
		// tabs索引
		currenttabs: 0
    },

	methods: {
		menuselect: function (name) {
			navmenuselect(name);
		},
		// 1.加载进度条
		loadingbarstart () {
			this.$Loading.start();
		},
		loadingbarfinish () {
			this.$Loading.finish();
		},
		loadingbarerror () {
			this.$Loading.error();
		},
		// 2.Notice 通知提醒
		info (nodesc, title, content) {
			this.$Notice.info({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		success (nodesc, title, content) {
			this.$Notice.success({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		warning (nodesc, title, content) {
			this.$Notice.warning({
				title: title,
				desc: nodesc ? '' : content
			});
		},
		error (nodesc, title, content) {
			this.$Notice.error({
				title: title,
				desc: nodesc ? '' : content
			});
		},		
		// 切换当前页
		oncurrentpagechange: function (currentpage) {
			this.fieldgets(currentpage, this.pagelast);
		},
		// 切换页记录数
		onpagesizechange: function (pagesize) {
			
			var _this = this;
			var cfg_data = {};
			cfg_data['PERPAGE_RECORDS_FOR_FIELD'] = pagesize;
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
				cfg_data: cfg_data
			})
			.then(function (response) {
				if (response.data) {
					_this.page_size = pagesize;
					_this.fieldgets(1, _this.page_last);
				} else {
					alert('failed');
				}
			})
			.catch(function (error) {
				alert('failed');
				// console.log(error);
			})
		},
		
		// field列表
		fieldgets: function(page, last_page){
			var _this = this;
			var url = "{{ route('admin.field.fieldgets') }}";
			// var perPage = 1; // 有待修改，将来使用配置项
			
			if (page > last_page) {
				page = last_page;
			} else if (page < 1) {
				page = 1;
			}
			// _this.gets.current_page = page;
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page
				}
			})
			.then(function (response) {
				// if (typeof(response.data.data) == "undefined") {
					// alert(response);
					// _this.alert_exit();
				// }
				// _this.gets = response.data;
				
				_this.page_current = response.data.current_page;
				_this.page_total = response.data.total;
				_this.page_last = response.data.last_page;
				_this.tabledata = response.data.data;
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
		},
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		// 显示当前field并切换到编辑界面
		field_detail: function (index) {
			var _this = this;
			// console.log(index);
			// console.log(_this.gets.data[index].readonly);
			// _this.field_add_type_change(_this.gets.data[index].type);
			
			_this.field_add_id = _this.gets.data[index].id;
			_this.field_add_name = _this.gets.data[index].name;
			_this.field_selected_add_type = [_this.gets.data[index].type];
			_this.field_add_bgcolor_hex = _this.gets.data[index].bgcolor;
			
			_this.field_add_helpblock = _this.gets.data[index].helpblock;
			_this.field_add_readonly = _this.gets.data[index].readonly ? true : false;
			_this.field_add_placeholder = _this.gets.data[index].placeholder;
			_this.field_add_regexp = _this.gets.data[index].regexp;

			
			// _this.field_add_defaultvalue = _this.gets.data[index].defaultvalue;
			// 选择不同field显示不同值
			switch(_this.gets.data[index].type)
			{
				case '1-Text': //text
					_this.field_add_defaultvalue = _this.gets.data[index].value;
					break;

				case '2-True/False': //True/False
					_this.field_add_ischecked = _this.gets.data[index].value == 1 ? true : false;
					break;

				case '3-Number': //Number
					_this.field_add_defaultvalue = _this.gets.data[index].value;
					break;

				case '4-Date': //Date
					_this.field_add_defaultvalue = _this.gets.data[index].value;
					break;

				case '5-Textfield': //Textfield
					_this.field_add_defaultvalue = _this.gets.data[index].value;
					break;

				case '6-Radiogroup': //Radiogroup
					var arr_tmp = _this.gets.data[index].value.split('---');
					var arr_counts = arr_tmp.length;
					var arr_result = [];
					
					for(var i=0;i<arr_counts;i+=2)
					{
						arr_result.push({value: arr_tmp[i], ischecked: arr_tmp[i+1]==1?true:false});
					}
					_this.radiochecked = arr_result;

					break;

				case '7-Checkboxgroup': //Checkboxgroup
					arr_tmp = _this.gets.data[index].value.split('---');
					arr_counts = arr_tmp.length;
					arr_result = [];
					
					for(var i=0;i<arr_counts;i+=2)
					{
						arr_result.push({value: arr_tmp[i], ischecked: arr_tmp[i+1]==1?true:false});
					console.log(arr_tmp[i+1]);
					}
					_this.checkboxchecked = arr_result;

					break;

				case '8-Combobox': //Combobox
					arr_tmp = _this.gets.data[index].value.split('---');
					arr_counts = arr_tmp.length;
					arr_result = [];
					var selectstring = '';
					
					for(var i=0;i<arr_counts;i+=2)
					{
						// arr_result.push({value: arr_tmp[i], label: arr_tmp[i+1]==1?true:false});
						arr_result.push({value: arr_tmp[i], label: arr_tmp[i]});
						if (arr_tmp[i+1] == 1) {
							selectstring = arr_tmp[i];
						}
					}
					_this.comboboxchecked = arr_result;
					_this.comboboxchecked_select = [selectstring];
					
					break;

				case '9-File': //File
					
					break;
				
				default:
			}



			// 切换出相应field状态			
			_this.field_add_type_change(_this.gets.data[index].type);

			// 切换到第二个面板
			_this.currenttabs = 1;
		},
		// 点击radio后选中的状态
		radiochecked_change: function (index) {
			this.radiochecked.map(function (v,i) {
			if(i==index){
					v.ischecked = true
				}else{
					v.ischecked = false
				}
			});
			
		},
		// 生成radio
		radiochecked_generate: function (counts) {
			var arr = [];
			for(var i=0;i<counts;i++)
			{
				arr.push({value: '', ischecked: false});
			}
			this.radiochecked = arr;
		},
		// 取消radio选中状态
		radiochecked_reset: function () {
			// console.log(this.radiochecked[0].value);
			this.radiochecked.map(function (v,i) {
				v.ischecked = false
			});
		},
		// 点击checkbox后选中的状态
		checkboxchecked_change: function (index) {
			this.checkboxchecked[index].ischecked = ! this.checkboxchecked[index].ischecked
		},
		// 生成checkbox
		checkboxchecked_generate: function (counts) {
			var arr = [];
			for(var i=0;i<counts;i++)
			{
				arr.push({value: '', ischecked: false});
			}
			this.checkboxchecked = arr;
		},
		// 取消checkbox选中状态
		checkboxchecked_reset: function () {
			// console.log(this.radiochecked[0].value);
			this.checkboxchecked.map(function (v,i) {
				v.ischecked = false
			});
		},
		// 点击combobox后选中的状态
		comboboxchecked_change: function (index) {
			// console.log(index);return false;
			// console.log(this.comboboxchecked[index]);
			// console.log(this.comboboxchecked[index].label);
			// console.log(this.comboboxchecked[index].value);
			if (this.comboboxchecked[index] == undefined) {
				this.comboboxchecked_select = [];
				this.comboboxchecked.map(function (v,i) {
					v.ischecked = false
				});
				
			} else {
				this.comboboxchecked_select = [
					this.comboboxchecked[index].value
				];
				this.comboboxchecked.map(function (v,i) {
					if(i==index){
						v.ischecked = true
					}else{
						v.ischecked = false
					}
				});
			}

		},
		// 生成combobox
		comboboxchecked_generate: function (counts) {
			var arr = [];
			for(var i=0;i<counts;i++)
			{
				arr.push({value: i});
			}
			this.comboboxchecked = arr;
			this.comboboxchecked_select = [];
		},
		// 取消combobox选中状态
		comboboxchecked_reset: function () {
			this.comboboxchecked_select = [];
			this.comboboxchecked = [];
			// this.comboboxchecked_generate(2);
			// this.comboboxchecked = [
				// {value: 1, label: ''},
				// {value: 2, label: ''}
			// ];
		},
		fieldreset: function () {
			var _this = this;
			_this.field_add_id = '';
			_this.field_add_name = '';
			_this.field_selected_add_type = [];
			_this.field_add_bgcolor = '';
			_this.field_add_bgcolor_hex = '';
			_this.field_add_helpblock = '';
			_this.field_add_readonly = false;
			_this.field_add_defaultvalue = '';
			_this.field_add_placeholder = '';
			_this.field_add_regexp = '';
			_this.field_add_ischecked = false;
			
			_this.show_text=_this.show_trueorfalse=_this.show_number=_this.show_date=_this.show_textfield=_this.show_radiogroup=_this.show_checkboxgroup=_this.show_combobox=_this.show_file=false;
		
			_this.radiochecked_generate(2);
			_this.checkboxchecked_generate(2);
			_this.comboboxchecked_generate(2);
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
		// 选择不同类型
		field_add_type_change: function (value) {
			var _this = this;
			_this.show_text=_this.show_trueorfalse=_this.show_number=_this.show_date=_this.show_textfield=_this.show_radiogroup=_this.show_checkboxgroup=_this.show_combobox=_this.show_file=false;

			switch(value)
			{
				case '1-Text': //text
					_this.show_text = true;
					break;

				case '2-True/False': //True/False
					_this.show_trueorfalse = true;
					break;

				case '3-Number': //Number
					_this.show_number = true;
					break;

				case '4-Date': //Date
					_this.show_date = true;
					break;

				case '5-Textfield': //Textfield
					_this.show_textfield = true;
					break;

				case '6-Radiogroup': //Radiogroup
					_this.show_radiogroup = true;
					break;

				case '7-Checkboxgroup': //Checkboxgroup
					_this.show_checkboxgroup = true;
					break;

				case '8-Combobox': //Combobox
					_this.show_combobox = true;
					break;

				case '9-File': //File
					_this.show_file = true;
					break;
				
				default:
					// _this.show_text=_this.show_trueorfalse=_this.show_number=_this.show_date=_this.show_textfield=_this.show_radiogroup=_this.show_checkboxgroup=_this.show_combobox=_this.show_file=false;
			}
		},
		// 创建或更新field
		fieldcreateorupdate: function (createorupdate) {
			var _this = this;
			var field_add_id = _this.field_add_id;
			var field_add_name = _this.field_add_name;
			
			if(field_add_name.length==0){
				_this.notification_type = 'danger';
				_this.notification_title = 'Error';
				_this.notification_content = 'Please input the field name!';
				_this.notification_message();
				return false;
			}
			
			var postdata = {};
			postdata['createorupdate'] = createorupdate;
			
			postdata['id'] = field_add_id;
			postdata['name'] = field_add_name;
			
			var field_selected_add_type = _this.field_selected_add_type[0];
			postdata['type'] = field_selected_add_type;
			
			var field_add_bgcolor_hex = _this.field_add_bgcolor_hex;
			postdata['bgcolor'] = field_add_bgcolor_hex || '';
			
			var field_add_helpblock = _this.field_add_helpblock;
			postdata['helpblock'] = field_add_helpblock || '';
			
			var field_add_readonly = _this.field_add_readonly;
			postdata['readonly'] = field_add_readonly ? '1' : '0';
			
			var field_add_defaultvalue = _this.field_add_defaultvalue;
			var field_add_placeholder = _this.field_add_placeholder;
			var field_add_regexp = _this.field_add_regexp;
			var field_add_ischecked = _this.field_add_ischecked;

			var tmpstr = '';
			// radiogroup
			_this.radiochecked.map(function (v,i) {
				tmpstr += v.value + '---' + (v.ischecked?1:0) + '---';
			});
			var radiochecked = tmpstr.substring(0, tmpstr.length-3);

			tmpstr = '';
			// checkboxgroup;
			_this.checkboxchecked.map(function (v,i) {
				tmpstr += v.value + '---' + (v.ischecked?1:0) + '---';
			});
			var checkboxchecked = tmpstr.substring(0, tmpstr.length-3);

			tmpstr = '';
			// comboboxgroup
			_this.comboboxchecked.map(function (v,i) {
				tmpstr += v.label + '---' + (_this.comboboxchecked_select[0]==v.value?1:0) + '---';
			});
			var comboboxchecked = tmpstr.substring(0, tmpstr.length-3);
			
			// 分配
			switch(field_selected_add_type)
			{
				case '1-Text': //text
					postdata['value'] = field_add_defaultvalue;
					postdata['placeholder'] = field_add_placeholder;
					postdata['regexp'] = field_add_regexp;
					break;

				case '2-True/False': //True/False
					postdata['value'] = field_add_ischecked?'1':'0';
					break;

				case '3-Number': //Number
					postdata['value'] = field_add_defaultvalue;
					postdata['placeholder'] = field_add_placeholder;
					postdata['regexp'] = field_add_regexp;
					break;

				case '4-Date': //Date
					postdata['value'] = field_add_defaultvalue;
					postdata['placeholder'] = field_add_placeholder;
					postdata['regexp'] = field_add_regexp;
					break;

				case '5-Textfield': //Textfield
					postdata['defaultvalue'] = field_add_defaultvalue;
					postdata['placeholder'] = field_add_placeholder;
					break;

				case '6-Radiogroup': //Radiogroup
					postdata['value'] = radiochecked;
					break;

				case '7-Checkboxgroup': //Checkboxgroup
					postdata['value'] = checkboxchecked;
					break;

				case '8-Combobox': //Combobox
					postdata['value'] = comboboxchecked;
					postdata['placeholder'] = field_add_placeholder;
					break;

				case '9-File': //File
					break;
				
				default:
					_this.notification_type = 'danger';
					_this.notification_title = 'Error';
					_this.notification_content = 'Field type error!';
					_this.notification_message();
					return false;
			}
			postdata['placeholder'] = postdata['placeholder'] || '';
			postdata['regexp'] = postdata['regexp'] || '';
			postdata['value'] = postdata['value'] || '';


			var url = "{{ route('admin.field.createorupdate') }}";
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
					_this.notification_content = 'Field [' + field_add_name + '] failed to ' + createorupdate + ' !';
					_this.notification_message();
				} else {
					_this.notification_type = 'success';
					_this.notification_title = 'Success';
					_this.notification_content = 'Field [' + field_add_name + '] ' + createorupdate + ' successfully!';
					_this.notification_message();

					if (createorupdate=='create') {_this.fieldreset()}

				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				// _this.notification_content = error.response.data.message;
					_this.notification_content = 'Error! Field [' + field_add_name + '] failed to ' + createorupdate + ' !';
				_this.notification_message();
			})
		},
		// 删除field
		field_delete: function (id) {
			var _this = this;
			
			if (id == undefined) {
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
					id: id
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
					_this.fieldgets(_this.gets.current_page, _this.gets.last_page);
				}
			})
			.catch(function (error) {
				_this.notification_type = 'warning';
				_this.notification_title = 'Warning';
				_this.notification_content = error.response.data.message;
				_this.notification_message();
			})
		},
	},
	watch: {
        field_add_bgcolor: function(val) {
            this.field_add_bgcolor_hex = val['hex'];
        }
    },
	mounted: function(){
		var _this = this;

		// 显示所有field
		_this.fieldgets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection