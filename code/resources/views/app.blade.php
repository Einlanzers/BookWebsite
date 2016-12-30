<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Book Database</title>
	<link rel="stylesheet" href="/css/bootstrap.css">
	<link rel="stylesheet" href="/css/all.css">
	@yield("styles")
</head>
<body id="app-layout">
	<nav class="navbar navbar-default navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="{{ action("HomeController@index") }}">Book Database</a>
			</div>
			<div class="collapse navbar-collapse" id="app-navbar-collapse">
				<ul class="nav navbar-nav">
					@if (Auth::check())
						<li><a href="{{ action("BookController@index") }}">Books</a></li>
						<li><a href="{{ action("UserBookController@index") }}">My Books</a></li>
					@endif
				</ul>
				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li><a href="{{ url("/login") }}"><i class="fa fa-btn fa-sign-out"></i>Login</a></li>
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								{{ Auth::user()->getFullName() }} <span class="caret"></span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a href="{{ url('/logout') }}"
										onclick="event.preventDefault();
												 document.getElementById('logout-form').submit();">
										<i class="fa fa-btn fa-sign-out"></i>Logout
									</a>
									<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
										{{ csrf_field() }}
									</form>
								</li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				@include("notifications")
				<ol class="breadcrumb well well-sm">
					<li><a href="{{ action("HomeController@index") }}">Home</a></li>
					@yield("breadcrumb")
				</ol>
			</div>
		</div>
	</div>
	@yield("content")
	<footer>
		<div class="container">
			<hr />
			<p>Copyright &copy; 2016, Einlanzers</p>
		</div>
	</footer>
	<script src="{{ elixir('js/app.js') }}""></script>
	<script type="text/javascript">
		$(document).ready(function()
		{
			$(".datepicker").datepicker({autoclose: true});
		});
	</script>
	@yield("scripts")
</body>
</html>
