<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\RecoverPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){
    	Log::debug("Metodo Login del LoginController");
    	$validator = Validator::make($request->all(), [
    		'email' => 'required|email',
    		'password' => 'required',
            'device_name' => 'required'
    	]);
    	if($validator->fails()){
            Log::debug("Metodo Login del LoginController - Datos incompletos");
    		return response()->json(['message' => 'Datos incompletos'], 422);
    	}

    	$user = User::where('email', $request->email)->first();
    	if(! $user || ! Hash::check($request->password, $user->password)){    
            Log::debug("Metodo Login del LoginController - Credenciales no validas");		
    		return response()->json(['message' => 'The given data was invalid.', 
	    			'errors' => 
	    				[
	    					'message' => 'The provided credentials are incorrect.'
	    				]
	    			]
	    			,401);
    	}
    	Log::debug("Metodo Login del LoginController - Usuario [". $user->name ."]logeado con exito");
    	return response()->json([
            'id' => $user->id
            'name' => $user->name,            
            'token' => $user->createToken($request->device_name)->plainTextToken
        ],200);  
    }

    public function recovery(Request $request){
        Log::debug("Metodo recovery del LoginController");
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            Log::debug("Metodo recovery del LoginController - Datos incompletos");
            return response()->json(['message' => 'Datos incompletos'], 422);
        }
        $user = User::where('email', $request->email)->first();
        if(! $user){
            Log::debug("Metodo recovery del LoginController - Usuario no encontrado");
            return response()->json(['message' => 'The given data was invalid.', 
                    'errors' => 
                        [
                            'message' => 'User not found.'
                        ]
                    ]
                    ,401);
        }
        Log::debug("antes del enviar el email");
        $pass = "T3MP0R4L";
        $user->password = bcrypt($pass);
        $user->save();
       
        Mail::to($user->email)->send(new RecoverPasswordMail($user, $pass));
        return response()->json([
            'message' => 'Restablecimiento de contraseña OK'
        ], 200);
    }
}
