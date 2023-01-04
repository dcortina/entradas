<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    use HasFactory;
    protected $table='TBL_LINEAREC';
    protected $connection = 'sqlsrv';


}
