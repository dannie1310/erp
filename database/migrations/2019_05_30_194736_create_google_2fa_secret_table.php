<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogle2faSecretTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_2fa_secret', function (Blueprint $table) {
            $table->increments('id');
            $table->string('secret');
            $table->integer('id_user');
            $table->integer('id_obra');
            $table->string('base_datos')->unsigned();
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->foreign('base_datos')->references('base_datos')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_2fa_secret');
    }
}
