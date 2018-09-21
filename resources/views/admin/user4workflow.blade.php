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
<div>

	<Divider orientation="left">User4workflow Management</Divider>

	<Card>
		<p slot="title">编辑 User4workflow</p>
		<p>
			<i-row :gutter="16">
				<i-col span="9">
					<i-select v-model="user_select" @on-change="change_user" clearable placeholder="select user" style="width: 280px;">
						<i-option v-for="item in user_options" :value="item.value" :key="item.value">@{{ item.label }}</i-option>
					</i-select>
					&nbsp;&nbsp;<i-button @click="">Reverse</i-button>
				</i-col>
				<i-col span="9">
					&nbsp;
				</i-col>
				<i-col span="6">
					<i-button type="primary" :disabled="boo_update" @click="userupdate()">Update</i-button>
				</i-col>
			</i-row>
			&nbsp;
		</p>
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
		<p>
			<i-row :gutter="16">
				<i-col span="10">
					选择权限
					<Checkbox-group v-model="user_right" @on-change="save_user_right">
						<Checkbox label="Administrator"></Checkbox>
						<Checkbox label="Sender"></Checkbox>
						<Checkbox label="Receiver"></Checkbox>
						<Checkbox label="ReadOnly"></Checkbox>
					</Checkbox-group>
					<br>
					Substitute Time (minute)&nbsp;&nbsp;
					<Input-number v-model.lazy="substitute_time" @on-change="save_substitute_time" :min="480" :max="4320" size="small"></Input-number>
				</i-col>
				<i-col span="14">
				&nbsp;
				</i-col>
			</i-row>
			&nbsp;
		</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
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
		
		sideractivename: '2-3-3',
		sideropennames: ['2', '2-3'],
		
		user_select: [],
        user_options: [],

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
									vm_app.substituteuser_down(params)
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
									vm_app.substituteuser_up(params)
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
									vm_app.substituteuser_remove(params)
								}
							}
						})
					]);
				}
			}
		],
		tabledata: [],
		
		substitute_time: '',
		
		user_right: [],
		
		
		
		
		// gets: {},
		// perpage: {{ $config['PERPAGE_RECORDS_FOR_SLOT'] }},
		// substituteuser_select: [],
        // substituteuser_options: [],
		// substitute_time: '',
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
		
		// 
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
		
		json2transfer4user: function (json) {
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
		
		
		// update
		userupdate: function () {
			var _this = this;
			var user_id = _this.user_select;
			var substituteuser_id = _this.targetkeystransfer;

			if (user_id == undefined || substituteuser_id == undefined || user_id == '' || substituteuser_id == '') return false;
			
			var url = "{{ route('admin.user4workflow.userupdate') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				user_id: user_id,
				substituteuser_id: substituteuser_id
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.success(false, 'Success', 'Update OK!');
					_this.change_user();
				} else {
					_this.warning(false, 'Warning', 'Update failed!');
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},		
		
		// sort向前
		substituteuser_up: function (params) {
			var _this = this;
			var substituteuserid = params.row.id;
			var index = params.index;
			
			if (substituteuserid==undefined || index==0) return false;
			var userid = _this.user_select;
			var url = "{{ route('admin.user4workflow.substituteusersort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				substituteuserid: substituteuserid,
				index: index,
				userid: userid,
				sort: 'up'
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.change_user();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},
		
		
		// sort向后
		substituteuser_down: function (params) {
			var _this = this;
			var substituteuserid = params.row.id;
			var index = params.index;
			
			if (substituteuserid==undefined || index==_this.tabledata.length-1) return false;
			var userid = _this.user_select;
			var url = "{{ route('admin.user4workflow.substituteusersort') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				substituteuserid: substituteuserid,
				index: index,
				userid: userid,
				sort: 'down'
			})
			.then(function (response) {
				if (response.data == 1) {
					_this.change_user();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
		},

		
		// 删除代理user
		substituteuser_remove: function (params) {
			var _this = this;
			var userid = _this.user_select;
			var index = params.index;
			
			if (userid == undefined || index == undefined) return false;
			
			var url = "{{ route('admin.user4workflow.user4workflowremove') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				userid: userid,
				index: index
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data == 1) {
					_this.change_user();
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
			
		},
		
		// user4workflow列表
		user4workflowgets: function(){
			var _this = this;
			var url = "{{ route('admin.user4workflow.user4workflowgets') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					limit: 1000
				}
			})
			.then(function (response) {
				// console.log(response.data);
				if (response.data.length == 0 || response.data == undefined) {
					_this.alert_exit();
				}
				
				var json = response.data;
				_this.user_options = _this.json2select(json);
				
				_this.loadingbarfinish();
				
			})
			.catch(function (error) {
				_this.loadingbarerror();
				_this.error(false, 'Error', error);
			})
		},


		// 选择user
		change_user: function () {
			var _this = this;
			var userid = _this.user_select;
			// console.log(userid);return false;
			if (userid == undefined || userid == '') {
				_this.targetkeystransfer = [];
				_this.tabledata = [];
				_this.boo_update = true;
				_this.substitute_time = '';
				_this.user_right = [];
				return false;
			}
			_this.boo_update = false;
			var url = "{{ route('admin.user4workflow.changeuser') }}";
			axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			axios.get(url,{
				params: {
					userid: userid
				}
			})
			.then(function (response) {
				// console.log(response.data);
				// return false;
				
				if (response.data) {
					var json = response.data.user_all;
					_this.datatransfer = _this.json2transfer(json);
					
					json = response.data.user_selected;
					_this.targetkeystransfer = _this.json2transfer4user(json);
					_this.tabledata = json;

					_this.substitute_time = response.data.user_substitute_time;

					var rights = response.data.rights;
					_this.user_right = [];
					if (rights >= 8) {
						_this.user_right.push('Administrator');
						rights -= 8;
					}
					if (rights >= 4) {
						_this.user_right.push('Sender');
						rights -= 4;
					}
					if (rights >= 2) {
						_this.user_right.push('Receiver');
						rights -= 2;
					}
					if (rights >= 1) {
						_this.user_right.push('ReadOnly');
					}
					
				} else {
					_this.targetkeystransfer = [];
					_this.tabledata = [];
				}

			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})
			
		},


		// 保存代理时间
		save_substitute_time: function () {
			var _this = this;
			var userid = _this.user_select;
			var substitute_time = _this.substitute_time;

			if (userid == undefined || userid == '' || isNaN(parseInt(substitute_time))) {
				_this.warning(false, 'Warning', `No user selected or substitute time is incorrect!`);
				return false;
			}
			
			var url = "{{ route('admin.user4workflow.savesubstitutetime') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				userid: userid,
				substitute_time: substitute_time
			})
			.then(function (response) {
				if (response.data == 0) {
					_this.warning(false, 'Warning', `Failed to Save!`);
				} else {
					// _this.success(false, 'Success', `Saved OK!`);
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})			
			
		},

		
		// 保存用户权限
		save_user_right: function () {
			var _this = this;
			var userid = _this.user_select;
			var user_right = _this.user_right;
			var rights = 0;
			
			if (userid == undefined || user_right == undefined || userid == '' ) {
				_this.warning(false, 'Warning', `No user selected or user right is incorrect!`);
				return false;
			}
			
			user_right.map(function (v,i) {
				// console.log(v);
				switch(v) {
					case 'Administrator':
						rights += 8;
						break;
					case 'Sender':
						rights += 4;
						break;
					case 'Receiver':
						rights += 2;
						break;
					case 'ReadOnly':
						rights += 1;
						break;
					default:
				}
			});

			var url = "{{ route('admin.user4workflow.saveuserright') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url,{
				userid: userid,
				rights: rights
			})
			.then(function (response) {
				if (response.data == 0) {
					_this.warning(false, 'Warning', `Failed to Save!`);
				} else {
					// _this.success(false, 'Success', `Saved OK!`);
				}
			})
			.catch(function (error) {
				_this.error(false, 'Error', error);
			})			
			
		},		
		
		
		
		
		
		
		
		
		
		
		
		




	},
	mounted: function(){
		var _this = this;
		_this.current_nav = '元素管理';
		_this.current_subnav = '用户关联 - User4workflow';
		// 显示所有user4workflow
		_this.user4workflowgets();
	}
});
</script>
@endsection