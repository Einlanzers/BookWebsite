<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;

class BookController extends Controller
{
	public function __construct()
	{
		$this->middleware("auth");
	}

	public function index(Request $request)
	{
		$request->session()->put("books", "all");
		$books = Book::where("id", ">", 0);
		if ($request->has("search"))
		{
			$results = Book::search($request->get("search"))->get();
			$ids = array_pluck($results, "id");
			$books = Book::whereIn("id", $ids);
		}
		$books = $books->orderBy("title", "ASC")
			->paginate(15)
			->appends($request->all());
		return view("books/index", ["books" => $books]);
	}

	public function create()
	{
		return view("books/create");
	}

	public function show($book)
	{
		return view("books/show", ["book" => $book]);
	}

	public function store(StoreBookRequest $request)
	{
		$book = Book::getByISBN($request->get("isbn"));
		if (!$book)
			return redirect()
				->action("BookController@create")
				->with("error", "Failed to find book by ISBN.");
		return redirect()
			->action("BookController@show", $book)
			->with("success", "Book created!");
	}
}
