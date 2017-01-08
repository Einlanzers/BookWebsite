<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use DB;

class BookController extends Controller
{
	public function __construct()
	{
		$this->middleware("auth");
	}

	public function index(Request $request)
	{
		$books = Book::where("books.id", ">", 0);
		if ($request->has("search"))
		{
			$results = Book::search($request->get("search"))->get();
			$ids = array_pluck($results, "id");
			$books = Book::whereIn("books.id", $ids);
		}
		$books = $books->leftJoin("user_books", function($join) use($request)
		{
			$join->on("user_books.book_id", "=", "books.id");
			$join->on("user_books.user_id", "=", DB::raw($request->user()->id));
		});
		if ($request->has("start_date") && strtotime($request->get("start_date")))
			$books->where("user_books.date", ">=", new \Carbon\Carbon($request->get("start_date")));
		if ($request->has("end_date") && strtotime($request->get("end_date")))
			$books->where("user_books.date", "<=", new \Carbon\Carbon($request->get("end_date")));
		$books = $books->select("books.*", DB::raw("MAX(user_books.`date`) AS last_read"), DB::raw("MAX(user_books.`created_at`) AS last_created"))
			->groupBy("books.id")
			->orderBy("last_read", "DESC")
			->orderBy("last_created", "DESC")
			->orderBy("books.title", "ASC")
			->paginate(15)
			->appends($request->all());
			
		$totalBooks = $request->user()->userBooks()->select("book_id")->groupBy("book_id")->get()->count();
		$totalReadings = $request->user()->userBooks()->count();
		return view("books/index", ["books" => $books, "totalReadings" => $totalReadings, "totalBooks" => $totalBooks]);
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
				->with("error", "Failed to find book by ISBN/EAN/ASIN.")
				->withInput();
		return redirect()
			->action("BookController@show", $book)
			->with("success", "Book created!");
	}
}
