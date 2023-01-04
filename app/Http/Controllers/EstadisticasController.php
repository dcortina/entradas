<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PlanMedicamento;
use App\Models\Linea;

class EstadisticasController extends Controller
{
    //


public function acumuladoPorLinea (){

 // lineas de productos
$lineaProductos = Linea::select('DESCRIPCION')->where('CODLINEAREC','!=','19')->get();

 $calculoPorciento= function($valor){

 $fechaActual= now();
 $lineaProductos = DB::connection('sqlsrv')->table('TBL_LINEAREC')

   ->join('RECAMBIO', 'TBL_LINEAREC.CODLINEAREC','=','RECAMBIO.CODLINEAREC')
   ->join('RECEPCION_CIEGA', 'RECAMBIO.REFERENCIA','=','RECEPCION_CIEGA.REFERENCIA')
   ->select('TBL_LINEAREC.CODLINEAREC', 'RECAMBIO.CODIGOORIGINAL',DB::raw('SUM(RECEPCION_CIEGA.CANTIDAD) as totalAcumulado'))
   ->whereYear('RECEPCION_CIEGA.FECHA',$fechaActual->isoFormat('YYYY'))
   ->groupBy('TBL_LINEAREC.CODLINEAREC','RECAMBIO.CODIGOORIGINAL')
   ->get();

   $planPorLineas= $lineaProductos->filter(function($linea)use($valor){return $linea->CODLINEAREC == $valor;})->map(function($productoTableta) use($fechaActual){

      return PlanMedicamento::where('referencia',$productoTableta->CODIGOORIGINAL)->whereYear('created_at', $fechaActual->isoFormat('YYYY'))->value('cantidad');})->sum();

     $acumulado= $lineaProductos->filter(function($linea)use($valor){return $linea->CODLINEAREC == $valor;})->map(function($acumulado){return $acumulado->totalAcumulado;})->sum();


       if($planPorLineas == null || $acumulado == null){
           return 0;
       }else{

     return ($acumulado*100)/$planPorLineas;

   }

    };




   $cumplimientos=[
   $cumplimientoPlanTabletas=$calculoPorciento('01'),
   $cumplimientoPlanLiquidos=$calculoPorciento('02'),
   $cumplimientoPlanInyectables=$calculoPorciento('03'),
   $cumplimientoPlanSemisolidos=$calculoPorciento('04'),
   $cumplimientoPlanDrogas=$calculoPorciento('05'),
   $cumplimientoPlanVacunas=$calculoPorciento('06'),
   $cumplimientoPlanControlados=$calculoPorciento('07'),
   $cumplimientoPQuimicos=$calculoPorciento('08'),
   $cumplimientoReactivos=$calculoPorciento('09'),
   $cumplimientoHigienicos=$calculoPorciento('10'),
   $cumplimientoPolvos=$calculoPorciento('11'),
   $cumplimientoSueros=$calculoPorciento('12'),
   $cumplimientoAntiretrovirales=$calculoPorciento('13'),
   $cumplimientoPDentales=$calculoPorciento('14'),
   $cumplimientoEmsume=$calculoPorciento('15'),
   $cumplimientoPeligrosas=$calculoPorciento('16'),
   $cumplimientoPNaturales=$calculoPorciento('17'),
   $cumplimientoHomeopaticos=$calculoPorciento('18'),
   ];

   foreach($lineaProductos as $key=>$linea){

       $mostrar []=[
           'linea'=>$linea->DESCRIPCION,
           'porciento'=>$cumplimientos[$key]
       ];

   }


 return $mostrar;
 }


 public function ultimasEntradas(){

    $fechaActual= now();




    $response = [];


    $entradas = DB::connection('sqlsrv')->table('RECEPCION_CIEGA')

     ->join('RECAMBIO', 'RECEPCION_CIEGA.REFERENCIA','=','RECAMBIO.REFERENCIA')
    //->join('RECEPCION_CIEGA', 'RECAMBIO.REFERENCIA','=','RECEPCION_CIEGA.REFERENCIA')
    ->select('RECEPCION_CIEGA.REFERENCIA','RECAMBIO.DESCRIPCION' ,'RECEPCION_CIEGA.CANTIDAD' ,'RECAMBIO.CODIGOORIGINAL','RECEPCION_CIEGA.FECHA')
    ->where('RECEPCION_CIEGA.CODTIPORECEP', 'RECEP')
    ->whereYear('RECEPCION_CIEGA.FECHA',$fechaActual->isoFormat('YYYY'))
    ->orderBy('RECEPCION_CIEGA.FECHA', 'desc')
    ->take(4)
    ->get();




    foreach($entradas as $entrada){

        $cantidadAcumuladaAño=DB::connection('sqlsrv')->table('RECEPCION_CIEGA')
        ->select(DB::raw('SUM(RECEPCION_CIEGA.CANTIDAD) as totalAcumulado'))
        ->where('RECEPCION_CIEGA.REFERENCIA','=',$entrada->REFERENCIA)
        ->whereYear('FECHA','=',$fechaActual->isoFormat('YYYY'))
        //->groupBy('RECEPCION_CIEGA.REFERENCIA')
        ->value('totalAcumulado');

        $planEste=PlanMedicamento::select('cantidad')->where('referencia',$entrada->CODIGOORIGINAL)->value('cantidad');

       if ($planEste > 0){

        $cumPlan = ($cantidadAcumuladaAño*100)/$planEste;



    }
    else{
        $cumPlan =0;
    }

    $cantUltimas= DB::connection('sqlsrv')->table('RECEPCION_CIEGA')
    ->select('RECEPCION_CIEGA.CANTIDAD as cantEntrada', 'RECEPCION_CIEGA.FECHA as fechaEntrada')
    ->where('RECEPCION_CIEGA.REFERENCIA','=',$entrada->REFERENCIA)
    ->whereYear('FECHA','=',$fechaActual->isoFormat('YYYY'))
    ->orderBy('fechaEntrada', 'desc')
   ->take(5)
    ->get();

    $prueba = $cantUltimas->map(function($cantidad){


       $valor= $cantidad->cantEntrada+0;

       $fecha = $cantidad->fechaEntrada;

       return  $lastEntradas []=[
        'cantidad'=>$valor,
        'fecha'=>$fecha
       ];

    });

        $var=['codigo'=>$entrada->REFERENCIA, 'descripcion'=>$entrada->DESCRIPCION,'cantidad'=>$entrada->CANTIDAD, 'planAnual'=>$planEste, 'acumulado'=>$cantidadAcumuladaAño, 'cumPlan'=>$cumPlan, 'cantUltimas'=>$prueba];


        array_push($response, $var);




    }
/*
    $response['codigo']=$entrada->REFERENCIA;
    $response['descripcion']=$entrada->DESCRIPCION;
    $response['cantidad']=$entrada->CANTIDAD;
    $response['planAnual']=$planEste;
    $response['acumulado']=$cantidadAcumuladaAño;
    $response['cumPlan']=$cumPlan;
    $response['cantUltimas']=$cantUltimas;
    */

    return response()->json($response);


 }

