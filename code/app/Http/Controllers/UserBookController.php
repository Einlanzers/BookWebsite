<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBook;
use App\Models\Book;
use App\Http\Requests\StoreUserBookRequest;
use Auth;
use DB;

class UserBookController extends Controller
{
	public function __construct()
	{
		$this->middleware("auth");
	}

	public function index(Request $request)
	{
		$request->session()->put("books", "mine");
		$books = Book::where("books.id", ">", 0);
		if ($request->has("search"))
		{
			$results = Book::search($request->get("search"))->get();
			$ids = array_pluck($results, "id");
			$books = Book::whereIn("books.id", $ids);
		}
		$books = $books->join("user_books", "user_books.book_id", "=", "books.id");
		if ($request->has("start_date") && strtotime($request->get("start_date")))
			$books->where("user_books.date", ">=", new \Carbon\Carbon($request->get("start_date")));
		if ($request->has("end_date") && strtotime($request->get("end_date")))
			$books->where("user_books.date", "<=", new \Carbon\Carbon($request->get("end_date")));
		$readings = clone $books;
		$books = $books->select("books.*", DB::raw("MAX(user_books.`date`) AS last_read"))
			->groupBy("books.id")
			->orderBy("last_read", "DESC")
			->paginate(15)
			->appends($request->all());
			
		$readings = $readings->count();
		return view("userBooks/index", ["books" => $books, "readings" => $readings]);
	}

	public function create($book)
	{
		return view("userBooks/create", ["book" => $book]);
	}

	public function show($book)
	{
		$userBooks = $book->userBooks()
			->where("user_id", Auth::user()->id)
			->orderBy("date", "DESC")
			->get();
		return view("userBooks/show", ["book" => $book, "userBooks" => $userBooks]);
	}

	public function store(StoreUserBookRequest $request, $book)
	{
		$books = $request->session()->get("books", "main");
		$controller = $books == "mine" ? "UserBookController" : "BookController";
		$userBook = new UserBook;
		$userBook->user_id = Auth::user()->id;
		$userBook->book_id = $book->id;
		$userBook->date = new \Carbon\Carbon($request->get("date"));
		$userBook->save();
		return redirect()
			->action("{$controller}@index")
			->with("success", "Book marked as read!");
	}
}
