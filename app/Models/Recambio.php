<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recambio extends Model
{
    use HasFactory;

    protected $table='ALMACEN_RECAMBIO';
    protected $connection = 'sqlsrv';


}
