@extends("app")

@section("breadcrumb")
	<li class="active">My Books</li>
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
						{!! Form::open(["action" => "UserBookController@index", "method" => "get", "class" => "form-inline", "autocomplete" => "off"]) !!}
							<div class="form-group">
								{!! Form::text("search", Request::get("search"), ["class" => "form-control input-sm", "placeholder" => "Search Term", "style" => "width: 200px;"]) !!}
								{!! Form::text("start_date", Request::get("start_date"), ["class" => "form-control input-sm datepicker", "placeholder" => "Start", "style" => "width: 100px;"]) !!}
								{!! Form::text("end_date", Request::get("end_date"), ["class" => "form-control input-sm datepicker", "placeholder" => "End", "style" => "width: 100px;"]) !!}
							</div>
							{{ Form::submit("Filter", ["class" => "btn btn-primary btn-sm"]) }}
						{!! Form::close() !!}
						<div style="clear: both;"></div>
					</div>
					Books: {{ $books->total() }}<br />
					Readings: {{ $readings }}
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
											{{ $book->isbn_10 }}<br />
											Last Read: {{ Helper::formatDate($book->last_read, "m/d/Y") }}
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