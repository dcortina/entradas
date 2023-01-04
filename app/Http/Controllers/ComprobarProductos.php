<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComprobarProducto;
use Illuminate\Support\Facades\DB;
class ComprobarProductos extends Controller
{
    //
    public function guardarDatos(Request $request){

        $datosComprobar= ComprobarProducto::all();

        $data = $request->all();

        if($datosComprobar->isEmpty()){

            foreach($data as $item){

                $datos= new ComprobarProducto();

                if(isset($item['referencia'])){

                    $datos->referencia= $item['referencia'];
                    $datos->precioPublico= $item['precioPublico'];
                    $recargo=$item['recargo']/100;
                    $datos->recargo= $recargo;
                    if($item['conformado'] == 'Si'){
                    $datos->conformado= 1;
                    }else{
                        $datos->conformado= 0;
                    }
                    if($item['donativo']=='Si'){
                    $datos->donativo= 1;
                        }else{
                            $datos->donativo= 0;
                        }
                    $datos->save();

                    $response['status'] = 1;
                    $response['message']= 'Datos Importados Correctamente';
                    $response['code']=200;

                }else{
                    $response['status'] = 0;
                    $response['message']= 'Los datos que intenta importar son incorrectos';
                    $response['code']=200;
                }

            }

        }else{

            DB::table('comprobarproductos')->delete();

            foreach($data as $item){

                $datos= new ComprobarProducto();

                if(isset($item['referencia'])){

                    $datos->referencia= $item['referencia'];
                    $datos->precioPublico= $item['precioPublico'];
                    $recargo=$item['recargo']/100;
                    $datos->recargo= $recargo;
                    if($item['conformado'] == 'Si'){
                    $datos->conformado= 1;
                    }else{
                        $datos->conformado= 0;
                    }
                    if($item['donativo']=='Si'){
                    $datos->donativo= 1;
                        }else{
                            $datos->donativo= 0;
                        }
                    $datos->save();

                    $response['status'] = 1;
                    $response['message']= 'Datos Importados Correctamente';
                    $response['code']=200;

                }else{
                    $response['status'] = 0;
                    $response['message']= 'Los datos que intenta importar son incorrectos';
                    $response['code']=200;
                }

            }

            $response['status'] = 1;
            $response['message']= 'Datos Importados Correctamente';
            $response['code']=200;

        }

        return response()->json($response);

    }


    public function buscarDatosDiferentesPrecios(){

    

        $datosComprobarPrecio= ComprobarProducto::all();

         foreach($datosComprobarPrecio as $item){

            $productosMistral= DB::connection('sqlsrv')->table('RECAMBIO')
            ->select('RECAMBIO.REFERENCIA', 'RECAMBIO.DESCRIPCION', 'RECAMBIO.PVP')
            ->where([['RECAMBIO.REFERENCIA','=',$item->referencia],
                     ['RECAMBIO.PVP','!=',$item->precioPublico]
                    ])
            ->get();

            foreach($productosMistral as $productoMistral){


            $mostrar []= [
                'referencia'=>$productoMistral->REFERENCIA,
                'descripcion'=>$productoMistral->DESCRIPCION,
                'mistral'=>$productoMistral->PVP,
                'gespre'=>$item->precioPublico
            ];

        }

        }


        if($productosMistral->isEmpty()){

            $mostrar []= [
                'message'=> 'No Hay Valores incorrectos',
                'status'=>1,
                'code'=>200,
          
            ];
        
        }


        return $mostrar;



    }

    public function buscarDatosDiferenteMargen(){

       

        $datosComprobarPrecio= ComprobarProducto::all();

        foreach($datosComprobarPrecio as $item){

            $productosMistral= DB::connection('sqlsrv')->table('RECAMBIO')
            ->select('RECAMBIO.REFERENCIA', 'RECAMBIO.DESCRIPCION', 'RECAMBIO.RECARGO')
            ->where([['RECAMBIO.REFERENCIA','=',$item->referencia],
                     ['RECAMBIO.RECARGO','!=',$item->recargo]
                    ])
            ->get();

            foreach($productosMistral as $productoMistral){


            $mostrar []= [
                'referencia'=>$productoMistral->REFERENCIA,
                'descripcion'=>$productoMistral->DESCRIPCION,
                'mistral'=>$productoMistral->RECARGO,
                'gespre'=>$item->recargo
            ];

        }

        }


        if($productosMistral->isEmpty()){

            $mostrar []= [
                'message'=> 'No Hay Valores incorrectos',
                'status'=>1,
                'code'=>200,
          
            ];
        
        }


        return $mostrar;



    }


    public function buscarDatosDiferenteConformado(){

    

        $datosComprobarPrecio= ComprobarProducto::all();

        foreach($datosComprobarPrecio as $item){

            $productosMistral= DB::connection('sqlsrv')->table('RECAMBIO')
            ->select('RECAMBIO.REFERENCIA', 'RECAMBIO.DESCRIPCION', 'RECAMBIO.CONFORMADO')
            ->where([['RECAMBIO.REFERENCIA','=',$item->referencia],
                     ['RECAMBIO.CONFORMADO','!=',$item->conformado]
                    ])
            ->get();

            foreach($productosMistral as $productoMistral){


            $mostrar []= [
                'referencia'=>$productoMistral->REFERENCIA,
                'descripcion'=>$productoMistral->DESCRIPCION,
                'mistral'=>$productoMistral->CONFORMADO,
                'gespre'=>$item->conformado
            ];

        }

        }

        if($productosMistral->isEmpty()){

            $mostrar []= [
                'message'=> 'No Hay Valores incorrectos',
                'status'=>1,
                'code'=>200,
          
            ];
        
        }


        return $mostrar;
        



    }



    public function buscarDatosDiferenteDonativos(){

        $datosComprobarPrecio= ComprobarProducto::all();

      

        foreach($datosComprobarPrecio as $item){

            $productosMistral= DB::connection('sqlsrv')->table('RECAMBIO')
            ->select('RECAMBIO.REFERENCIA', 'RECAMBIO.DESCRIPCION', 'RECAMBIO.DONATIVO')
            ->where([['RECAMBIO.REFERENCIA','=',$item->referencia],
                     ['RECAMBIO.DONATIVO','!=',$item->donativo]
                    ])
            ->get();

            foreach($productosMistral as $productoMistral){


            $mostrar []= [
                'referencia'=>$productoMistral->REFERENCIA,
                'descripcion'=>$productoMistral->DESCRIPCION,
                'mistral'=>$productoMistral->DONATIVO,
                'gespre'=>$item->donativo
            ];

        }

        }

        if($productosMistral->isEmpty()){

            $mostrar []= [
                'message'=> 'No Hay Valores incorrectos',
                'status'=>1,
                'code'=>200,
          
            ];
        
        }


        return $mostrar;
        

        

        







    }




  

    


}
