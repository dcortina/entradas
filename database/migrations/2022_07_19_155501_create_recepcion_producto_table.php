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
        Schema::create('recepcionProducto', function (Blueprint $table) {
            $table->id();
            $table->string('lote');
            $table->date('fechafabricacion');
            $table->date('fechaVence');
            $table->integer('cantidad');
            $table->integer('cantBultos');
            $table->integer('producto_id');
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
        Schema::dropIfExists('recepcion_producto');
    }
};
