@extends('admin.layouts.adminbase')

@section('my_title')
Admin(slot2user) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div>

	<Divider orientation="left">Slot2user Management</Divider>

	<Card>
		<p slot="title">编辑 Slot2user</p>
		<p>
			<i-row :gutter="16">
				<i-col span="9">
					<i-select v-model="mailinglist_select" @on-change="change_mailinglist" clearable placeholder="select mailinglist" style="width: 280px;">
						<i-option v-for="item in mailinglist_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
				<i-col span="6">
					&nbsp;
				</i-col>
			</i-row>
			<br>
		</p>
		<br>
		<p>
			<i-row :gutter="16">
				<i-col span="9">
					<i-select v-model="slot_select" @on-change="change_slot" clearable placeholder="select slot" style="width: 280px;">
						<i-option v-for="item in slot_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;<i-button @click="slot_review()">Review</i-button>
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
				<i-col span="6">
					<i-button type="primary" :disabled="boo_update" @click="slotupdate()">Update</i-button>
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
		
		sideractivename: '2-3-2',
		sideropennames: ['2', '2-3'],
		
		mailinglist_select: [],
        mailinglist_options: [],

		slot_select: [],
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
									vm_app.user_down(params)
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
									vm_app.user_up(params)
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
									vm_app.user_remove(params)
								}
							}
						})
					]);
				}
			}
		],
		tabledata: [],		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		// gets: {},
		// perpage: {{ $config['PERPAGE_RECORDS_FOR_SLOT'] }},
		
		// user_select: [],
        // user_options: [],

		// tabs索引
		// currenttabs: 0
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
		
		// 通过mailinglist选择slot
		change_mailinglist: function () {
			var _this = this;
			var mailinglist_id = _this.mailinglist_select;
			// console.log(mailinglist_id);return false;
			if (mailinglist_id == undefined || mailinglist_id == '') {
				_this.slot_select = [];
				_this.slot_options = [];
				return false;
			}
			
			var url = "{{ route('admin.slot2user.changemailinglist') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					mailinglist_id: mailinglist_id
				}
			})
			.then(function (response) {
				if (response.data != undefined) {
					var json = response.data;
					_this.slot_options = _this.json2select(json);
				}
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
		},
		
		// 通过slot选择user
		change_slot: function () {
			var _this = this;
			var slot2user_id = _this.slot_select;
			// console.log(slot2user_id);return false;
			if (slot2user_id == undefined || slot2user_id == '') {
				_this.targetkeystransfer = [];
				_this.tabledata = [];
				_this.boo_update = true;
				return false;
			}
			_this.boo_update = false;
			var url = "{{ route('admin.slot2user.changeslot') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					slot2user_id: slot2user_id
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;
				
				if (response.data) {
					var json = response.data;
					_this.targetkeystransfer = _this.json2transfer4slot(json);
					_this.tabledata = json;
					
					// _this.slot_review();
				} else {
					_this.targetkeystransfer = [];
					_this.tabledata = [];
				}
				
			})
			.catch(function (error) {
				console.log(error);
				alert(error);
			})
			
		},

		// 预览slot
		slot_review: function () {
			var _this = this;
			var slotid = _this.slot_select;
			if (slotid == undefined || slotid == '') {
				_this.warning(false, 'Warning', 'Slot is not selected!');
				return false;
			}
				
			alert('功能未完成！');
		},
		
		
		// update
		slotupdate: function () {
			var _this = this;
			var slot2user_id = _this.slot_select;
			var user_id = _this.targetkeystransfer;

			if (slot2user_id == undefined || user_id == undefined || slot2user_id == '' || user_id == '') return false;
			
			var url = "{{ route('admin.slot2user.slot2userupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				slot2user_id: slot2user_id,
				user_id: user_id
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
		user_up: function (params) {
			var _this = this;
			var userid = params.row.id;
			var index = params.index;

			if (userid==undefined || index==0) return false;
			var slot2user_id = _this.slot_select;
			var url = "{{ route('admin.slot2user.usersort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				userid: userid,
				index: index,
				slot2user_id: slot2user_id,
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
		user_down: function (params) {
			var _this = this;
			var userid = params.row.id;
			var index = params.index;

			if (userid==undefined || index==_this.tabledata.length-1) return false;
			var slot2user_id = _this.slot_select;
			var url = "{{ route('admin.slot2user.usersort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				userid: userid,
				index: index,
				slot2user_id: slot2user_id,
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
		
		
		// 删除user
		user_remove: function (params) {
			var _this = this;
			var slot2user_id = _this.slot_select;
			var index = params.index;
			
			if (slot2user_id == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.slot2user.slot2userremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				slot2user_id: slot2user_id,
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
		
		// slot2user列表
		slot2usergets: function() {
			var _this = this;
			var url = "{{ route('admin.slot2user.slot2usergets') }}";
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
				
				var json = response.data.mailinglist;
				_this.mailinglist_options = _this.json2select(json);
				
				json = response.data.user;
				_this.datatransfer = _this.json2transfer(json);
				
				_this.loadingbarfinish();
				
			})
			.catch(function (error) {
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},		
		
		
		
		
		
		

		
		
		
		
		
		
		
		
		

	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '用户关联 - Slot2User';
		// 显示所有slot2user
		_this.slot2usergets();
	}
});
</script>
@endsection