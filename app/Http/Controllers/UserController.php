<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    //
    public function registro(request $request){

        $user = User::where('email', $request['email'])->first();



        if($user){
            $response['status'] = 0;
            $response['message']= 'El usuario ya esta registrado';
            $response['code']=409;
        }else{

        $user = User::create([
            'name'      =>  $request->name,
            'email'     =>  $request->email,
            'password'  =>  bcrypt($request->password),
            'profile'   =>  $request->profile,
            'carnet'    =>  $request->carnet,
            'cargo'     => $request->cargo

        ]);

            $response['status'] = 1;
            $response['message']= 'Usuario Registrado correctamente';
            $response['code']=200;
        }
            return response()->json($response);

    }

    public function login(request $request){

        $credencial= $request->only('email', 'password');

        try{
            if(!JWTAuth::attempt($credencial)){
                $response['status']=0;
                $response['data']=null;
                $response['code']=401;
                $response['message']='La contraseña o el correo es incorrecto';
                return response()->json($response);
            }
        }catch(JWTException $e){
            $response['data']=null;
            $response['code']=500;
            $response['message']='No se puede crear el token o pase';
            return response()->json($response);
        }

        $user = auth()->user();

        $data['token'] = auth()->claims([
            'name'=>$user->name,
            'user_id' => $user->id,
            'email' => $user->email,
            'profile' => $user->profile,
            'carnet'  => $user->carnet,
            'cargo'   => $user->cargo

        ])->attempt($credencial);

        $response['data']=$data;
        $response['status']=1;
        $response['code']=200;
        $response['message']='Login exitoso';

        return response()->json($response);



    }

    public function showUser(){

        $usuariosRegistrados= User::select('id','name','email','created_at','profile', 'carnet', 'cargo')->get();

        return response()->json($usuariosRegistrados);

    }

    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);

        $user->name= $request->name;
        $user->email= $request->email;
        $user->password = bcrypt($request->password);
        $user->profile = $request->profile;
        $user->carnet = $request->carnet;
        $user->cargo = $request->cargo;
        $user->update();

        return $user;

    }

    public function destroy($id)
    {
        //
        $user = User::findOrFail($id);
        $user->delete();

    }

}
