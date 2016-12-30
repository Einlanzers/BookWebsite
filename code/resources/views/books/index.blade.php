@extends("app")

@section("breadcrumb")
	<li class="active">Books</li>
@stop

@section("content")
<style>
	.thumbnail
	{
		height: 125px;
		width: 125px;
	}
	
	.thumbnailCol
	{
		width: 125px;
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="well well-sm">
						<div class="pull-right">
							<a href="{{ action("BookController@create") }}" class="btn btn-success btn-sm" title="Add">
								<i class="fa fa-plus"></i> Add
							</a>
						</div>
						{!! Form::open(["action" => "BookController@index", "method" => "get", "class" => "form-inline", "autocomplete" => "off"]) !!}
							<div class="form-group">
								{!! Form::text("search", Request::get("search"), ["class" => "form-control input-sm", "placeholder" => "Search Term", "style" => "width:500px;"]) !!}
							</div>
							{{ Form::submit("Filter", ["class" => "btn btn-primary btn-sm"]) }}
						{!! Form::close() !!}
						<div style="clear: both;"></div>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th class="thumbnailCol">&nbsp;</th>
									<th class="col-md-11">Title</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($books as $book)
									<tr>
										<td>
											@if (!empty($book->image_link))
												<a href="{{ action("BookController@show", $book) }}">
													<img class="thumbnail" src="{{ $book->image_link }}" />
												</a>
											@else
												&nbsp;
											@endif
										</td>
										<td>
											<a href="{{ action("BookController@show", $book) }}">
												{{ $book->title }}
											</a><br />
											{{ $book->authors }}<br />
											{{ $book->getISBN13() }}<br />
											{{ $book->isbn_10 }}
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{{ $books->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
