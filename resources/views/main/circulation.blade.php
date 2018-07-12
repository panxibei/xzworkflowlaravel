@extends('main.layouts.mainbase')

@section('my_title')
Main(circulation) - 
@parent
@endsection

@section('my_js')
<script type="text/javascript">
</script>
@endsection

@section('my_tag')
@parent
<Tag type="dot" closable>标签三</Tag>
@endsection

@section('my_body')
@parent

<i-table height="360" size="small" border :columns="tablecolumns" :data="tabledata"></i-table>
<br>
<Page :current="pagecurrent" :total="pagetotal" :page-size="pagepagesize" @on-change="p => {alert(p);this.pagecurrent=p;}" @on-page-size-change="t => alert(t)" size="small" show-total show-elevator show-sizer></Page>
@endsection

@section('my_footer')
@parent

@endsection

@section('my_js_others')
<script>
var vm_app = new Vue({
    el: '#app',
    data: {
		// 左侧导航
		sideractivename: '2-1',
		sideropennames: "['2']",
		
		// 表格
		tablecolumns: [
			{
				title: 'ID',
				key: 'id',
				// sortable: true,
				width: 64
			},
			{
				title: 'Name',
				key: 'name',
				sortable: true,
				render: (h, params) => {
					return h('div', [
						h('Icon', {
							props: {
								type: 'person'
							}
						}),
						h('strong', params.row.name)
					]);
				}
			},
			{
				title: 'Current Station',
				key: 'currentstation',
				sortable: true,
				render: (h, params) => {
					return h('div', [
						h('Icon', {
							props: {
								type: 'person'
							}
						}),
						h('strong', params.row.name)
					]);
				}
			},
			{
				title: 'Sending Date',
				key: 'sendingdate',
				sortable: true
			},
			{
				title: 'Days in process',
				key: 'daysinprocess',
				sortable: true
			},
			{
				title: 'Creator',
				key: 'creator',
				sortable: true,
				width: 96
			},
			{
				title: 'Progress',
				key: 'progress',
				sortable: true,
				width: 96
			},
			
			{
				title: 'Action',
				key: 'action',
				align: 'center',
				width: 170,
				render: (h, params) => {
					return h('div', [
						h('Button', {
							props: {
								type: 'primary',
								size: 'small',
								icon: 'eye'
							},
							style: {
								marginRight: '5px'
							},
							on: {
								click: () => {
									vm_app.showperson(params.index)
								}
							}
						}, 'View'),
						h('Button', {
							props: {
								type: 'error',
								size: 'small',
								icon: 'trash-a'
							},
							on: {
								click: () => {
									vm_app.removeperson(params.index)
								}
							}
						}, 'Delete')
					]);
				}
			}
		],
		tabledata: [
			{
				id: '1',
				name: 'John Brown',
				currentstation: 'user1',
				sendingdate: '2018-07-12 01:01:01',
				daysinprocess: '36',
				creator: 'admin',
				progress: '10%'
			},
			{
				id: '2',
				name: 'John Brown2',
				currentstation: 'user2',
				sendingdate: '2018-07-11 01:01:01',
				daysinprocess: '36',
				creator: 'admin',
				progress: '10%'
			}
		],
		// 页码
		pagecurrent: 2,
		pagetotal: 199,
		pagepagesize: 20,

		
	}
})
</script>
@endsection