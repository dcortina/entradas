<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Provedor extends Model
{
    use HasFactory;

    protected $table = "provedores";
    protected $connection = 'mysql';

    protected $fillable = [
        'codeAux',
        'nombre',
        'direccion',
        'provincia',
        'municipio',
        'email',
        'telefono',
        'observaciones',


    ];

    public function producto(){

        return $this->hasMany(Producto::class, 'provedores_id');
    }


}
