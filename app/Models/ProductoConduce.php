<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoConduce extends Model
{
    use HasFactory;

    protected $table= "productosconduce";
    protected $connection = 'mysql';

    protected $fillable = [
        'referencia',
        'descripcion',
        'um',
        'cantidad',
        'precio',
        'importe',
        'conduce_id'

    ];

}
