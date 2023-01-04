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
        Schema::create('conduce', function (Blueprint $table) {
            $table->id();
            $table->string('consecutivo');
            $table->string('destino');
            $table->string('codeEntidad');
            $table->string('direccion');
            $table->string('motivoConduce');
            $table->string('producto');
            $table->string('chofer');
            $table->string('carnetChofer');
            $table->string('fechaTransporte');
            $table->string('lugarEntrega');
            $table->string('recibe');
            $table->string('recibeCarnet');
            $table->string('recibeCargo');
            $table->string('fechaRecibido');
            $table->string('user_id');
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
        Schema::dropIfExists('conduce');
    }
};
