<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    //

    public function consultaPrecios(Request $request){

        $data = $request->all();

        if(empty($data)==false){

            echo $data;

        }

    }

}
