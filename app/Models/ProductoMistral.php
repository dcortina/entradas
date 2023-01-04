<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoMistral extends Model
{
    use HasFactory;
   

    protected $table='RECAMBIO';
    protected $connection = 'sqlsrv';

}
