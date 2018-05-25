@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Config) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_body')
@parent
<div id="page-wrapper" style="min-height: 331px;">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Configration Management</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					系统配置项
				</div>
				<div id="config_list" class="panel-body" v-cloak>
				<form role="form">
					<div class="row">
					<span v-for="(val, index) in gets">

						<div class="col-lg-3">
							<div class="form-group">
								<label>@{{ val.cfg_name }}</label>
								<!--<input v-model="val.cfg_value" class="form-control" placeholder="暂无配置值" >-->
								<input v-bind:id="val.cfg_name" v-bind:value="val.cfg_value" v-on:change="configchange" class="form-control" placeholder="暂无配置值" >
								<p class="help-block">&nbsp;@{{ val.cfg_description }}</p>
							</div>
						</div>
					</span>
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
					<button type="reset" class="btn btn-default">Reset</button>
				</form>
				</div>

				<div style="background-color:#c9e2b3;height:1px"></div>

			</div>
		</div>
	</div>
</div>
@endsection

@section('my_footer')
@parent
<script>
// ajax 获取数据
var vm_config = new Vue({
    el: '#config_list',
    data: {
		notification_type: '',
		notification_title: '',
		notification_content: '',
		gets: {}
    },
	methods: {
		notification_message: function () {
			this.$notify({
				type: this.notification_type,
				title: this.notification_title,
				content: this.notification_content
			})
		},
		configchange: function(event){
			var _this = this;
			var cfg_name = event.target.id;
			var cfg_value = event.target.value;
			
			var url = "{{ route('admin.config.change') }}";
			axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
			axios.post(url, {
					cfg_name: cfg_name,
					cfg_value: cfg_value
				})
				.then(function (response) {
					if (response.data) {
						// alert('success');
					} else {
						_this.notification_type = 'danger';
						_this.notification_title = 'Error';
						_this.notification_content = cfg_name + 'failed to be modified!';
						_this.notification_message();
						event.target.value = cfg_value;
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
		var url = "{{ route('admin.config.list') }}";
		axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
		axios.get(url, {
			})
			.then(function (response) {
				//console.log(response);
				_this.gets = response.data;
				// _this.gets.total = _this.gets.data.length;
				// alert(_this.gets.total);
				// alert(_this.gets);
			})
			.catch(function (error) {
				console.log(error);
			})
	}
});
</script>
@endsection