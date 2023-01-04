<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entrada;
use App\Models\RecepcionProducto;
use App\Models\Provedor;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'referencia',
        'descripcion',
        'provedores_id'

    ];

    protected $table="productos";
    protected $connection = 'mysql';

    public function entradas()
    {
        return $this->belongsToMany(Entrada::class);
    }

    public function recepcionProductos(){

        return $this->hasMany(RecepcionProducto::class);


    }


    public function provedores(){

        return $this->belongsToMany(Provedor::class);


    }

}
