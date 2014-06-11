<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DatatablesContentTable extends Migration {


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->text('content');
			$table->integer('user_id')->unsigned();
			$table->timestamp('published_at');
			$table->timestamps();
		});
		
		DB::table('content')->insert(array(
			array(
				"title" => "Melinda Messenger has boob job",
				"content" => "Old news...",
				"user_id" => 1,
				"published_at" => date("Y-m-d H:i:s"),
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s"),
			),
			array(
				"title" => "Teenager rescued in Somalia",
				"content" => "Pirate training camp unveiled",
				"user_id" => 1,
				"published_at" => date("Y-m-d H:i:s"),
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s"),
			),
			array(
				"title" => "Oil found under Slough, UK",
				"content" => "City to be levelled... finally",
				"user_id" => 2,
				"published_at" => date("Y-m-d H:i:s"),
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
		Schema::drop('content');
	}

}
