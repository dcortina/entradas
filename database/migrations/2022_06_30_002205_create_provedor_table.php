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
        Schema::create('provedores', function (Blueprint $table) {
            $table->id();
            $table->string('codeAux');
            $table->string('nombre');
            $table->string('direccion');
            $table->string('provincia');
            $table->string('municipio');
            $table->string('email');
            $table->string('telefono');
            $table->string('observaciones')->nullable();;
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
        Schema::dropIfExists('provedor');
    }
};
