<?php
namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\RecepcionProducto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductosExport;
use App\Models\Entrada;

class nuevoProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {


        $productosTotal=[];
        $fechaActual= now();
        $entradas=Entrada::select('noFactura')->get();
        $facturas = [];
        $productosEntradas=Producto::all();
        //obteniendo los productos con sus cantidades totales desde la bd entradas
        $productosCantidadTotales =DB::table('productos')
        ->join('recepcionproducto', 'productos.id','=','recepcionproducto.producto_id' )
        ->select('productos.id','productos.referencia','productos.descripcion',
                 DB::raw('SUM(recepcionproducto.cantidad) as total_cant'), 'recepcionproducto.producto_id',
                'productos.provedores_id', 'productos.created_at')
        ->whereYear('productos.created_at', $fechaActual->isoFormat('YYYY'))
        ->groupBy('recepcionproducto.producto_id')
       ->get();

      foreach($entradas as $entrada){
      $facturas[]=$entrada->noFactura;
        }

        foreach($productosEntradas as $productosEntrada){

            $ultimaCantidad = RecepcionProducto::where('producto_id',$productosEntrada->id)->get();

            if (!$ultimaCantidad->isEmpty()){

                $productosCantidadTotales =DB::table('productos')
                ->join('recepcionproducto', 'productos.id','=','recepcionproducto.producto_id' )
                ->select(DB::raw('SUM(recepcionproducto.cantidad) as total_cant'))
                ->where('referencia',$productosEntrada->referencia)
                ->whereYear('productos.created_at', $fechaActual->isoFormat('YYYY'))
                ->groupBy('recepcionproducto.producto_id')
                ->value('total_cant');

                $entradasMistralCantidad = DB::connection('sqlsrv')->table('RECEPCION_CIEGA')
                               ->select(DB::raw('SUM(CANTIDAD) as total_cant'))
                                ->where([['RECEPCION_CIEGA.REFERENCIA', $productosEntrada->referencia],
                                         ['FACTURA','!=', $facturas],
                                         ['RECEPCION_CIEGA.CODAREA','!=', 'PREDES']
                                         ])
                               ->whereYear('FECHA','=',$fechaActual->isoFormat('YYYY'))
                               ->value('total_cant');

                $total=  $productosCantidadTotales+ $entradasMistralCantidad;

            }else{

                $entradasMistralCantidad = DB::connection('sqlsrv')->table('RECEPCION_CIEGA')
                               ->select(DB::raw('SUM(CANTIDAD) as total_cant'))
                                ->where([['RECEPCION_CIEGA.REFERENCIA', $productosEntrada->referencia],
                                         ['RECEPCION_CIEGA.CODAREA','!=', 'PREDES']
                                         ])
                               ->whereYear('FECHA','=',$fechaActual->isoFormat('YYYY'))
                               ->value('total_cant');

                        $total =0;
                        $total+=$entradasMistralCantidad;
            }

             $productosTotal[]=['id'=>$productosEntrada->id,
                                'referencia'=>$productosEntrada->referencia,
                                'descripcion'=>$productosEntrada->descripcion,
                                'cantidad'=>$total,
                                'fechaEntrada'=>$productosEntrada->created_at,
                                'provedores_id'=>$productosEntrada->provedores_id
                                ];

        }

        return $productosTotal;

}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*
        $productosRecambio= DB::select('select * from RECAMBIO');

        foreach($productosRecambio as $item){

            echo $item->DESCRIPCION;

        }

        */



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      //  Carbon::parse($request->fechaVence);
        //
        $producto= Producto::where('referencia',$request['referencia'])->first();

        if($producto==true){

            $response['status'] = 0;
            $response['message']= 'El producto ya se encuentra en el sistema';
            $response['code']=409;

        }else{

            $producto=Producto::create([
                'referencia'    =>  $request->referencia,
                'descripcion'   =>  $request->descripcion,
                'provedores_id' =>  $request->provedores_id,
            ]);

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
        $producto = Producto::findOrFail($id);

        $producto->referencia= $request->referencia;
        $producto->descripcion= $request->descripcion;
        $producto->update();
        return $producto;

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
        $producto = Producto::findOrFail($id);
        $producto->delete();
    }


    public function productosMistral(){


        $productos = DB::connection('sqlsrv')->table('RECAMBIO')
        ->join('ALMACEN_RECAMBIO', 'RECAMBIO.REFERENCIA', '=', 'ALMACEN_RECAMBIO.REFERENCIA')
        ->select('RECAMBIO.codigooriginal','RECAMBIO.referencia', 'RECAMBIO.descripcion', 'ALMACEN_RECAMBIO.cantidad', 'RECAMBIO.pvp', 'ALMACEN_RECAMBIO.codarea', 'ALMACEN_RECAMBIO.PCPM as pcpm', 'RECAMBIO.RECARGO as conformado')
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

        ->get();


     return response()->json($productos);

     }


     public function exportExcel(){



     return Excel::download(new ProductosExport, 'productos.xlsx');


     }



}


