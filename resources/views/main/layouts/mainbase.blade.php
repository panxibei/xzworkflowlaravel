<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>
@section('my_title')
{{$config['SITE_TITLE']}}  Ver: {{$config['SITE_VERSION']}}
@show
</title>
<link rel="stylesheet" href="{{ asset('statics/iview/styles/iview.css') }}">
<style type="text/css">
	/* 解决闪烁问题的CSS */
	[v-cloak] {	display: none; }
</style>
<style type="text/css">
.layout{
    border: 1px solid #d7dde4;
    background: #f5f7f9;
    position: relative;
    border-radius: 4px;
    overflow: hidden;
}
.layout-header-bar{
	background: #fff;
	box-shadow: 0 1px 1px rgba(0,0,0,.1);
}
.layout-logo{
    width: 100px;
    height: 30px;
    <!--background: #5b6270;-->
    border-radius: 3px;
    float: left;
    position: relative;
    top: 15px;
    left: 20px;
}
.layout-breadcrumb{
	<!-- padding: 10px 15px 0; -->
    width: 100px;
    height: 30px;
    <!--background: #5b6270;-->
    border-radius: 3px;
    float: left;
    position: relative;
    top: 5px;
    left: 20px;
}
.layout-nav{
	float: right;
	position: relative;
    width: 420px;
    margin: 0 auto;
    margin-right: 10px;
}
.layout-footer-center{
    text-align: center;
}
</style>
@yield('my_style')

<script src="{{ asset('js/functions.js') }}"></script>
@yield('my_js')
</head>
<body>
<div id="app" v-cloak>

    <div class="layout">
        <Layout>
			<Layout>
            <!--头部导航-->
			<div style="z-index: 999;">
            <Header :style="{position: 'fixed', width: '100%', marginLeft: '200px'}">
                <Layout>
				<i-menu mode="horizontal" theme="light" active-name="3">
                    <!--<div class="layout-logo">qqqqqqqqqqqq</div>-->
					
					<!--面包屑-->
					<div class="layout-breadcrumb">
						<Breadcrumb>
							<Breadcrumb-item href="#">首页</Breadcrumb-item>
							<Breadcrumb-item href="#">应用中心</Breadcrumb-item>
							<Breadcrumb-item>某应用</Breadcrumb-item>
						</Breadcrumb>
					</div>
					
					<!--头部导航菜单-->
                    <div class="layout-nav">
						<!--Item 1-->
                        <Menu-item name="1">
							<Badge dot>
								<Icon type="email" size="24"></Icon>
							</Badge>
                            
                        </Menu-item>
						<!--Item 2-->
                        <Menu-item name="2">
							<Dropdown @click.native="event => dropdownuser(event.target.innerText.trim())">
								<Badge dot>
									<Icon type="document-text" size="24"></Icon>
								</Badge>
								<Dropdown-menu slot="list" style="width: 260px">
									<Dropdown-item>
									<strong>Task: xxxxx1</strong>
										<i-progress :percent="55" status="active"></i-progress>
									</Dropdown-item>
									<Dropdown-item divided>
									<strong>Task: xxxxx2</strong>
										<i-progress :percent="55" status="active"></i-progress>
									</Dropdown-item>
									<Dropdown-item divided>
									<strong>Task: xxxxx3</strong>
										<i-progress :percent="55" status="active"></i-progress>
									</Dropdown-item>
								</Dropdown-menu>
							</Dropdown>
                        </Menu-item>
						<!--Item 3-->
                        <Menu-item name="3">
							<Dropdown @click.native="event => dropdownuser(event.target.innerText.trim())">
								<!--<a href="javascript:;">-->
									<Icon type="person" size="24"></Icon>
									<Icon type="arrow-down-b"></Icon>
								<!--</a>-->
								<Dropdown-menu slot="list">
									<Dropdown-item><Icon type="person"></Icon> {{ $user['name'] or 'Unknown User'}}</Dropdown-item>
									<Dropdown-item divided><Icon type="home"></Icon> Home</Dropdown-item>
									<Dropdown-item><Icon type="android-exit"></Icon> Logout</Dropdown-item>
								</Dropdown-menu>
							</Dropdown>
							
                        </Menu-item>
                    </div>
                </i-menu>
				</Layout>

				<!--上部标签组-->
				<Layout :style="{padding: '0 2px', marginLeft: '10px'}">
					<div>
						@section('my_tag')
						
						<!--
						<Tag type="dot">标签一</Tag>
						<Tag type="dot" closable>标签三</Tag>
						<Tag v-if="show" @on-close="handleClose" type="dot" closable color="blue">可关闭标签</Tag>
						-->
						@show
					</div>
				</Layout>
            </Header>
			</div>
			</Layout>

            <Layout>
                <!--左侧导航菜单-->
				<Sider hide-trigger :style="{background: '#fff', position: 'fixed', height: '100vh', left: 0, overflow: 'auto'}">
					<div style="height: 60px;">
						<div class="layout-logo">xzWorkflow 2018</div>
					</div>
					<i-menu :active-name="sideractivename" theme="light" width="auto" :open-names="sideropennames">
                        <Submenu name="1">
                            <template slot="title">
                                <Icon type="ios-navigate"></Icon>
                                网站首页
                            </template>
                            <Menu-item name="1-1">Option 1</Menu-item>
                            <Menu-item name="1-2">Option 2</Menu-item>
                            <Menu-item name="1-3">Option 3</Menu-item>
                        </Submenu>
                        <Submenu name="2">
                            <template slot="title">
                                <Icon type="ios-loop-strong"></Icon>
								Circulation
                            </template>
                            <Menu-item name="2-1"><Icon type="document-text"></Icon>Circulation</Menu-item>
                            <Menu-item name="2-2"><Icon type="edit"></Icon>ToDo</Menu-item>
                            <Menu-item name="2-3"><Icon type="archive"></Icon>Archives</Menu-item>
                        </Submenu>
                        <Submenu name="3">
                            <template slot="title">
                                <Icon type="ios-analytics"></Icon>
                                Item 3
                            </template>
                            <Menu-item name="3-1">Option 1</Menu-item>
                            <Menu-item name="3-2">Option 2</Menu-item>
                        </Submenu>
                    </i-menu>
                </Sider>
			</Layout>
			
			<div><br><br><br><br></div>
			<Layout :style="{padding: '0 12px 24px', marginLeft: '200px'}">
				<!--内容主体-->
				<Content :style="{padding: '24px 12px', minHeight: '280px', background: '#fff'}">
				<br>
				<!-- 主体 -->
				@section('my_body')
				@show
				<!-- /主体 -->

				</Content>
			</Layout>

 			<!-- 底部 -->
			<Footer class="layout-footer-center">
			@section('my_footer')
			<a href="{{route('main.circulation.index')}}">{{$config['SITE_TITLE']}}</a>&nbsp;&nbsp;{{$config['SITE_COPYRIGHT']}}
			@show
			</Footer>
			<!-- /底部 -->
			
        </Layout>
		<!-- 返回顶部 -->
		<Back-top></Back-top>
    </div>

</div>

<script src="{{ asset('js/vue.min.js') }}"></script>
<script src="{{ asset('js/axios.min.js') }}"></script>
<script src="{{ asset('js/bluebird.min.js') }}"></script>
<script src="{{ asset('statics/iview/iview.min.js') }}"></script>
@yield('my_js_others')
</body>
</html>
