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
        Schema::create('productosconduce', function (Blueprint $table) {
            $table->id();
            $table->string('referencia');
            $table->string('descripcion');
            $table->string('um');
            $table->integer('cantidad');
            $table->integer('precio');
            $table->integer('importe');
            $table->integer('conduce_id');
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
        Schema::dropIfExists('productosConduce');
    }
};
