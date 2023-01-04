<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepcionProducto extends Model
{
    use HasFactory;

    protected $table= "recepcionproducto";
    protected $connection = 'mysql';

    protected $fillable = [
        'lote',
        'fechaFabricacion',
        'fechaVence',
        'cantidad',
        'cantBultos',
        'producto_id',
        'entrada_id'

    ];



}
