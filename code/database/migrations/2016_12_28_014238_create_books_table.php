<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("books", function (Blueprint $table)
		{
			$table->increments("id");
			$table->text("google_id")->nullable();
			$table->text("amazon_id")->nullable();
			$table->text("title");
			$table->text("authors");
			$table->text("publisher");
			$table->date("published_date")->nullable();
			$table->text("description");
			$table->text("isbn_13");
			$table->text("isbn_10");
			$table->integer("pages")->unsigned()->nullable();
			$table->text("image_link");
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("books");
	}
}
