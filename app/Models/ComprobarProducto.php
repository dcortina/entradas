<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobarProducto extends Model
{
    use HasFactory;

    protected $table= "comprobarproductos";
    protected $connection = 'mysql';

    protected $fillable = [
        'noFactura',
        'fechaLlegada',
        'fechaSalida',
        'observaciones',
        'provedores_id'
    ];
}
