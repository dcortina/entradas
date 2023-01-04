<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\nuevoProductoController;
use App\Models\PlanMedicamento;
use Carbon\Carbon;

class PlanMedicamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $plan= PlanMedicamento::all();

        return response()->json($plan);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data = $request->all();

        if(empty($data)==false){

        foreach($data as $value){

            $id=PlanMedicamento::select('id')->where('referencia','=',$value['referencia'])->value('id');

            if(isset($value['referencia'])){

            if(!$id == null){

             $producto = PlanMedicamento::findOrFail($id);

        //     $producto->cantidad=$value['cantidad'];
             $producto->delete();

             if(empty($value['cantidad'])){
                PlanMedicamento::create([
                    'referencia' =>  $value['referencia'],
                    'cantidad'   =>  0,

                ]);
            }else{

             PlanMedicamento::create([
                 'referencia' =>  $value['referencia'],
                 'cantidad'   =>  $value['cantidad'],

             ]);


            }


            }elseif($id == null && empty($value['cantidad'])){
                PlanMedicamento::create([
                    'referencia' =>  $value['referencia'],
                    'cantidad'   =>  0,

                ]);
            }else{

             PlanMedicamento::create([
                 'referencia' =>  $value['referencia'],
                 'cantidad'   =>  $value['cantidad'],

             ]);



            }

            $response['status'] = 1;
            $response['message']= 'Producto guardado correctamente';
            $response['code']=200;

        }
        else{

            $response['status'] = 0;
            $response['message']= 'El plan que intenta importar no es correcto';
            $response['code']=200;

        }
    }


         }else{
            $response['status'] = 0;
        $response['message']= 'No se ha mandado un plan';
        $response['code']=400;
        }


        return response()->json($response);


/*
        $data = $request->all();

        if(empty($data)==false){


foreach($data as $item) {

    $plan = new PlanMedicamento();

    if(isset($item['referencia'])){

        $plan->referencia = $item['referencia'];

        if (empty($item['cantidad'])){

            $plan->cantidad = 0;

        }else{

            $plan->cantidad = $item['cantidad'];

        }

        $plan->save();

    $response['status'] = 1;
    $response['message']= 'Producto guardado correctamente';
    $response['code']=200;

    }else{

    $response['status'] = 0;
    $response['message']= 'El plan que intenta importar no es correcto';
    $response['code']=200;

    }


}


}else{
    $response['status'] = 0;
$response['message']= 'No se ha mandado un plan';
$response['code']=400;
}



return response()->json($response);


*/


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
