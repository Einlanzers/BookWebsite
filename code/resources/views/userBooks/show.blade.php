@extends("app")

@section("breadcrumb")
	@if (session("books") != "mine")
		<li><a href="{{ action("BookController@index") }}">Books</a></li>
	@else
		<li><a href="{{ action("UserBookController@index") }}">My Books</a></li>
	@endif
	<li><a href="{{ action("BookController@show", $book) }}">{{ $book->title }}</a></li>
	<li class="active">My Readings</li>
@stop

@section("content")
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="pull-right">
						<a href="{{ action("UserBookController@create", $book) }}" class="btn btn-success btn-sm" title="Mark Read">
							<i class="fa fa-plus"></i> Mark Read
						</a>
					</div>
					<div style="clear: both;"></div>
					<div class="form-horizontal">
						<div class="form-group">
							{!! Form::label("title", "Title", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->title }}</div>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th class="col-md-12">Date Read</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($userBooks as $userBook)
									<tr>
										<td>{{ $userBook->date->format("m/d/Y") }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
