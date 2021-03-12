<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index(){
    	Log::debug("Usuario ".Auth::User()->name." ingresando al metodo index del ContactController");
    	$contacts = Auth()->User()->contacts;
    	if($contacts->isEmpty()){
    		Log::debug("Metodo index del ContactController - No se encontraron datos");
            return response()->json([
                'message' => 'No se encontraron datos asociados'
            ], 204);
    	}
    	Log::debug("Metodo index del ContactController - Se han encontrado ".count($contacts)." Contactos");
        return response()->json($contacts, 200);
    }

    public function store(Request $request)
    {
        Log::debug("Usuario ".Auth::User()->name." ingresando al metodo Store del ContactController");
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phoneNumber' => 'required',
        ]);
        if($validator->fails()){
            Log::debug("Metodo Store del ContactController - Data incompleta");
            return response()->json(['message' => 'Incomplete data'], 422);
        }
        $contact = Contact::create([
            'name' => $request->name,
            'phoneNumber' => $request->description,
            'user_id' => Auth::User()->id,
        ]);
        if(! $contact){
            Log::debug("Metodo Store del ContactController - Error al guardar el contacto");
            return response()->json([
                'message' => 'No se pudo realizar el proceso correctamente'
            ], 500);
        }
        Log::debug("Metodo Store del ContactController - Se han registrado con exito el contacto [".$contact->name."]");
        return response()->json([
            'message' => 'Se ha creado el contacto correctamente'
        ], 201);
            
    }

    public function show(Contact $contact){
    	return response()->json($contact, 200);
    }

    public function destroy(Contact $contact){
    	$contact->delete();
    	return response()-json(null,204);
    }
}
