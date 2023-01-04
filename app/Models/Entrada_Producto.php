<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada_Producto extends Model
{
    use HasFactory;

    protected $table= "entrada_producto";
    protected $connection = 'mysql';

    protected $fillable = [

        'entrada_id',
        'producto_id'

    ];

}
