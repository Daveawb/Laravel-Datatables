<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DatatablesUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $t) {
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
			),
            array(
                'first_name' => 'Barry',
                'last_name' => 'White',
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ),
            array(
                'first_name' => 'Barry',
                'last_name' => 'Williams',
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ),
            array(
                'first_name' => 'Barry',
                'last_name' => 'Scott',
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ),
            array(
                'first_name' => 'Barry',
                'last_name' => 'Evans',
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
