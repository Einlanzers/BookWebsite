@extends("app")

@section("breadcrumb")
	@if (session("books") != "mine")
		<li><a href="{{ action("BookController@index") }}">Books</a></li>
	@else
		<li><a href="{{ action("UserBookController@index") }}">My Books</a></li>
	@endif
	<li class="active">{{ $book->title }}</li>
@stop

@section("content")
<style>
	.thumbnail
	{
		height: 125px;
		width: 125px;
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="pull-right">
						<a href="{{ action("UserBookController@create", $book) }}" class="btn btn-success btn-sm" title="Mark Read">
							<i class="fa fa-plus"></i> Mark Read
						</a>
						<a href="{{ action("UserBookController@show", $book) }}" class="btn btn-primary btn-sm" title="Readings">
							Readings
						</a>
					</div>
					<div style="clear: both;"></div>
					<div class="form-horizontal">
						<div class="form-group">
							{!! Form::label("google_id", "Google Id", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->google_id }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("amazon_id", "Amazon Id", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->amazon_id }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("title", "Title", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->title }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("authors", "Authors", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->authors }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("publisher", "Publisher", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->publisher }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("published_date", "Published Date", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ !is_null($book->published_date) ? $book->published_date->format("m/d/Y") : "" }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("description", "Description", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->description }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("isbn_13", "ISBN 13", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->getISBN13() }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("isbn_10", "ISBN 10", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->isbn_10 }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("pages", "Pages", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->pages }}</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("image_link", "Image", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">
									<a target="_blank" href="{{ $book->image_link }}">
										<img class="thumbnail" src="{{ $book->image_link }}" />
									</a>
								</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label("last_read", "Last Read", ["class" => "control-label col-md-4"]) !!}
							<div class="col-md-6">
								<div class="form-control-static">{{ $book->getLastRead() }}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
