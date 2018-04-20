<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
		<!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    <p>@{{ message }}</p>
					<input v-model="message">
                </div>

                <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>
            </div>
			
			<div class="content1">
				<div class="title1">
					<ul>
						<li v-for="todo in todos">
						@{{ todo.text }}
						</li>
					</ul>
				</div>
			</div>
			
			<div class="content">
				<div class="title2">
					<p>@{{ message }}</p>
					<button v-on:click="reverseMessage">反转消息</button>
				</div>
			</div>			
			
        </div>
		
		<div class="content0">
			<input v-model="newTodo" v-on:keyup.enter="addTodo">
			<ul>
				<li v-for="todo in todos">
					<span>@{{ todo.text }}</span>
					<button v-on:click="removeTodo($index)">XX</button>
				</li>
			</ul>
		</div>
		
		<script type="text/javascript" src="{{asset('js/vue.min.js')}}"></script>
        <script type="text/javascript">
			new Vue({
				el: '.title',
				data: {
					message: 'Hello Laravel!'
				}
			})

			new Vue({
				el: '.title1',
				data: {
					todos: [
						{ text: 'Learn Laravel' },
						{ text: 'Learn Vue.js' },
						{ text: 'At LaravelAcademy.org' }
					]
				}
			})
			
			new Vue({
				el: '.title2',
				data: {
					message: 'Hello Laravel!'
				},
				methods: {
					reverseMessage: function () {
						this.message = this.message.split('').reverse().join('')
					}
				}
			})
			
			new Vue({
				el: '.content0',
				data: {
					newTodo: '',
					todos: [
						{ text: '新增todos' }
					]
				},
				methods: {
					addTodo: function () {
						var text = this.newTodo.trim()
						if (text) {
							this.todos.push({ text: text })
							this.newTodo = ''
						}
					},
					removeTodo: function (index) {
						this.todos.splice(index, 1)
					}
				}
			})
		</script>
    </body>
</html>
