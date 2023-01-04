<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conduce extends Model
{
    use HasFactory;

    protected $table= "conduce";
    protected $connection = 'mysql';


    protected $fillable = [
        'consecutivo',
        'destino',
        'codeEntidad',
        'direccion',
        'motivoConduce',
        'chofer',
        'carnetChofer',
        'fechaTransporte',
        'lugarEntrega',
        'recibe',
        'recibeCarnet',
        'recibeCargo',
        'fechaRecibido',
        'user_id'

    ];

    public function productosConduce(){

        return $this->belongsToMany(Producto::class);

    }

}
