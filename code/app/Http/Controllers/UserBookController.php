<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBook;
use App\Models\Book;
use App\Http\Requests\StoreUserBookRequest;
use Auth;

class UserBookController extends Controller
{
	public function __construct()
	{
		$this->middleware("auth");
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
		$userBook = new UserBook;
		$userBook->user_id = Auth::user()->id;
		$userBook->book_id = $book->id;
		$userBook->date = new \Carbon\Carbon($request->get("date"));
		$userBook->save();
		return redirect()
			->action("BookController@index")
			->with("success", "Book marked as read!");
	}
	
	public function markReadNow($book)
	{
		$date = new \Carbon\Carbon("now", "America/Detroit");
		$userBook = new UserBook;
		$userBook->user_id = Auth::user()->id;
		$userBook->book_id = $book->id;
		$userBook->date = $date;
		$userBook->save();
		return ["success" => true, "date" => $date->format("m/d/Y")];
	}
}
