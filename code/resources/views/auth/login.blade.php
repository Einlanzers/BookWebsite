@extends('app')

@section("breadcrumb")
	<li class="active">Login</li>
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::open(["route" => "login", "method" => "POST", "class" => "form-horizontal"]) !!}
						<div class="form-group{{ $errors->has("email") ? " has-error" : "" }}">
							{!! Form::label("email", "Email", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								{!! Form::text("email", null, ["class" => "form-control"]) !!}
								{!! Helper::fieldErrorDisplay("email", $errors) !!}
							</div>
						</div>
						<div class="form-group{{ $errors->has("password") ? " has-error" : "" }}">
							{!! Form::label("password", "Password", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								{!! Form::password("password", ["class" => "form-control"]) !!}
								{!! Helper::fieldErrorDisplay("password", $errors) !!}
							</div>
						</div>
						 <div class="form-group{{ $errors->has("remember") ? " has-error" : "" }}">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox("remember", true, null) !!} Remember Me
                                    </label>
                                </div>
                                {!! Helper::fieldErrorDisplay("remember", $errors) !!}
                            </div>
                        </div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								{{ Form::submit("Login", ["class" => "btn btn-primary"]) }}
								<a class="btn btn-link" href="{{ url("/password/reset") }}">Forgot Your Password?</a>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
