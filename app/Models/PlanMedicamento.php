<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PlanMedicamento extends Model
{
    protected $table="planmedicamento";
    protected $connection = 'mysql';
  

    protected $fillable = [

        'referencia',
        'cantidad'

    ];





    use HasFactory;
}
