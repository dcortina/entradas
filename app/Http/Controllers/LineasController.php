<?php

namespace App\Http\Controllers;

use App\Models\Linea;
use App\Models\Producto;
use App\Models\Recepcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PlanMedicamento;

class LineasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $lineasProductos= DB::connection('sqlsrv')->table('TBL_LINEAREC')
        ->select('TBL_LINEAREC.CODLINEAREC as linea_id', 'TBL_LINEAREC.DESCRIPCION as descripcion')
        ->get();

        return $lineasProductos;


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

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Linea  $linea
     * @return \Illuminate\Http\Response
     */
    public function show(Linea $linea)
    {
        //



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Linea  $linea
     * @return \Illuminate\Http\Response
     */
    public function edit(Linea $linea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Linea  $linea
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Linea $linea)
    {
        //


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Linea  $linea
     * @return \Illuminate\Http\Response
     */
    public function destroy(Linea $linea)
    {
        //
    }


    public function ObtenerProductosPorLinea(Request $request)

    {
        //
        $mostrar=[];
        $fechaActual= now();
        $sumaCantidad=0;
        $ultimaEntrada='';
        $cantidadUltimaEntrada=0;
        $acumuladoAnual=0;
        $planAnual=0;
        $planMes=0;
        $cumPlan=0;
        $diasAbastecidos=0;

        $cobertura='';

        //CONSULTA DONDE SELECCIONO LOS PRODUCTOS DEL MISTRAL SEGUN LINEA CON SU CANTIDAD AREA QUE PERTENECE Y CODIGO ORIGINAL
        $productos= DB::connection('sqlsrv')->table('RECAMBIO')
        ->join('ALMACEN_RECAMBIO', 'RECAMBIO.REFERENCIA', '=', 'ALMACEN_RECAMBIO.REFERENCIA')
        ->select('RECAMBIO.CODLINEAREC as codlinearec','RECAMBIO.REFERENCIA as referencia','RECAMBIO.DESCRIPCION as descripcion', 'ALMACEN_RECAMBIO.CANTIDAD as cantidad',
        'ALMACEN_RECAMBIO.CODAREA as codarea','RECAMBIO.CODIGOORIGINAL')
        ->where([
            ['RECAMBIO.CODLINEAREC','=',$request->linea_id],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'RESEM'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'RESE'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'RESEF'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'RECHA'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'PREDES'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'RETEN'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'LMOV'],
            ['ALMACEN_RECAMBIO.CODAREA', '!=', 'OCIOSO']
        ])
        ->get();

        foreach($productos as $producto){

            $cantidad =DB::table('productos')
            ->join('recepcionproducto', 'productos.id','=','recepcionproducto.producto_id' )
            ->select(DB::raw('SUM(recepcionproducto.cantidad) as total_cant'),'productos.descripcion','productos.referencia' ,'productos.created_at', 'productos.id')
            ->where('productos.referencia','=',$producto->referencia)
            ->get();

            //OBTIENE LA SUMA ACUMULADA DE LAS ENTRADAS EN MISTRAL
            $cantidadAcumuladaAño=DB::connection('sqlsrv')->table('RECEPCION_CIEGA')
        ->select(DB::raw('SUM(RECEPCION_CIEGA.CANTIDAD) as totalAcumulado'))
        ->where('RECEPCION_CIEGA.REFERENCIA','=',$producto->referencia)
        ->whereYear('FECHA','=',$fechaActual->isoFormat('YYYY'))
        //->groupBy('RECEPCION_CIEGA.REFERENCIA')
        ->value('totalAcumulado');

        //OBTIENE LA SUMA ACUMULADA DE LAS ENTRADAS EN LA BD ENTRADAS
        $cantidadAcumuladoAñoProducto =DB::table('productos')
        ->join('recepcionproducto', 'productos.id','=','recepcionproducto.producto_id' )
        ->select(DB::raw('SUM(recepcionproducto.cantidad) as total_cant'),'productos.descripcion','productos.referencia' ,'productos.created_at', 'productos.id')
        ->where('productos.referencia','=',$producto->referencia)
        ->whereYear('recepcionproducto.created_at','=',$fechaActual->isoFormat('YYYY'))
        ->value('total_cant');

        //la uso para ver si una factura ingresada en el mistral tambien se encuentra en la bd entradas
        $AcumuladoAño=DB::connection('sqlsrv')->table('RECEPCION_CIEGA')
        ->select('REFERENCIA','CANTIDAD','FACTURA')
        ->where('RECEPCION_CIEGA.REFERENCIA','=',$producto->referencia)
        ->whereYear('FECHA','=',$fechaActual->isoFormat('YYYY'))
        ->get();

        $coincide =0;

        foreach($AcumuladoAño as $item){

            $valor =DB::table('productos')
            ->join('recepcionproducto', 'productos.id','=','recepcionproducto.producto_id' )
            ->join('entradas','recepcionproducto.entrada_id','=','entradas.id')
            ->select('recepcionproducto.cantidad')
            ->where([
                ['productos.referencia','=',$producto->referencia],
                ['entradas.noFactura',$item->FACTURA]
            ])

            ->whereYear('recepcionproducto.created_at','=',$fechaActual->isoFormat('YYYY'))
            ->value('cantidad');

                //la uso para despues restar las cantidades de una misma factura que esta tanto en mistral como en la bd entradas
            $coincide = $coincide + $valor;
        }

        $planEste=PlanMedicamento::select('cantidad')->where('referencia',$producto->CODIGOORIGINAL)->whereYear('created_at','=',$fechaActual->isoFormat('YYYY'))->value('cantidad');

           foreach($cantidad as $cantidad){

            if ($producto->referencia == $cantidad->referencia){

                $sumaCantidad= ($producto->cantidad+$cantidad->total_cant)-$coincide;

                $ultimaEntrada=Producto::find($cantidad->id)->recepcionProductos()->latest()->value('created_at');

                $cantidadUltimaEntrada=Producto::find($cantidad->id)->recepcionProductos()->latest()->value('cantidad');

                $acumuladoAnual=($cantidadAcumuladaAño+$cantidadAcumuladoAñoProducto)-$coincide;

            }else{
                $sumaCantidad = $producto->cantidad;

                $ultimaEntrada = Recepcion::where('REFERENCIA',$producto->referencia)->orderBy('FECHA','desc')->value('FECHA');

                $cantidadUltimaEntrada = Recepcion::where('REFERENCIA',$producto->referencia)->orderBy('FECHA','desc')->value('CANTIDAD');

                $acumuladoAnual=$cantidadAcumuladaAño;
            }

           }

           if($planEste > 0){

            $planAnual = $planEste;
            $planMes = $planEste/12;
            $cumPlan = ($acumuladoAnual*100)/$planAnual;
            $diasAbastecidos=($sumaCantidad*365)/$planAnual;

           }else{
            $planAnual=0;
            $planMes=0;
            $cumPlan=0;
            $diasAbastecidos=0;
        }

        if($diasAbastecidos >0 && $diasAbastecidos <= 30.99){

            $cobertura = 'Baja Cobertura';
        }else if($diasAbastecidos == 0){
            $cobertura = 'Falta Provincial';
        }else if($diasAbastecidos>=31 && $diasAbastecidos <90){
            $cobertura = 'Cobertura correcta';
        }else{
            $cobertura = 'Sobre Abastecido';
        }



                $var=['codlinearec'=>$producto->codlinearec, 'referencia'=>$producto->referencia, 'descripcion'=>$producto->descripcion,
                'cantidad'=>$sumaCantidad,'codarea'=>$producto->codarea, 'ultimaEntrada'=>$ultimaEntrada,'cantidadUltimaEntrada'=>$cantidadUltimaEntrada,
                'acumuladoAunual'=>$acumuladoAnual, 'planAnual'=>$planAnual, 'planMes'=>$planMes, 'cumPlan'=>$cumPlan,'diasAbastecidos'=>$diasAbastecidos,
                'cobertura'=>$cobertura,'codigooriginal'=> $producto->CODIGOORIGINAL];

                array_push($mostrar,$var);

        }



        return $mostrar;




    }


    public function actualizarPlan(Request $request){


        foreach($request->items as $key[] => $value){

           $id=PlanMedicamento::select('id')->where('referencia','=',$value['codigoOriginal'])->value('id');

           if(!$id == null){

            $producto = PlanMedicamento::findOrFail($id);

            $producto->cantidad=$value['plan'];
            $producto->update();


           }else{

            PlanMedicamento::create([
                'referencia'    =>  $value['    codigoOriginal'],
                'cantidad'   =>  $value['plan'],

            ]);



           }




        }

/*
            $response['status'] = 1;
            $response['message']= 'Producto guardado correctamente';
            $response['code']=200;


            return response()->json($response);
            */

    }


}
