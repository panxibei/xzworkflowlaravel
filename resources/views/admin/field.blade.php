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

	<Tabs type="card" v-model="currenttabs">
		<Tab-pane label="Field List">
			<i-table height="300" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
			<br><Page :current="page_current" :total="page_total" :page-size="page_size" @on-change="currentpage => oncurrentpagechange(currentpage)" @on-page-size-change="pagesize => onpagesizechange(pagesize)" :page-size-opts="[5, 10, 20, 50]" show-total show-elevator show-sizer></Page>
		</Tab-pane>

		<Tab-pane label="Create/Edit Field">
		
			<i-row>
				<i-col span="6">
					<Card>
						<p slot="title">新建/编辑元素</p>
						<p>
							<input v-model="field_add_id" type="hidden">
							* 名称<br>
							<i-input v-model="field_add_name" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							* 类型<br>
							<i-select v-model="field_selected_add_type" @on-change="value=>field_add_type_change(value)" clearable size="small" style="width:200px">
								<i-option v-for="item in field_options_add_type" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
							</i-select>
						</p>
						<br>
						<p>
							背景色<br>
							<Color-picker v-model="field_add_bgcolor" size="small"/>
						</p>
						<br>
						<p>
							帮助文本<br>
							<i-input v-model="field_add_helpblock" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							只读&nbsp;
							<i-switch v-model="field_add_readonly" size="small">
								<Icon type="android-done" slot="open"></Icon>
								<Icon type="android-close" slot="close"></Icon>
							</i-switch>
						</p>
					</Card>
				</i-col>
				<i-col span="1">
				&nbsp;
				</i-col>
				<i-col span="6">
					<Card>
						<p slot="title">扩展属性</p>
					<!--field others-->
					<!--1-text-->
					<div v-show="show_text">
						<p>
							默认值<br>
							<i-input v-model="field_add_defaultvalue" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							占位符<br>
							<i-input v-model="field_add_placeholder" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							正则表达式<br>
							<i-input v-model="field_add_regexp" size="small" clearable style="width: 200px"></i-input>
						</p>
					</div>
					
					<!--2-True/False-->
					<div v-show="show_trueorfalse">
						<p>
							默认值<br><br>
							是否选中？&nbsp;
							<i-switch v-model="field_add_ischecked" size="small">
							</i-switch>
						</p>
					</div>

					<!--3-Number-->
					<div v-show="show_number">
						<p>
							默认值<br>
							<Input-number v-model="field_add_defaultvalue" size="small"></Input-number>
						</p>
						<br>
						<p>
							占位符<br>
							<i-input v-model="field_add_placeholder" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							正则表达式<br>
							<i-input v-model="field_add_regexp" size="small" clearable style="width: 200px"></i-input>
						</p>
					</div>

					<!--4-Date-->
					<div v-show="show_date">
						<p>
							默认值</br>
							<Date-picker v-model="field_add_defaultvalue" type="date" size="small" style="width: 200px"></Date-picker>
						</p>
						<br>
						<p>
							占位符<br>
							<i-input v-model="field_add_placeholder" size="small" clearable style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							正则表达式<br>
							<i-input v-model="field_add_regexp" size="small" clearable style="width: 200px"></i-input>
						</p>
					</div>
					
					<!--5-Textfield-->
					<div v-show="show_textfield">
						<p>
							默认值<br>
							<i-input v-model="field_add_defaultvalue" type="textarea" :rows="1" size="small" style="width: 200px"></i-input>
						</p>
						<br>
						<p>
							占位符<br>
							<i-input v-model="field_add_placeholder" size="small" clearable style="width: 200px"></i-input>
						</p>
					</div>
					
					<!--6-Radiogroup-->
					<div v-show="show_radiogroup">
						<p>
							(only input fields with valid values will be saved)
						</p>
						<br>
						<p>
							<Input-number v-model="field_add_radio_quantity" @on-change="value=>radiochecked_generate(value)" min="2" size="small" style="width: 80px"></Input-number>
							&nbsp;&nbsp;<i-button @click="radiochecked_reset" size="small" icon="ios-refresh">Reset selections</i-button>
						</p>
						<br>
						<p>
							<Radio-group v-model.lazy="field_add_radio_select" vertical>
								<span v-for="(item, index) in radiochecked">
									<Radio :label="item.value"></Radio>
									* <i-input type="text" v-model="item.value" size="small" style="width: 200px">
									<br>
								</span>
							</Radio-group>
						</p>
					</div>
					
					<!--7-Checkboxgroup-->
					<div v-show="show_checkboxgroup">
						<p>
							(Input and check the following fields)
						</p>
						<br>
						<p>
							<Input-number v-model="field_add_checkbox_quantity" @on-change="value=>checkboxchecked_generate(value)" min="2" size="small" style="width: 80px"></Input-number>
							&nbsp;&nbsp;<i-button @click="checkboxchecked_reset" size="small" icon="ios-refresh">Reset selections</i-button>
						</p>
						<br>
						<p>
							<Checkbox-group v-model.lazy="field_add_checkbox_select">
								<span v-for="(item, index) in checkboxchecked">
									<Checkbox :label="item.value"></Checkbox>
									* <i-input type="text" v-model="item.value" size="small" style="width: 200px">
									<br>
								</span>
							</Checkbox-group>
						</p>					
					
					
					</div>
					
					<!--8-Combobox-->
					<div v-show="show_combobox">
						<p>(Input and check the following fields)</p>
						<br>
						<p>
							<Input-number v-model="field_add_combobox_quantity" @on-change="value=>comboboxchecked_generate(value)" min="2" size="small" style="width: 80px"></Input-number>
							&nbsp;&nbsp;<i-button @click="comboboxchecked_reset" size="small" icon="ios-refresh">Reset selections</i-button>
						</p>
						<br>
						<p>
							<Checkbox-group v-model.lazy="field_add_combobox_select" vertical>
								<span v-for="(item, index) in comboboxchecked">
									<Checkbox :label="item.value"></Checkbox>
									* <i-input type="text" v-model="item.value" size="small" style="width: 200px">
									<br>
								</span>
							</Checkbox-group>
						</p>
						<br>
						<p>
							占位符<br>
							<i-input v-model="field_add_placeholder" size="small" clearable style="width: 200px"></i-input>
						</p>
					</div>
					
					<!--9-File-->
					<div v-show="show_file">
						<p>
							* URL<br>
							<i-input v-model="field_add_url" size="small" clearable style="width: 200px"></i-input>
						</p>
					
					</div>

				
				
					</Card>
				</i-col>
				<i-col span="2">
					&nbsp;
				</i-col>
				<i-col span="6">
					<i-button type="primary" @click="fieldcreateorupdate('create')">Create</i-button>&nbsp;&nbsp;
					<i-button type="primary" @click="fieldcreateorupdate('update')">Update</i-button>&nbsp;&nbsp;
					<i-button @click="onreset()">Reset</i-button>
					<br><br>
					<Card>
						<p slot="title">示例/结果（新建/编辑）</p>

						<!--field example-->
						<!--1-text-->
						<p v-show="show_text">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<i-input v-model.lazy="field_add_defaultvalue" :readonly="field_add_readonly" :placeholder="field_add_placeholder" size="small" clearable style="width: 200px;"></i-input>
							<br><span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>
						
						<!--2-True/False-->
						<p v-show="show_trueorfalse">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span>&nbsp;
							<i-switch v-model.lazy="field_add_ischecked" :disabled="field_add_readonly" size="small">
							</i-switch>
							<br><span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>

						<!--3-Number-->
						<p v-show="show_number">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<Input-number v-model.lazy="field_add_defaultvalue" :readonly="field_add_readonly" :placeholder="field_add_placeholder" size="small" clearable style="width: 200px;"></Input-number>
							<br><span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>

						<!--4-Date-->
						<p v-show="show_date">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<Date-picker v-model.lazy="field_add_defaultvalue" type="date" :readonly="field_add_readonly" :placeholder="field_add_placeholder" size="small" style="width: 200px"></Date-picker>
							<br><span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>
						
						<!--5-Textfield-->
						<p v-show="show_textfield">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<i-input v-model.lazy="field_add_defaultvalue" :readonly="field_add_readonly" :placeholder="field_add_placeholder" type="textarea" :rows="1" size="small" clearable style="width: 200px;"></i-input>
							<br><span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>
						
						<!--6-Radiogroup-->
						<p v-show="show_radiogroup">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<Radio-group v-model.lazy="field_add_radio_select" vertical>
								<span v-for="(item, index) in radiochecked">
									<Radio :label="item.value" :disabled="field_add_readonly"></Radio>
								</span>
							</Radio-group>
							<br><span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>

						<!--7-Checkboxgroup-->
						<p v-show="show_checkboxgroup">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<Checkbox-group v-model.lazy="field_add_checkbox_select">
								<span v-for="(item, index) in checkboxchecked">
									<Checkbox :label="item.value" :disabled="field_add_readonly"></Checkbox>
								</span>
							</Checkbox-group>
							<span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>

						<!--8-Combobox-->
						<p v-show="show_combobox">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<i-select v-model="field_add_combobox_select" multiple clearable filterable size="small" style="width:200px">
								<i-option v-for="item in comboboxchecked" :value="item.value" :key="item.value">@{{ item.value }}</i-option>
							</i-select>
							<span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
						</p>

						<!--9-File-->
						<p v-show="show_file">
							<span :style="{background: field_add_bgcolor}">@{{field_add_name||'未命名'}}</span><br>
							<Upload
								multiple
								:action="field_add_url">
								<i-button icon="ios-cloud-upload-outline" :disabled="field_add_readonly">Upload files</i-button>
							</Upload>
							<span style="color: rgb(128, 132, 143);">@{{field_add_helpblock}}</span>
							
						</p>

					</Card>
				</i-col>
				<i-col span="3">
				</i-col>
			</i-row>

		</Tab-pane>

	</Tabs>

