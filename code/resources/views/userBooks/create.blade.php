@extends('app')

@section("breadcrumb")
	@if (session("books") != "mine")
		<li><a href="{{ action("BookController@index") }}">Books</a></li>
	@else
		<li><a href="{{ action("UserBookController@index") }}">My Books</a></li>
	@endif
	<li><a href="{{ action("BookController@show", $book) }}">{{ $book->title }}</a></li>
	<li class="active">Mark Read</li>
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::open(["action" => ["UserBookController@store", $book], "method" => "POST", "class" => "form-horizontal"]) !!}
						<div class="form-group">
							{!! Form::label("", "Book", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->title }}</div>
							</div>
						</div>
						<div class="form-group{{ $errors->has("date") ? " has-error" : "" }}">
							{!! Form::label("date", "Date", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								{!! Form::text("date", date("m/d/Y"), ["class" => "form-control datepicker"]) !!}
								{!! Helper::fieldErrorDisplay("date", $errors) !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								{{ Form::submit("Save", ["class" => "btn btn-primary"]) }}
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
