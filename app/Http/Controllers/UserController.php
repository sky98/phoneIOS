<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request){

        Log::debug("Metodo Store del UserController");
    	$validator = Validator::make($request->all(), [
    		'name' => 'required',
    		'password' => 'required',
    		'password2' => 'required',
    		'email' => 'required|email',
    		'device_name' => 'required'
    	]);

        if($validator->fails()){
            Log::debug("Metodo Store del UserController - Datos incompletos ".$validator->fails());
            return response()->json(['message' => 'Incomplete data'], 422);
        }

    	if($request->password != $request->password2){
    		Log::debug("Metodo Store del UserController - Las contraseñas no coinciden");
    		return response()->json(['message' => 'Las contraseñas no coinciden'], 422);
    	}    	
    	$user = User::where('email', $request->email)
    				->first();

    	if(! $user){
    		$user = User::create([
	            'name' => $request->name,
	            'password' => bcrypt($request->password),
	            'email' => $request->email,
        	]);
            Log::debug("Metodo Store del UserController - Usuario creado con exito [".$user->name."]");
            return response()->json([
                'message' => 'Usuario creado con exito',
                'token' => $user->createToken($request->device_name)->plainTextToken,
                'name' => $user->name,
                'id' => $user->id
            ],201);
    	}
        Log::debug("Metodo Store del UserController - Usuario debe cambiar las credenciales");
    	return response()->json([
        		'message' => 'Error en las credenciales'
        	],400);
    }

    public function update(Request $request, User $user){
        Log::debug("Metodo Update del UserController");
        $validator = Validator::make($request->all(), [            
            'passwordOld' => 'required',
            'passwordNew' => 'required',
            'passwordNew2' => 'required',
        ]);
        if($validator->fails()){
            Log::debug("Metodo Update del UserController - Datos incompletos");
            return response()->json(['message' => 'Incomplete data'], 422);
        }
        if($request->passwordNew != $request->passwordNew2){
            Log::debug("Metodo Update del UserController - Las nuevas contraseñas no coinciden");
            return response()->json(['message' => 'Las nuevas contraseñas no coinciden'], 422);
        }
        
        if(Hash::check($request->passwordOld, $user->password)){
            $user->password = bcrypt($request->passwordNew);
            $user->save();
            Log::debug("Metodo Update del UserController - La contraseña se ha actualizado correctamente");
            return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
        }
        Log::debug("Metodo Update del UserController - La contraseña no coincide");
        return response()->json(['message' => 'Contraseña no coincide'], 422);
    }

    public function destroy(){
        Log::debug("Metodo Destroy del UserController");
        $user = Auth::User();
        $user->tokens()->delete();
        

        $contacts = Auth()->User()->contacts;

        foreach ($contacts as $contact) {
            if ($user->id == $contact->user_id){
                $contact->delete();
            }
        }
        $user->delete();
        return response()->json([
            'message' => 'Usuario eliminado del sistema'
        ],204);
    }
}
