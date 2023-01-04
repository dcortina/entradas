<?php

namespace App\Http\Controllers;

use App\Models\Provedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class nuevoProvedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $provedores = Provedor::select('id','codeAux','nombre','direccion','provincia','municipio','email','telefono','observaciones')->get();

        return response()->json($provedores);
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

       $provedor= Provedor::where('codeAux',$request['codeAux'])->first();

        if($provedor==true){

            $response['status'] = 0;
            $response['message']= 'El provedor ya se encuentra en el sistema';
            $response['code']=409;

        }else{

            $provedor=Provedor::create([
                'codeAux'    =>  $request->codeAux,
                'nombre'   =>  $request->nombre,
                'direccion'   =>  $request->direccion,
                'provincia'   =>  $request->provincia,
                'municipio'   =>  $request->municipio,
                'email'   =>  $request->email,
                'telefono'   =>  $request->telefono,
                'observaciones'    =>  $request->observaciones,

            ]);

            $response['status'] = 1;
            $response['message']= 'Provedor guardado correctamente';
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
        $provedor = Provedor::findOrFail($id);

        return response()->json($provedor->nombre);

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
        $provedor = Provedor::findOrFail($id);
        $provedor->codeAux= $request->codeAux;
        $provedor->nombre= $request->nombre;
        $provedor->direccion= $request->direccion;
        $provedor->provincia= $request->provincia;
        $provedor->municipio= $request->municipio;
        $provedor->email= $request->email;
        $provedor->telefono= $request->telefono;
        $provedor->observaciones= $request->observaciones;

        $provedor->update();

        $response['status'] = 1;
        $response['message']= 'Provedor actualizado correctamente';
        $response['code']=200;

        return response()->json($response);

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

        $provedor = Provedor::findOrFail($id);
      //  $provedor = delete();

    }

    public function provedoresMistral(){

       $provedores = DB::connection('sqlsrv')->table('ENTIDAD')
       ->join('TBL_MUNICIPIOS', 'ENTIDAD.MUNICIPIO', '=', 'TBL_MUNICIPIOS.CODIGO')
       ->join('TBL_PROVINCIAS', 'TBL_MUNICIPIOS.PROVINCIA','=', 'TBL_PROVINCIAS.CODIGO')
       ->select('ENTIDAD.CODENTIDAD', 'ENTIDAD.TIPO', 'ENTIDAD.DESCRIPCION', 'ENTIDAD.DIRECCION', 'ENTIDAD.EMAIL', 'ENTIDAD.TELEFONO', 'TBL_MUNICIPIOS.DESCRIPCION AS MUNICIPIO', 'TBL_PROVINCIAS.DESCRIPCION AS PROVINCIA')
       ->where('ENTIDAD.TIPO', '=', 1)
       ->orWhere('ENTIDAD.TIPO', '=', 2)
       ->get();

    return response()->json($provedores);

    }

    



}
