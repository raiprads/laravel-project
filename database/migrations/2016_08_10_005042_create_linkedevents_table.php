<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkedeventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linkedevents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('listing_id')->unique();
            $table->string('start_time');
            $table->string('end_time');
            $table->text('short_description');
            $table->text('description');
            $table->string('location');
            $table->text('image');
            $table->text('info_url');
            $table->text('api_link');
            
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
        Schema::drop('linkedevents');
    }
}
