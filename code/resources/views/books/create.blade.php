@extends('app')

@section("breadcrumb")
	<li><a href="{{ action("BookController@index") }}">Books</a></li>
	<li class="active">Create</li>
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::open(["action" => "BookController@store", "method" => "POST", "class" => "form-horizontal"]) !!}
						<div class="form-group{{ $errors->has("isbn") ? " has-error" : "" }}">
							{!! Form::label("isbn", "ISBN/EAN/ASIN", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								{!! Form::text("isbn", null, ["class" => "form-control"]) !!}
								{!! Helper::fieldErrorDisplay("isbn", $errors) !!}
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