</div>

@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
@parent
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
				type: 'index',
				width: 60,
				align: 'center'
			},
			{
				title: 'id',
				key: 'id',
				sortable: true,
				width: 80
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
									vm_app.field_detail(params.row)
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
									vm_app.field_delete(params.row.id)
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
		
		// 创建ID
		field_add_id: '',
		// 创建名称
		field_add_name: '',
		// 创建类型
		field_selected_add_type: '',
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
		
		// 创建radiochecked
		field_add_radio_quantity: 2,
		field_add_radio_select: '',
		radiochecked: [
			{value: 'radio1'},
			{value: 'radio2'}
		],
		
		// 创建checkbox
		field_add_checkbox_quantity: 2,
		field_add_checkbox_select: [],
		checkboxchecked: [
			{value: 'checkbox1'},
			{value: 'checkbox2'}
		],
		
		// 创建combobox
		field_add_combobox_quantity: 2,
		field_add_combobox_select: [],
		comboboxchecked: [
			{value: 'combobox1'},
			{value: 'combobox2'}
		],

		// 创建是否选中
		field_add_ischecked: false,
		// 创建帮助文本
		field_add_helpblock: '',
		// 创建只读
		field_add_readonly: false,
		// 创建背景色
		field_add_bgcolor: '',
		// 创建默认值
		field_add_defaultvalue: '',
		// 创建占位符
		field_add_placeholder: '',
		// 创建正则
		field_add_regexp: '',
		// url
		field_add_url: '',
		
		// 显示扩展属性
		show_text: false,
		show_trueorfalse: false,
		show_number: false,
		show_date: false,
		show_textfield: false,
		show_radiogroup: false,
		show_checkboxgroup: false,
		show_combobox: false,
		show_file: false,
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
			_this.loadingbarstart();
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					perPage: _this.page_size,
					page: page
				}
			})
			.then(function (response) {
				if (response.data.length == 0 || response.data.data == undefined) {
					_this.alert_exit();
				}
				
				_this.page_current = response.data.current_page;
				_this.page_total = response.data.total;
				_this.page_last = response.data.last_page;
				_this.tabledata = response.data.data;
				
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
				_this.loadingbarerror();
			})
		},
		
		// 生成radio
		radiochecked_generate: function (counts) {
			var len = this.radiochecked.length;
			
			if (counts > len) {
				for (var i=0;i<counts-len;i++) {
					this.radiochecked.push({value: 'radio'+parseInt(len+i+1)});
				}
			} else if (counts < len) {
				if (this.field_add_radio_select != '') {
					for (var i=counts;i<len;i++) {
						if (this.field_add_radio_select == this.radiochecked[i].value) {
							this.field_add_radio_select = '';
							break;
						}
					}
				}
				
				for (var i=0;i<len-counts;i++) {
					this.radiochecked.pop();
				}
			}
		},
		
		// 取消radio选中状态
		radiochecked_reset: function () {
			this.field_add_radio_select = '';
		},
		
		// 生成checkbox
		checkboxchecked_generate: function (counts) {
			var len = this.checkboxchecked.length;
			
			if (counts > len) {
				for (var i=0;i<counts-len;i++) {
					this.checkboxchecked.push({value: 'checkbox'+parseInt(len+i+1)});
				}
			} else if (counts < len) {
				for (var i=len-1;i>counts-1;i--) {
					for (j in this.field_add_checkbox_select) {
						if (this.field_add_checkbox_select[j] == this.checkboxchecked[i].value) {
							this.field_add_checkbox_select.splice(j, 1);
						}
					}
					this.checkboxchecked.pop();
				}
			}
		},
		
		// 取消checkbox选中状态
		checkboxchecked_reset: function () {
			this.field_add_checkbox_select = [];
		},
		
		// 生成combobox
		comboboxchecked_generate: function (counts) {
			var len = this.comboboxchecked.length;
			
			if (counts > len) {
				for (var i=0;i<counts-len;i++) {
					this.comboboxchecked.push({value: 'combobox'+parseInt(len+i+1)});
				}
			} else if (counts < len) {
				if (this.field_add_combobox_select[0] != '') {
					for (var i=counts;i<len;i++) {
						var len0 = this.field_add_combobox_select.length;
						for (var j=0;j<len0;j++) {
							if (this.field_add_combobox_select[j] == this.comboboxchecked[i].value) {
								this.field_add_combobox_select.splice(j, 1);
							}
						}
					}
				}
				
				for (var i=0;i<len-counts;i++) {
					this.comboboxchecked.pop();
				}				
			}
		},
		
		// 取消combobox选中状态
		comboboxchecked_reset: function () {
			this.field_add_combobox_select = [];
		},
		
		// Reset
		onreset: function () {
			this.fieldreset();
		},
		
		// 创建或更新field
		fieldcreateorupdate: function (createorupdate) {
			var _this = this;
			var field_add_id = _this.field_add_id;
			var field_add_name = _this.field_add_name;
			
			if(field_add_name.length==0){
				_this.warning(false, 'Warning', 'Please input the field name!');
				return false;
			}
			
			var postdata = {};
			postdata['createorupdate'] = createorupdate;
			
			postdata['id'] = field_add_id;
			postdata['name'] = field_add_name;
			
			var field_selected_add_type = _this.field_selected_add_type;
			postdata['type'] = field_selected_add_type;
			
			var field_add_bgcolor = _this.field_add_bgcolor;
			postdata['bgcolor'] = field_add_bgcolor || '';
			
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
				tmpstr += v.value + '---';
			});
			var radiochecked = tmpstr.substring(0, tmpstr.length-3) + '|' + _this.field_add_radio_select;

			tmpstr = '';
			// checkboxgroup;
			_this.checkboxchecked.map(function (v,i) {
				tmpstr += v.value + '---';
			});
			var checkboxchecked = tmpstr.substring(0, tmpstr.length-3);
			tmpstr = '';
			_this.field_add_checkbox_select.map(function (v,i) {
				tmpstr += v + ',';
			});
			checkboxchecked = checkboxchecked + '|' + tmpstr.substring(0, tmpstr.length-1);

			tmpstr = '';
			// comboboxgroup
			_this.comboboxchecked.map(function (v,i) {
				tmpstr += v.value + '---';
			});
			var comboboxchecked = tmpstr.substring(0, tmpstr.length-3);
			tmpstr = '';
			_this.field_add_combobox_select.map(function (v,i) {
				tmpstr += v + ',';
			});
			comboboxchecked = comboboxchecked + '|' + tmpstr.substring(0, tmpstr.length-1);
			
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
					_this.error(false, 'Error', 'Field type error!');
					return false;
			}
			postdata['placeholder'] = postdata['placeholder'] || '';
			postdata['regexp'] = postdata['regexp'] || '';
			postdata['value'] = postdata['value'] || '';


			var url = "{{ route('admin.field.createorupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				postdata: postdata
			})
			.then(function (response) {
				if (response.data != 1) {
					_this.warning(false, 'Error', 'Field [' + field_add_name + '] failed to ' + createorupdate + ' !');
				} else {
					_this.success(false, 'Success', 'Field [' + field_add_name + '] ' + createorupdate + ' successfully!');

					if (createorupdate=='create') {_this.fieldreset()}
				}
				// 刷新
				_this.fieldgets(_this.current_page, _this.last_page);
			})
			.catch(function (error) {
				_this.error(false, 'Error', 'Error! Field [' + field_add_name + '] failed to ' + createorupdate + ' !');
			})
		},
		
		fieldreset: function () {
			var _this = this;
			_this.field_add_id = '';
			_this.field_add_name = '';
			_this.field_selected_add_type = '';
			_this.field_add_bgcolor = '';
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
		
		// 显示当前field并切换到编辑界面
		field_detail: function (row) {
			var _this = this;
			
			_this.field_add_id = row.id;
			_this.field_add_name = row.name;
			_this.field_selected_add_type = row.type;
			_this.field_add_bgcolor = row.bgcolor || '';
			
			_this.field_add_helpblock = row.helpblock;
			_this.field_add_readonly = row.readonly ? true : false;
			_this.field_add_placeholder = row.placeholder;
			_this.field_add_regexp = row.regexp;

			// 选择不同field显示不同值
			switch(row.type)
			{
				case '1-Text': //text
					_this.field_add_defaultvalue = row.value;
					break;

				case '2-True/False': //True/False
					_this.field_add_ischecked = row.value == 1 ? true : false;
					break;

				case '3-Number': //Number
					_this.field_add_defaultvalue = row.value;
					break;

				case '4-Date': //Date
					_this.field_add_defaultvalue = row.value;
					break;

				case '5-Textfield': //Textfield
					_this.field_add_defaultvalue = row.value;
					break;

				case '6-Radiogroup': //Radiogroup
					var arr_tmp = row.value.split('|');
					var arr_tmp_options = arr_tmp[0].split('---');
					var arr_tmp_selects = arr_tmp[1].split(',');
					
					_this.radiochecked = [];
					arr_tmp_options.map(function (v,i) {
						_this.radiochecked.push({value: v});
					});
					
					_this.field_add_radio_select = arr_tmp_selects[0];
					
					break;

				case '7-Checkboxgroup': //Checkboxgroup
					var arr_tmp = row.value.split('|');
					var arr_tmp_options = arr_tmp[0].split('---');
					var arr_tmp_selects = arr_tmp[1].split(',');
				
					_this.checkboxchecked = [];
					arr_tmp_options.map(function (v,i) {
						_this.checkboxchecked.push({value: v});
					});
					
					_this.field_add_checkbox_select = [];
					arr_tmp_selects.map(function (v,i) {
						_this.field_add_checkbox_select.push(v);
					});

					break;

				case '8-Combobox': //Combobox
					var arr_tmp = row.value.split('|');
					var arr_tmp_options = arr_tmp[0].split('---');
					var arr_tmp_selects = arr_tmp[1].split(',');
					
					_this.comboboxchecked = [];
					arr_tmp_options.map(function (v,i) {
						_this.comboboxchecked.push({value: v});
					});
					
					_this.field_add_combobox_select = [];
					arr_tmp_selects.map(function (v,i) {
						_this.field_add_combobox_select.push(v);
					});

					break;

				case '9-File': //File
					
					break;
				
				default:
			}

			// 切换出相应field状态			
			_this.field_add_type_change(row.type);

			// 切换到第二个面板
			_this.currenttabs = 1;
		},		
		
		
		// 删除field
		field_delete: function (id) {
			var _this = this;
			
			if (id == undefined) {
				_this.error(false, 'Error', 'Please select the field(s)!');
				return false;
			}
			
			var url = "{{ route('admin.field.fielddelete') }}";
			// alert(url);return false;
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				id: id
			})
			.then(function (response) {
				if (typeof(response.data) == "undefined") {
					_this.warning(false, 'Warning', 'Field(s) failed to delete!');
				} else {
					_this.success(false, 'Success', 'Field(s) deleted successfully!');
					
					// 刷新
					_this.fieldgets(_this.current_page, _this.last_page);
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error.response.data.message);
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
		
		alert_exit: function () {
			this.$Notice.error({
				title: '会话超时',
				desc: '会话超时，请重新登录！',
				duration: 2,
				onClose: function () {
					window.location.href = "{{ route('login') }}";
				}
			});
		},



	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '基本元素 - Field';
		// 显示所有field
		_this.fieldgets(1, 1); // page: 1, last_page: 1
	}
});
</script>
@endsection