<?php

namespace App\Http\Controllers;

use App\Models\Conduce;
use App\Models\ProductoConduce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConduceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $conduce = DB::table('conduce')
        ->join('productosconduce', 'conduce.id','=','productosconduce.conduce_id')
        ->join('users', 'conduce.user_id','=','users.id')
        ->select('conduce.consecutivo','conduce.fechaTransporte','conduce.created_at as fechaCreado' ,'conduce.destino','conduce.codeEntidad','conduce.direccion',
                 'conduce.motivoConduce','conduce.lugarEntrega','conduce.chofer','conduce.carnetChofer','users.name','users.cargo','users.carnet','conduce.recibe','conduce.recibeCarnet', 'conduce.recibeCargo')
        ->groupBy('conduce.consecutivo')
        ->get();



                 return response()->json($conduce);
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
        $conduce= Conduce::where('consecutivo',$request['consecutivo'])->first();
        $productoConduce = new ProductoConduce();

        if($conduce==true){

            $response['status'] = 0;
            $response['message']= 'El Conduce ya se encuentra en el sistema';
            $response['code']=409;

        }else{

            $conduce=Conduce::create([
                'consecutivo'   =>  $request->consecutivo,
                'fechaTransporte' =>  Carbon::parse($request->fecha),
                'destino'       =>  $request->destino,
                'codeEntidad'   =>  $request->codeEntidad,
                'direccion'     =>  $request->direccion,
                'motivoConduce' =>  $request->motivoConduce,
                'chofer'        =>  $request->chofer,
                'carnetChofer'  =>  $request->carnetChofer,
                'fechaRecibido' =>  Carbon::parse($request->fechaRecibido),
                'lugarEntrega'  =>  $request->lugarEntrega,
                'recibe'        =>  $request->recibe,
                'recibeCarnet'  =>  $request->recibeCarnet,
                'recibeCargo'   =>  $request->recibeCargo,
                'user_id'       =>  $request->usuario_id,
            ]);



            foreach($request->items as $key[] => $value){

                $productoConduce::create([

                    'referencia'          =>  $value['referencia'],
                    'descripcion'          =>  $value['descripcion'],
                    'um'          =>  $value['unidadMedida'],
                    'cantidad'          =>  $value['cantidad'],
                    'precio'          =>  $value['precio'],
                    'importe'          =>  $value['importe'],
                    'conduce_id'=> $conduce->id

                   ]);

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

    public function clientes(){

        $clientes = DB::connection('sqlsrv')->table('ENTIDAD')
        ->join('TBL_MUNICIPIOS', 'ENTIDAD.MUNICIPIO', '=', 'TBL_MUNICIPIOS.CODIGO')
        ->select('ENTIDAD.CODENTIDAD as codeEntidad', 'ENTIDAD.DESCRIPCION as descripcion', 'ENTIDAD.DIRECCION as direccion',
         'ENTIDAD.EMAIL as email','ENTIDAD.TELEFONO as telefono', 'TBL_MUNICIPIOS.DESCRIPCION AS municipio')
        ->where('ENTIDAD.TIPO', '=', 4)
        ->get();



     return response()->json($clientes);

    }

    public function chofer(){

        $chofer = DB::connection('sqlsrv')->table('TBL_CHOFERES')
        ->select('TBL_CHOFERES.NOMBRE as nombre', 'TBL_CHOFERES.CCI as carnet')
        ->get();



     return response()->json($chofer);

    }

    public function productosConduce(){

        $productos = DB::table('conduce')
        ->join('productosconduce', 'conduce.id','=','productosconduce.conduce_id')
        ->select('conduce.consecutivo','productosconduce.referencia','productosconduce.descripcion','productosconduce.um',
                 'productosconduce.cantidad','productosconduce.precio','productosconduce.importe')
        ->get();



                 return response()->json($productos);

    }


}
