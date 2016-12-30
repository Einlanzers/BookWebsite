@section("notifications")

@if (Session::has("error"))
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		{{ trans(Session::get("error")) }}
	</div>
@endif

@if (Session::has("success"))
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		{{ trans(Session::get("success")) }}
	</div>
@endif

@show