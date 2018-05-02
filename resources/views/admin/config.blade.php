@extends('admin.layouts.adminbase')

@section('my_title', "Admin(Config) - $SITE_TITLE  Ver: $SITE_VERSION")

@section('my_js')
	<script type="text/javascript">
/*		$(function(){
			// 一打开就默认读取所有配置内容
			$("#test01").val("{$Think.config.test01}");
			$("#test02").val("{$Think.config.test02}");
			
			//配置修改customized
			$("#config_update_customized").on('click',function(){
				var config_file = 'customized.php';
				var test01 = parseInt($("#test01").val());
				var test02 = $("#test02").val();
				var customized = '{"TEST01":' + test01 + ',"TEST02":"' + test02 + '"}';

				$.post("{:U('Admin/Index/config_update')}",{config_file:config_file,customized:customized}, function(jdata){
					if(jdata.ajax_status == 0){
						//alert('更新配置成功！'); //查询成功
						BootstrapDialog.show({
						type:BootstrapDialog.TYPE_SUCCESS,
						message:'更新配置成功！',
						onhidden: function(dialogRef){
							window.location.reload();
						}});
						
						
					} else {
						//alert('更新配置失败');
						BootstrapDialog.show({message:'更新配置失败！'});
					}
				});
				return false;
			});
		});
*/	</script>
@endsection

@section('my_body')
@parent
<div id="page-wrapper" style="min-height: 331px;">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">这里是配置项啊</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Basic Form Elements
				</div>
				<div id="config_list" class="panel-body">
				<form role="form">
					<div class="row">
					<span v-for="(val, index) in gets">

						<div class="col-lg-3">
							<div class="form-group">
								<label>@{{ val.cfg_name }}</label>
								<input v-model="val.cfg_value" class="form-control" placeholder="Enter text" >
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
		gets: {}
    },
	mounted: function(){
		var _this = this;
		var url = "{{ route('admin.config.list') }}";
		axios.get(url, {
				params: {
					perPage: 1,
					page: 1
				}
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