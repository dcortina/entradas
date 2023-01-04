<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\Entrada_Producto;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\ProductoMistral;
use App\Models\Recepcion;
use App\Models\RecepcionProducto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Return_;

use function PHPSTORM_META\map;

class nuevaEntradaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $entradas= DB::table('entrada_producto')
        ->join('entradas', 'entrada_producto.entrada_id', '=', 'entradas.id')
        ->join('provedores', 'entradas.provedores_id', '=', 'provedores.id')
        ->join('productos', 'entrada_producto.producto_id', '=', 'productos.id')
        ->join('recepcionproducto', 'productos.id', '=', 'recepcionproducto.producto_id')
        ->select('entrada_producto.entrada_id','entradas.fechaLlegada','entradas.noFactura','provedores.nombre' ,
                 'productos.referencia','productos.descripcion', 'recepcionproducto.cantidad',
                 'recepcionproducto.cantBultos', 'recepcionproducto.fechaVence', 'entradas.observaciones', 'entradas.provedores_id')
        ->groupBy('entrada_producto.entrada_id')
        ->orderBy('entradas.fechaLlegada','desc')
                 ->get();



                 $ultimoId=Producto::select('id')->orderBy('id', 'desc')->first()->value('id');


               return response()->json($entradas);



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
        /*
        $entrada= new Entrada;
        $entrada->noFactura=$request->noFactura;
        $entrada->fechaLlegada=$request->fechaLlegada;
        $entrada->fechaSalida=$request->fechaSalida;
        $entrada->observaciones=$request->observaciones;
        $entrada->producto_id=$request->producto_id;
        $entrada->provedor_id=$request->provedor_id;

        $entrada->save();
*/

        $entrada= Entrada::where('noFactura',$request['noFactura'])->first();
        $producto= new Producto;
        $recepcion= new Entrada_Producto;
        $recepcionProducto = new RecepcionProducto;
        if($entrada==true){

            $response['status'] = 0;
            $response['message']= 'La Factura ya se encuentra en el sistema';
            $response['code']=409;

        }else{

            $entrada=Entrada::create([
                'noFactura'     =>  $request->noFactura,
                'fechaLlegada'  => Carbon::parse($request->fechaLlegada),
                'observaciones' =>  $request->observaciones,
                'provedores_id' =>  $request->provedores
            ]);

        foreach($request->items as $key[] => $value){


   $compruebaProducto=Producto::select('referencia')->where('referencia','=',$request->referencia)->get();


            if($compruebaProducto->isEmpty()){

                $descripcion = ProductoMistral::select('descripcion')->where('referencia',$value['product'])->value('descripcion');

                Producto::create([
                    'referencia'    =>  $value['product'],
                    'descripcion'   =>  $descripcion,
                    'provedores_id' =>  $request->provedores,
                ]);

                $ultimoId=Producto::select('id')->orderBy('id', 'desc')->first();

                $recepcionProducto::create([

                'lote'          =>  $value['lote'],
                'fechaFabricacion' => Carbon::parse($value['fechaFabricacion']),
                'fechaVence'    =>    Carbon::parse($value['fechaVence']),
                'cantidad'      =>  $value['cantidad'],
                'cantBultos'    =>   $value['cantBultos'],
                'producto_id'   =>  $ultimoId->id,
                'entrada_id'=> $entrada->id

               ]);

               $recepcion::create([
                'entrada_id'=> $entrada->id,
                'producto_id'=> $ultimoId->id,
            ]);

            }else{

                $id = Producto::select('id')->where('referencia',$value['product'])->value('id');

                $recepcionProducto::create([

                    'lote'          =>  $value['lote'],
                    'fechaFabricacion' => Carbon::parse($value['fechaFabricacion']),
                    'fechaVence'    =>    Carbon::parse($value['fechaVence']),
                    'cantidad'      =>  $value['cantidad'],
                    'cantBultos'    =>   $value['cantBultos'],
                    'producto_id'   =>  $id,
                    'entrada_id'=> $entrada->id


                   ]);

                   $recepcion::create([
                    'entrada_id'=> $entrada->id,
                    'producto_id'=> $id
                ]);
            }



        }

            $response['status'] = 1;
            $response['message']= 'Producto guardado correctamente';
            $response['code']=200;

        }

        return response()->json($response);

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
        $entrada= Entrada::findOrFail($id);
        $entrada->noFactura = $request->noFactura;
        $entrada->fechaLlegada = $request->fechaLlegada;
        $entrada->observaciones = $request->observaciones;
        $entrada->update();

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


    public function obtenerProductos(Request $request){
/*
        $productos= DB::table('entrada_producto')
        ->join('entradas', 'entrada_producto.entrada_id', '=', 'entradas.id')
        ->join('provedores', 'entradas.provedores_id', '=', 'provedores.id')
        ->join('productos', 'entrada_producto.producto_id', '=', 'productos.id')
        ->join('recepcionproducto', 'productos.id', '=', 'recepcionproducto.producto_id')
       // ->join('recepcionproducto', 'entradas.id', '=', 'recepcionproducto.entrada_id')
        ->select('productos.referencia','productos.descripcion', 'recepcionproducto.cantidad',
                 'recepcionproducto.cantBultos', 'recepcionproducto.fechaVence', 'recepcionproducto.entrada_id')
        ->where('entrada_producto.entrada_id','=',$request->entrada_id)
        ->get();
*/
$prueba=[];

$productos = Entrada::find($request->entrada_id)->recepcionProductos;
$entradas = Entrada::find($request->entrada_id)->productos;

foreach ($entradas as $entrada){
        foreach ($productos as $producto){

            if($entrada->id == $producto->producto_id){
            $var = ['referencia'=>$entrada->referencia,'descripcion'=>$entrada->descripcion,'id'=>$producto->id, 'lote'=>$producto->lote ,'fechaFabricacion' => $producto->fechafabricacion ,
                    'fechaVence' => $producto->fechaVence, 'cantidad' => $producto->cantidad,
                    'cantBultos' => $producto->cantBultos, 'producto_id' => $producto->producto_id, 'entrada_id' => $producto->entrada_id,];

                    array_push($prueba, $var);

                }
        }
    }
/*
    foreach ($entradas as $entrada){

         $var2=['id'=>$entrada->id, 'referencia'=>$entrada->referencia];

         array_push($prueba, $var2);

    }
*/

     return response()->json($prueba);

    }


    public function PendienteArribo(){

        $fechaActual= now();

 //         $facturasEnSistemas= Recepcion::select('FACTURA')->whereYear('FECHA',$fechaActual->isoFormat('YYYY'))->whereMonth('FECHA',$fechaActual->isoFormat('MM'))->groupBy('FACTURA')->get();




            $facturas = DB::connection('sqlsrv')->table('RECEPCIONCIEGA_IMPORT')
            ->join('RECAMBIO','RECEPCIONCIEGA_IMPORT.REFERENCIA','=','RECAMBIO.REFERENCIA')
            ->select('RECEPCIONCIEGA_IMPORT.NUMDOCUMENTO as noFactura')
            ->whereYear('RECEPCIONCIEGA_IMPORT.FECHADOC',$fechaActual->isoFormat('YYYY'))
            ->whereMonth('RECEPCIONCIEGA_IMPORT.FECHADOC',$fechaActual->isoFormat('MM'))
            ->where([['RECEPCIONCIEGA_IMPORT.ESTADO',null]])
            ->groupBy('RECEPCIONCIEGA_IMPORT.NUMDOCUMENTO')
            ->get();


            return      response()->json($facturas);






    }


    public function agregaPendienteArribo(Request $request){

        $fechaActual= now();

 //         $facturasEnSistemas= Recepcion::select('FACTURA')->whereYear('FECHA',$fechaActual->isoFormat('YYYY'))->whereMonth('FECHA',$fechaActual->isoFormat('MM'))->groupBy('FACTURA')->get();





        $facturas = DB::connection('sqlsrv')->table('RECEPCIONCIEGA_IMPORT')
            ->join('RECAMBIO','RECEPCIONCIEGA_IMPORT.REFERENCIA','=','RECAMBIO.REFERENCIA')
            ->select('RECEPCIONCIEGA_IMPORT.REFERENCIA','RECAMBIO.DESCRIPCION','RECEPCIONCIEGA_IMPORT.NUMDOCUMENTO as noFactura'
                    ,'RECEPCIONCIEGA_IMPORT.CANTIDAD','RECEPCIONCIEGA_IMPORT.IMPORTE','RECEPCIONCIEGA_IMPORT.FECHADOC','RECEPCIONCIEGA_IMPORT.FECHAINSERT')
            ->whereYear('RECEPCIONCIEGA_IMPORT.FECHADOC',$fechaActual->isoFormat('YYYY'))
            ->whereMonth('RECEPCIONCIEGA_IMPORT.FECHADOC',$fechaActual->isoFormat('MM'))
            ->where([['RECEPCIONCIEGA_IMPORT.NUMDOCUMENTO',$request->noFactura],['RECEPCIONCIEGA_IMPORT.ESTADO',null]])
            ->get();

            if(!$facturas->isEmpty()){

                return $facturas;

            }else{
                $response['status'] = 0;
                $response['message']= 'No hay factura';
                $response['code']=200;
                return response()->json($response);
            }









    }



}
