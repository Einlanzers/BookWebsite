<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Auth::routes();
Route::get("/", function () { return redirect()->action("BookController@index"); });

Route::resource("books", "BookController", ["only" => ["index", "create", "store", "show"]]);

Route::get("/book/{book}/mark-read", "UserBookController@create");
Route::post("/book/{book}/mark-read", "UserBookController@store");
Route::get("/book/{book}/readings", "UserBookController@show");
Route::post("/book/{book}/mark-read-now", "UserBookController@markReadNow");