 public function porcientoSituacion() {
    //

    $fechaActual= now();



    //CONSULTA DONDE SELECCIONO LOS PRODUCTOS DEL MISTRAL SEGUN LINEA CON SU CANTIDAD AREA QUE PERTENECE Y CODIGO ORIGINAL
    $productos= DB::connection('sqlsrv')->table('RECAMBIO')
    ->join('ALMACEN_RECAMBIO', 'RECAMBIO.REFERENCIA', '=', 'ALMACEN_RECAMBIO.REFERENCIA')
    ->select('RECAMBIO.CODLINEAREC as codlinearec','RECAMBIO.REFERENCIA as referencia','RECAMBIO.DESCRIPCION as descripcion', 'ALMACEN_RECAMBIO.CANTIDAD as cantidad',
    'ALMACEN_RECAMBIO.CODAREA as codarea','RECAMBIO.CODIGOORIGINAL')
    ->where([
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

 $existenciasBajaCobertura=$productos->map(function($producto)use($fechaActual){
    $planEste=PlanMedicamento::select('cantidad')->where('referencia',$producto->CODIGOORIGINAL)
    ->whereYear('created_at','=',$fechaActual->isoFormat('YYYY'))->value('cantidad');
    if($planEste>0){
    $da = ($producto->cantidad*365)/$planEste;
}else{
    $da=0;
}
     return   $da;
    })->filter(function($item){
        return $item > 0 && $item <= 30.99;
    })->count();

$existenciasFP=$productos->map(function($producto)use($fechaActual){
        $planEste=PlanMedicamento::select('cantidad')->where('referencia',$producto->CODIGOORIGINAL)
        ->whereYear('created_at','=',$fechaActual->isoFormat('YYYY'))->value('cantidad');
        if($planEste>0){
        $da = ($producto->cantidad*365)/$planEste;
    }else{
        $da=0;
    }
         return   $da;
        })->filter(function($item){
            return $item ==0;
        })->count();

$existenciasCC=$productos->map(function($producto)use($fechaActual){
            $planEste=PlanMedicamento::select('cantidad')->where('referencia',$producto->CODIGOORIGINAL)
            ->whereYear('created_at','=',$fechaActual->isoFormat('YYYY'))->value('cantidad');
            if($planEste>0){
            $da = ($producto->cantidad*365)/$planEste;
        }else{
            $da=0;
        }
             return   $da;
            })->filter(function($item){
                return $item >= 31 && $item <90;
            })->count();

$existenciasSA=$productos->map(function($producto)use($fechaActual){
                $planEste=PlanMedicamento::select('cantidad')->where('referencia',$producto->CODIGOORIGINAL)
                ->whereYear('created_at','=',$fechaActual->isoFormat('YYYY'))->value('cantidad');
                if($planEste>0){
                $da = ($producto->cantidad*365)/$planEste;
            }else{
                $da=0;
            }
                 return   $da;
                })->filter(function($item){
                    return $item >90;
                })->count();



$existencias=$productos->map(function($producto)use($fechaActual){
                    $planEste=PlanMedicamento::select('cantidad')->where('referencia',$producto->CODIGOORIGINAL)
                    ->whereYear('created_at','=',$fechaActual->isoFormat('YYYY'))->value('cantidad');
                    if($planEste>0){
                    $da = ($producto->cantidad*365)/$planEste;
                }else{
                    $da=0;
                }
                     return   $da;
                    })->count();


  return $mostrar[]=['BajaCobertura'=>($existenciasBajaCobertura*100)/$existencias,
                     'FaltaProvincial'=>($existenciasFP*100)/$existencias,
                     'ConCobertura'=>($existenciasCC*100)/$existencias,
                     'SobreAbastecido'=>($existenciasSA*100)/$existencias,
                        ];


 }



}

