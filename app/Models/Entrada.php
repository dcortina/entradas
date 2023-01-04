<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;
use App\Models\RecepcionProducto;
class Entrada extends Model
{
    use HasFactory;

    protected $table= "entradas";
    protected $connection = 'mysql';

    protected $fillable = [
        'noFactura',
        'fechaLlegada',
        'fechaSalida',
        'observaciones',
        'provedores_id'
    ];

/*
    public function productos(){

        return $this->hasMany(Producto::class);


    }
*/

    public function productos(){

        return $this->belongsToMany(Producto::class);

    }

    public function recepcionProductos(){

        return $this->hasMany(RecepcionProducto::class);


    }

}
