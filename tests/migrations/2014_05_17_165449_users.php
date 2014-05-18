<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($t) {
			$t->increments('id');
			$t->string('first_name');
			$t->string('last_name');
			$t->timestamps();
		});
		
		DB::table('users')->insert(array(
			array(
				'first_name' => 'Englebert',
				'last_name' => 'Humperdink',
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s"),
			),
			array(
				'first_name' => 'Barry',
				'last_name' => 'Manilow',
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s"),
			)
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
