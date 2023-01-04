<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprobarProductos', function (Blueprint $table) {
            $table->id();
            $table->string('referencia');
            $table->integer('precioPublico');
            $table->double('recargo',8,3);
            $table->boolean('conformado');
            $table->boolean('donativo');
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
        Schema::dropIfExists('comprobarProductos');
    }
};
