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
<div>

	<Divider orientation="left">Template2slot Management</Divider>

	<Card>
		<p slot="title">编辑 Template2slot</p>
		<p>
			<i-row :gutter="16">
				<i-col span="9">
					<i-select v-model="template_select" @on-change="change_template" clearable placeholder="select template" style="width: 280px;">
						<i-option v-for="item in template_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;<i-button @click="template_review()">Review</i-button>
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
				<i-col span="6">
					<i-button type="primary" :disabled="boo_update" @click="templateupdate()">Update</i-button>
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
		
		sideractivename: '2-2-2',
		sideropennames: ['2', '2-2'],
		
		template_select: '',
		template_options: [],
		
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
									vm_app.slot_down(params)
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
									vm_app.slot_up(params)
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
									vm_app.slot_remove(params)
								}
							}
						})
					]);
				}
			}
		],
		tabledata: [],		
		
		
		
		
		
		
		
		
		

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

		json2transfer4template: function (json) {
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
		
		// template2slot列表
		template2slotgets: function(){
			var _this = this;
			var url = "{{ route('admin.template2slot.template2slotgets') }}";
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
				
				var json = response.data.template;
				_this.template_options = _this.json2select(json);
				
				json = response.data.slot;
				_this.datatransfer = _this.json2transfer(json);
				
				_this.loadingbarfinish();
			})
			.catch(function (error) {
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},
		
		// 选择template
		change_template: function () {
			var _this = this;
			var templateid = _this.template_select;
			// console.log(templateid);return false;
			if (templateid == undefined || templateid == '') {
				_this.targetkeystransfer = [];
				_this.tabledata = [];
				_this.boo_update = true;
				// _this.gets_review_fields = {};
				return false;
			}
			_this.boo_update = false;
			var url = "{{ route('admin.template2slot.changetemplate') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					templateid: templateid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// console.log(_this.json2transfer4slot(response.data));
				// return false;
				
				if (response.data) {
					var json = response.data;
					_this.targetkeystransfer = _this.json2transfer4template(json);
					_this.tabledata = json;
					
					// _this.slot_review();
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
		templateupdate: function () {
			var _this = this;
			var templateid = _this.template_select;
			var slotid = _this.targetkeystransfer;
			
			if (templateid == undefined || slotid == undefined || templateid == '' || slotid == '') return false;
			
			var url = "{{ route('admin.template2slot.template2slotupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				templateid: templateid,
				slotid: slotid
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.success(false, 'Success', 'Update OK!');
					_this.change_template();
				} else {
					_this.warning(false, 'Warning', 'Update failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},
		
		// sort向前
		slot_up: function (params) {
			var _this = this;
			var slotid = params.row.id;
			var index = params.index;

			if (slotid==undefined || index==0) return false;
			var templateid = _this.template_select;
			var url = "{{ route('admin.template2slot.slotsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				slotid: slotid,
				index: index,
				templateid: templateid,
				sort: 'up'
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},

		// sort向后
		slot_down: function (params) {
			var _this = this;
			var slotid = params.row.id;
			var index = params.index;

			if (slotid==undefined || index==_this.tabledata.length-1) return false;
			var templateid = _this.template_select;
			var url = "{{ route('admin.template2slot.slotsort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				slotid: slotid,
				index: index,
				templateid: templateid,
				sort: 'down'
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},		
		
		
		// 删除slot
		slot_remove: function (params) {
			var _this = this;
			var templateid = _this.template_select;
			var index = params.index;
			
			if (templateid == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.template2slot.template2slotremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				templateid: templateid,
				index: index
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data == 1) {
					_this.change_template();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},
		
		// 预览template
		template_review: function () {
			var _this = this;
			var templateid = _this.template_select;
			if (templateid == undefined || templateid == '') {
				_this.warning(false, 'Warning', 'Template is not selected!');
				return false;
			}
				
			alert('功能未完成！');
		},


	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '元素关联 - Template2Slot';
		// 显示所有template2slot
		this.template2slotgets();
	}
});
</script>
@endsection