@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Role) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="role_list">
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Role Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					角色管理
				</div>
				<div class="panel-body">
					<div class="row">				
						<div class="col-lg-12">
							<div class="col-lg-3">
								<div class="form-group">
									<label>Create role</label><br>
									<input class="form-control input-sm" type="text" name="title" id="group_query_title" placeholder="角色名称" />
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-primary btn-sm">新建角色</button>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Select role(s) to delete</label><br>
									<multi-select v-model="selected" :options="options" filterable collapse-selected size="sm" placeholder="请选择要删除的角色名称..." />
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-danger btn-sm" >删除角色</button>
								</div>
							</div>
						</div>
						
						
						<div class="col-lg-12">
							<div style="background-color:#c9e2b3;height:1px"></div><br>
							
							<div class="col-lg-3">
								<div class="form-group">
									<label>Select User</label><br>
									<multi-select v-model="selected" :options="options" :limit="1" filterable collapse-selected size="sm" placeholder="请选择用户名称..."/>
								</div>
								<div class="form-group">
									<label>Select role(s) to add</label><br>
									<multi-select v-model="selected" :options="options" filterable collapse-selected size="sm" placeholder="请选择要添加的角色名称..." />
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-primary btn-sm" >添加角色到当前用户</button>
								</div>
								<div class="form-group">
									<label>Select role(s) to remove</label><br>
									<multi-select v-model="selected" :options="options" filterable collapse-selected size="sm" placeholder="请选择要移除的角色名称..." />
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-primary btn-sm" >移除角色从当前用户</button>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Current user's role(s)</label><br>
									<select id="select_slot2field_query_slot" class="form-control" size="16"></select>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Select role to view users</label><br>
									<multi-select v-model="selected" :options="options" :limit="1" filterable collapse-selected size="sm" placeholder="请选择角色名称..."/>
								</div>
								<div class="form-group">
									<label>User(s) using current role</label><br>
									<select id="select_slot2field_query_slot" class="form-control" size="11"></select>
								</div>
							</div>
							
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
var vm_role = new Vue({
    el: '#role_list',
    data: {
		gets: {},
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
		// "grouplist": function(page, last_page){
			// var _this = this;
			// var url = "{{ route('admin.group.list') }}";
			// var perPage = 1; // 有待修改，将来使用配置项
			
			// if (page > last_page) {
				// page = last_page;
			// } else if (page < 1) {
				// page = 1;
			// }
			// _this.gets.current_page = page;
			// axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
			// axios.get(url, {
					// params: {
						// perPage: perPage,
						// page: page
					// }
				// })
				// .then(function (response) {
					// console.log(response);
					// _this.gets = response.data;
					// alert(_this.gets);
				// })
				// .catch(function (error) {
					// console.log(error);
				// })
			// }
	},
	mounted: function(){
	}
});
</script>
@endsection