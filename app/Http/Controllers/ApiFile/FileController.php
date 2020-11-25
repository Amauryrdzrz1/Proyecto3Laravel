<?php

namespace App\Http\Controllers\ApiFile;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function guardarArchivo(Request $request){
        //sacar user con token
        $id=Auth::id();
        
        $user = new User();
        $user = User::find($id);

        $rutaArchivo = 'default';        
        if($user->email != null){
            $rutaArchivo = $user->email;
        }
        
        if($request->hasFile('archivo')){
            $path = Storage::disk('public')->put($rutaArchivo, $request->archivo);
            return response()->json(["path"=>$path],201);
        }

        return response()->json(null,400);

    }
}
