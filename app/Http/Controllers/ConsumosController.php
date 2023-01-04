<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consumo;
use Illuminate\Support\Facades\DB;
use App\Models\Recambio;
use App\Http\Controllers\nuevoProductoController;

class ConsumosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $consumos= DB::connection('sqlsrv')->table('CONSUMOS')
        ->select('CONSUMOS.entidad', 'CONSUMOS.descentidad')
       ->groupBy('CONSUMOS.DESCENTIDAD', 'CONSUMOS.ENTIDAD')
        ->get();


        return $consumos;


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

    public function obtenerConsumos(Request $request)
    {

        $fechaActual= now();

     //Consumo planificado segun unidad escogida
      $consumosPorUnidad=DB::connection('sqlsrv')->table('CONSUMOS')
      ->select('CONSUMOS.referencia','CONSUMOS.descripcion', 'CONSUMOS.cantidad', 'CONSUMOS.precio', 'CONSUMOS.importe')
      ->where('CONSUMOS.entidad', $request->entidad)
      ->get();


    $cantidadesVendidas = DB::connection('sqlsrv')->table('MOVIMIENTO')
      ->select('MOVIMIENTO.referencia',DB::raw('SUM(MOVIMIENTO.cantidad) as totalAcumulado'),
               DB::raw('SUM(MOVIMIENTO.importe) as importeAcumulado'))
      ->whereYear('MOVIMIENTO.fecha',$fechaActual->isoFormat('YYYY'))
      ->where([['MOVIMIENTO.codmovimiento','VENT'],['MOVIMIENTO.codentidad',$request->entidad]])
      ->groupBy('referencia')->get();

  return   $consumosPorUnidad->map(function($consumo)use($cantidadesVendidas){

           $cantidadVendida=$cantidadesVendidas->filter(function($cantidad)use($consumo){
                   return $cantidad->referencia ==$consumo->referencia;})->map(function($vendido){
               return $prueba []=['vendido'=>$vendido->totalAcumulado];})->value('vendido');

            $importeVendido=$cantidadesVendidas->filter(function($cantidad)use($consumo){
                return $cantidad->referencia ==$consumo->referencia;})->map(function($vendido){
           return $prueba []=['importeVendido'=>$vendido->importeAcumulado];})->value('importeVendido');


            if($cantidadVendida==null){
                $cantidadVendida=0;
            }

            if($importeVendido==null){
                $importeVendido=0;
            }

            return    $response []=[
                                 'descripcion'=>$consumo->descripcion,
                                 'precio'=>$consumo->precio,
                                 'cantidad'=>$consumo->cantidad,
                                 'importe'=>$consumo->precio*$consumo->cantidad,
                                 'vendido'=>$cantidadVendida,
                                 'importeVendido'=>$importeVendido,
                                ];


        });


        }

        public function obtenerConsumosPorCodigo(){

            $codigoOriginal = DB::connection('sqlsrv')->table('RECAMBIO')
            ->join('ALMACEN_RECAMBIO', 'RECAMBIO.REFERENCIA', '=', 'ALMACEN_RECAMBIO.REFERENCIA')
            -> select('RECAMBIO.codigooriginal',DB::raw('SUM(ALMACEN_RECAMBIO.CANTIDAD) as cantidad'))
            -> where( 'ALMACEN_RECAMBIO.CODAREA', '=', 'MPV1')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV19')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV24')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV18')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV26')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV6')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV13')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV17')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV10')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV5')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV12')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV4')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV28')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV2')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV7')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV9')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV3')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV27')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV15')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV20')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV11')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV22')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV25')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV23')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV21')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV16')
            ->orWhere('ALMACEN_RECAMBIO.CODAREA', '=', 'MPV8')
            -> groupBy('RECAMBIO.codigooriginal')
            ->get();

            return $codigoOriginal;

        }



 public function productoMasVendido(){
    $fechaActual= now();

    $producto = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('RECAMBIO','MOVIMIENTO.REFERENCIA','=','RECAMBIO.REFERENCIA')
    ->select('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION',DB::raw('COUNT(MOVIMIENTO.REFERENCIA) as masVendido'))
    ->where('codmovimiento','VENT')
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION')
    ->orderBy('masVendido','desc')
    ->take(1)
    ->get();

    return $producto;

 }

 public function productoConMayorImporte(){

    $fechaActual= now();

    $productoImporte =  DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('RECAMBIO','MOVIMIENTO.REFERENCIA','=','RECAMBIO.REFERENCIA')
    ->select('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION',DB::raw('SUM(MOVIMIENTO.IMPORTE) as mayorImporte'))
    ->where('codmovimiento','VENT')
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION')
    ->orderBy('mayorImporte','desc')
    ->take(1)
    ->get();

    return $productoImporte;

 }


 public function unidadMasActiva(){

    $fechaActual= now();

    $unidadActiva = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('ENTIDAD','MOVIMIENTO.CODENTIDAD','=','ENTIDAD.CODENTIDAD')
    ->select('MOVIMIENTO.CODENTIDAD','ENTIDAD.DESCRIPCION',DB::raw('COUNT(MOVIMIENTO.CODENTIDAD) as mayorImporte'))
    ->where('codmovimiento','VENT')
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.CODENTIDAD','ENTIDAD.DESCRIPCION')
    ->orderBy('mayorImporte','desc')
    ->take(1)
    ->get();

    return $unidadActiva;

 }



 public function unidadConMayorImporte(){

    $fechaActual= now();

    $unidadImporte = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('ENTIDAD','MOVIMIENTO.CODENTIDAD','=','ENTIDAD.CODENTIDAD')
    ->select('MOVIMIENTO.CODENTIDAD','ENTIDAD.DESCRIPCION',DB::raw('SUM(MOVIMIENTO.IMPORTE) as mayorImporte'))
    ->where('codmovimiento','VENT')
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.CODENTIDAD','ENTIDAD.DESCRIPCION')
    ->orderBy('mayorImporte','desc')
    ->take(1)
    ->get();

    return $unidadImporte;

 }

 public function productosMasVendidos(){
    $fechaActual= now();

    $producto = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('RECAMBIO','MOVIMIENTO.REFERENCIA','=','RECAMBIO.REFERENCIA')
    ->select('MOVIMIENTO.REFERENCIA as referencia','RECAMBIO.DESCRIPCION as descripcion',DB::raw('COUNT(MOVIMIENTO.REFERENCIA) as masVendido'))
    ->where([['MOVIMIENTO.CODMOVIMIENTO','VENT']])
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION')
    ->orderBy('masVendido','desc')
    ->take(5)
    ->get();

    return $producto;

 }

 public function productosMasimporte(){
    $fechaActual= now();
    $producto = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('RECAMBIO','MOVIMIENTO.REFERENCIA','=','RECAMBIO.REFERENCIA')
    ->select('MOVIMIENTO.REFERENCIA as referencia','MOVIMIENTO.CODMOVIMIENTO as code','RECAMBIO.DESCRIPCION as descripcion',DB::raw('SUM(MOVIMIENTO.IMPORTE) as importe'))
    ->where([['MOVIMIENTO.CODMOVIMIENTO','VENT']])
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION','MOVIMIENTO.CODMOVIMIENTO')
    ->orderBy('importe','desc')
    ->take(50)
    ->get();

    return response()->json($producto);

 }

 public function productosLentoMovimiento(){

    $fechaActual= now();
    $producto = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->join('RECAMBIO','MOVIMIENTO.REFERENCIA','=','RECAMBIO.REFERENCIA')
    ->select('MOVIMIENTO.REFERENCIA as referencia','MOVIMIENTO.CODMOVIMIENTO as code','RECAMBIO.DESCRIPCION as descripcion','MOVIMIENTO.CANTIDAD as cantidad', DB::raw('SUM(MOVIMIENTO.IMPORTE) as importe'))
    ->where([['MOVIMIENTO.CODMOVIMIENTO','VENT'],['MOVIMIENTO.CANTIDAD','<',100]])
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->groupBy('MOVIMIENTO.REFERENCIA','RECAMBIO.DESCRIPCION','MOVIMIENTO.CODMOVIMIENTO','MOVIMIENTO.CANTIDAD')
    ->orderBy('cantidad')
    ->get();

    return response()->json($producto);

 }

 public function importeTotal(){

    $fechaActual= now();
    $importe = DB::connection('sqlsrv')->table('MOVIMIENTO')
    ->select(DB::raw('SUM(MOVIMIENTO.IMPORTE) as importe'))
    ->where([['MOVIMIENTO.CODMOVIMIENTO','VENT']])
    ->whereYear('fecha',$fechaActual->isoFormat('YYYY'))
    ->value('importe');

    return response()->json($importe);

 }





}
