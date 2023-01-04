<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\Http\Controllers\nuevoProductoController;
use App\Models\Producto;

class ProductosExport implements FromArray
{


    
    public function array(): array
    {

        $prueba = new nuevoProductoController;

        $valor=$prueba->index();

   

     

      return $valor;

     
    }
}
