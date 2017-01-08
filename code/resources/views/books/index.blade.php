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
	
	.recentlyUpdated
	{
		color: green;
		font-weight: bold;
	}
	
	.loading
	{
		color: grey;
		font-weight: bold;
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
								{!! Form::text("search", Request::get("search"), ["class" => "form-control input-sm", "placeholder" => "Search Term", "style" => "width: 200px;"]) !!}
								{!! Form::text("start_date", Request::get("start_date"), ["class" => "form-control input-sm datepicker", "placeholder" => "Start", "style" => "width: 100px;"]) !!}
								{!! Form::text("end_date", Request::get("end_date"), ["class" => "form-control input-sm datepicker", "placeholder" => "End", "style" => "width: 100px;"]) !!}
							</div>
							{{ Form::submit("Filter", ["class" => "btn btn-primary btn-sm"]) }}
						{!! Form::close() !!}
						<div style="clear: both;"></div>
					</div>
					Books: {{ $totalBooks }}<br />
					Readings: {{ $totalReadings }}
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
											<span class="lastRead">Last Read: {{ $book->getLastRead() }}</span><br />
											<span class="btn btn-success btn-sm markReadNow" data-id="{{ $book->id }}" title="Mark Read Now">
												<i class="fa fa-plus"></i> Mark Read Now
											</span>
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

@section("scripts")
<script type="text/javascript">
	$(document).ready(function()
	{
		$(".markReadNow").click(function()
		{
			var label = "Last Read: ";
			var lastRead = $(this).closest("td").find(".lastRead");
			lastRead.addClass("loading");
			lastRead.html(label + "...");
			var bookId = $(this).data("id");
			$.ajax({
				url: "/book/" + bookId + "/mark-read-now",
				method: "POST",
			}).done(function(data)
			{
				lastRead.removeClass("loading");
				lastRead.html(label + "N/A");
				if (data.success)
				{
					lastRead.addClass("recentlyUpdated");
					lastRead.html(label + data.date);
					setTimeout(function() { lastRead.removeClass("recentlyUpdated"); }, 5000);
				}
			});
		});
	});
</script>
@endsection
