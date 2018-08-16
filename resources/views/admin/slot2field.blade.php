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
			<i-select v-model="slot_select" @on-change="change_slot" clearable placeholder="select slot" style="width:200px">
				<i-option v-for="item in slot_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
			</i-select>
			&nbsp;&nbsp;
			<i-button type="primary" @click="slotupdate()">Update</i-button>
		</p>
		<br>
		<p>
			<Transfer
				:titles="titlestransfer"
				:data="datatransfer"
				filterable
				:target-keys="targetKeystransfer"
				:render-format="rendertransfer"
				@on-change="onChangeTransfer">
			</Transfer>
		</p>
	</Card>
	<br>
	<Card>
		<p slot="title">Review Slot</p>
	
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
		targetKeystransfer: [], // ['1', '2'] key
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
			return arr.reverse();
		},
		
		// 穿梭框显示文本
		rendertransfer: function (item) {
			return item.label + ' (ID:' + item.key + ')';
		},
		
		onChangeTransfer: function (newTargetKeys, direction, moveKeys) {
			// console.log(newTargetKeys);
			// console.log(direction);
			// console.log(moveKeys);
			this.targetKeystransfer = newTargetKeys;
		},
		
		// slot2field列表
		slot2fieldgets: function(){
			var _this = this;
			var url = "{{ route('admin.slot2field.slot2fieldgets') }}";
			_this.loadingbarstart();
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url)
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
				// console.log(response.data);
				// console.log(_this.json2transfer4slot(response.data));return false;
				
				var json = response.data;
				_this.targetKeystransfer = _this.json2transfer4slot(json);
				return false;

				
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

		
		
		
		
		
		
		
		
		
		
		
		notification_message: function () {
			this.$notify({
				type: this.notification_type,
				title: this.notification_title,
				content: this.notification_content
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