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
<div>

	<Divider orientation="left">Slot2field Management</Divider>

	<Card>
		<p slot="title">编辑 Slot2field</p>
		<p>
			<i-row :gutter="16">
				<i-col span="9">
					<i-select v-model="slot_select" @on-change="change_slot" clearable placeholder="select slot">
						<i-option v-for="item in slot_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
				<i-col span="6">
					<i-button type="primary" :disabled="boo_update" @click="slotupdate()">Update</i-button>
					<i-button type="primary" @click="slot_review()">Review</i-button>
				</i-col>
			</i-row>
			<br>
		</p>
		<br>
		<p>
		<i-row :gutter="16">
			<i-col span="9">
				<i-table height="320" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
			</i-col>
			<i-col span="1">
			&nbsp;
			</i-col>
			<i-col span="14">
				<Transfer
					:titles="titlestransfer"
					:data="datatransfer"
					filterable
					:target-keys="targetkeystransfer"
					:render-format="rendertransfer"
					@on-change="onChangeTransfer">
				</Transfer>
			</i-col>
		</i-row>
		&nbsp;
		</p>
	</Card>
	<br>
	<Card>
		<p slot="title">Review Slot</p>
	
		<p>
			<!--slot，有field时显示，否则显示空的slot-->
			<Collapse v-model="collapse_something" v-if="gets_review_fields.slot!=null">
				<Panel name="collapse_something">
					@{{ gets_review_fields.slot.name }}
					<div slot="content" v-if="gets_review_fields.field!=null">

						<i-row :gutter="16">
							<i-col span="6" v-for="(val, k) in gets_review_fields.field">
							
								<!--1-Text-->
								<div v-if="val.type=='1-Text'" style="height: 100px">
									<strong>@{{val.name||'未命名'}}</strong><br>
									<i-input size="small" clearable :style="{background: val.bgcolor}" style="width: 200px" :readonly="val.readonly||false" :placeholder="val.placeholder"></i-input>
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>
								<!--2-True/False-->
								<div v-else-if="val.type=='2-True/False'" style="height: 100px">
									<strong :style="{background: val.bgcolor}">@{{val.name||'未命名'}}</strong><br>
									<i-switch :disabled="val.readonly||false">
										<Icon type="android-done" slot="open"></Icon>
										<Icon type="android-close" slot="close"></Icon>
									</i-switch>
									
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>
								<!--3-Number-->
								<div v-else-if="val.type=='3-Number'" style="height: 100px">
									<strong>@{{val.name||'未命名'}}</strong><br>
									<Input-number :style="{background: val.bgcolor}" :readonly="val.readonly" :placeholder="val.placeholder" size="small" style="width: 200px"></Input-number>
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>
								<!--4-Date-->
								<div v-else-if="val.type=='4-Date'" style="height: 100px">
									<strong>@{{val.name||'未命名'}}</strong><br>
									<Date-picker type="datetime" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :placeholder="val.placeholder" style="width: 200px" size="small"></Date-picker>
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>
								<!--5-Textfield-->
								<div v-else-if="val.type=='5-Textfield'" style="height: 100px">
									<strong>@{{val.name||'未命名'}}</strong><br>
									<i-input type="textarea" :rows="1" style="width:200px;" :style="{background: val.bgcolor}" :readonly="val.readonly||false" :placeholder="val.placeholder" size="small" clearable></i-input>
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>
								<!--6-Radiogroup-->
								<div v-else-if="val.type=='6-Radiogroup'" style="height: 100px">
									<strong>@{{val.name||'未命名'}}</strong><br>
									<Radio-group>
										<Radio v-for="(item,index) in val.value.split('|')[0].split('---')" :label="item" :style="{background: val.bgcolor}" :disabled="val.readonly||false"></Radio>
									</Radio-group>
									<p style="color: #80848f">@{{val.helpblock}}</p>
									
								</div>
								<!--7-Checkboxgroup-->
								<div v-else-if="val.type=='7-Checkboxgroup'" style="height: 100px">
									<strong>@{{val.name||'未命名'}}</strong><br>
									<Checkbox-group>
										<Checkbox v-for="(item,index) in val.value.split('|')[0].split('---')" :label="item" :style="{background: val.bgcolor}" :disabled="val.readonly||false"></Checkbox>
									</Checkbox-group>
									<p style="color: #80848f">@{{val.helpblock}}</p>

								</div>
								<!--8-Combobox-->
								<!--
								<div v-else-if="val.type=='8-Combobox'" style="height: 100px">
									<strong :style="{background: val.bgcolor}">@{{val.name||'未命名'}}</strong><br>
									<i-select :placeholder="val.placeholder" :disabled="val.readonly||false" clearable multiple size="small" style="width:200px">
										<i-option v-for="item in formItem.option[key+'_'+k]" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
									</i-select>
									<p style="color: #80848f">@{{val.helpblock}}</p>
								</div>-->


								
							</i-col>

							<!--
							<div class="ivu-col ivu-col-span-6" v-for="n in 4-value.field.length%4">
								<div style="height: 100px;"></div>
							</div>
							-->
						
						</i-row>

						&nbsp;
						
						
						
						
					</div>
					<div slot="content" v-else>
					
						<div>
							These's no fields ... <a href="{{ route('admin.slot2field.index') }}" class="alert-link">Goto add field now</a>.
						</div>
					
					&nbsp;
					</div>
					
					
					
				</Panel>
			</Collapse>
					
			<!--slot，否则显示空的slot-->
	
	
	
		</p>
	</Card>

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
		
		sideractivename: '2-2-1',
		sideropennames: ['2', '2-2'],
		
		slot_select: '',
		slot_options: [],
		
		titlestransfer: ['待选', '已选'], // ['源列表', '目的列表']
		datatransfer: [],
		targetkeystransfer: [], // ['1', '2'] key
		
		boo_update: true,
		
		tablecolumns: [
			{
				type: 'index',
				width: 60,
				align: 'center'
			},
			{
				title: 'id',
				key: 'id',
				width: 60
			},
			{
				title: 'name',
				key: 'name'
			},
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 140,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-down'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.field_down(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-arrow-round-up'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.field_up(params)
								}
							}
						}),
						h('Button', {
							props: {
								type: 'default',
								size: 'small',
								icon: 'md-close'
							},
							on: {
								click: () => {
									vm_app.field_remove(params)
								}
							}
						})
					]);
				}
			}
		],
		tabledata: [],		
		
		// 预览slot
		gets_review_fields: {},
		
		//
		collapse_something: 'collapse_something',
		collapse_null: 'collapse_null',
		
		
		
		
		
		
		
		
		
		
		
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
		
		// 把laravel返回的结果转换成select能接受的格式
		json2select: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push({ value: key, label: json[key] });
			}
			return arr.reverse();
		},

		json2transfer: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push({
					key: key,
					label: json[key],
					description: json[key],
					disabled: false
				});
			}
			return arr.reverse();
		},

		json2transfer4slot: function (json) {
			var arr = [];
			for (var key in json) {
				arr.push(json[key].id.toString());
			}
			return arr;
		},
		
		// 穿梭框显示文本
		rendertransfer: function (item) {
			return item.label + ' (ID:' + item.key + ')';
		},
		
		onChangeTransfer: function (newTargetKeys, direction, moveKeys) {
			// console.log(newTargetKeys);
			// console.log(direction);
			// console.log(moveKeys);
			this.targetkeystransfer = newTargetKeys;
		},
		
		// slot2field列表
		slot2fieldgets: function(){
			var _this = this;
			var url = "{{ route('admin.slot2field.slot2fieldgets') }}";
			_this.loadingbarstart();
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					limit: 1000
				}
			})
			.then(function (response) {
				if (response.data.length == 0 || response.data == undefined) {
					_this.alert_exit();
				}
				
				var json = response.data.slot;
				_this.slot_options = _this.json2select(json);
				
				json = response.data.field;
				_this.datatransfer = _this.json2transfer(json);
				
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},
		
		// 选择slot
		change_slot: function () {
			var _this = this;
			var slotid = _this.slot_select;
			// console.log(slotid);return false;
			if (slotid == undefined || slotid == '') {
				_this.targetkeystransfer = [];
				_this.tabledata = [];
				_this.boo_update = true;
				return false;
			}
			_this.boo_update = false;
			var url = "{{ route('admin.slot2field.changeslot') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					slotid: slotid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// console.log(_this.json2transfer4slot(response.data));return false;
				
				if (response.data) {
					var json = response.data;
					_this.targetkeystransfer = _this.json2transfer4slot(json);
					_this.tabledata = json;
					
					// _this.slot_review;
				} else {
					_this.targetkeystransfer = [];
					_this.tabledata = [];
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},
		
		// update
		slotupdate: function () {
			var _this = this;
			var slotid = _this.slot_select;
			var fieldid = _this.targetkeystransfer;
			
			if (slotid == undefined || fieldid == undefined || slotid == '' || fieldid == '') return false;
			
			var url = "{{ route('admin.slot2field.slot2fieldupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				slotid: slotid,
				fieldid: fieldid
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.success(false, 'Success', 'Update OK!');
					_this.change_slot();
				} else {
					_this.warning(false, 'Warning', 'Update failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},
		
		// sort向前
		field_up: function (params) {
			var _this = this;
			var fieldid = params.row.id;
			var index = params.index;

			if (fieldid==undefined || index==0) return false;
			var slotid = _this.slot_select;
			var url = "{{ route('admin.slot2field.fieldsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				fieldid: fieldid,
				index: index,
				slotid: slotid,
				sort: 'up'
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.change_slot();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},

		// sort向后
		field_down: function (params) {
			var _this = this;
			var fieldid = params.row.id;
			var index = params.index;
			if (fieldid==undefined || index==_this.tabledata.length-1) return false;
			var slotid = _this.slot_select;
			var url = "{{ route('admin.slot2field.fieldsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				fieldid: fieldid,
				index: index,
				slotid: slotid,
				sort: 'down'
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.change_slot();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},
		
		// 删除field
		field_remove: function (params) {
			var _this = this;
			var slotid = _this.slot_select;
			var index = params.index;
			
			if (slotid == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.slot2field.slot2fieldremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				slotid: slotid,
				index: index
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data == 1) {
					_this.change_slot();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},		
		
		

		
		
		
		
		
		
		
		
		
		
		




		// 待完成
		slot_review: function () {
			var _this = this;
			var slotid = _this.slot_select;

			if (slotid == undefined) return false;
			
			var url = "{{ route('admin.slot2field.slotreview') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					slotid: slotid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;
				
				_this.gets_review_fields = response.data;
				



			})
			.catch(function (error) {
				console.log(error);
			})			
		},
	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '元素关联 - Slot2Field';
		// 显示所有slot2field
		_this.slot2fieldgets();
	}
});
</script>
@endsection