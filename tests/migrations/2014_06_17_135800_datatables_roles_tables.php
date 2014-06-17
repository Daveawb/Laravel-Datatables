<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DatatablesRolesTables extends Migration {


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function(Blueprint $table) {
			$table->increments('id');
			$table->string('role');
            $table->integer('level');
			$table->text('description');
			$table->timestamps();
		});
        
        Schema::create('role_user', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->timestamps();
        });
		
		DB::table('roles')->insert(array(
			array(
				"role" => "admin",
				"description" => "Administrator role",
				"level" => 10,
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s"),
			),
			array(
                "role" => "user",
                "description" => "User role",
                "level" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ),
		));
        
        DB::table('role_user')->insert(array(
            array(
                "user_id" => 1,
                "role_id" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 2,
                "role_id" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 3,
                "role_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 4,
                "role_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 5,
                "role_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 6,
                "role_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 1,
                "role_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
            array(
                "user_id" => 2,
                "role_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ),
        ));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
        Schema::drop('role_user');
	}

}
