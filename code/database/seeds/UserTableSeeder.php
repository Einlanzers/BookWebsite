<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\App\Models\User::truncate();

		\App\Models\User::create([
			"first_name" => "John",
			"last_name" => "Doe",
			"email" => "jdoe@example.com",
			"password" => bcrypt("password"),
		]);
	}
}
