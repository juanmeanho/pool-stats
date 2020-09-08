<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Block;
use Illuminate\Support\Facades\Crypt;

class AppController extends Controller
{
    //
    public function init()
    {
    	$user = Auth::user();

    	return response()->json(['user' => $user], 200);

    }
    public function login(Request $request)
    {
    	if(Auth::attempt(['email' => $request->email, 'password' => $request->password], true))
    	{
    		return response()->json(Auth::user(), 200);

    	}else{

    		return response()->json(['error' => 'No puede entrar'], 401);
    	}
    	
	}
	public function setPasswords(Request $request)
    {

		// Obtiene el objeto del Usuario Autenticado
		$user = Auth::user();
		// Obtiene el ID del Usuario Autenticado
		$id = Auth::id();	
		
		User::where('id',$id)->update([
			'user' => $request->user,
			'password_2' => ($request->password_1 == 'null') ? null : Crypt::encryptString($request->password_1), 
			'password_3' => ($request->password_2 == 'null') ? null : Crypt::encryptString($request->password_2)
		]);			
		

		return response()->json(['msg' => 'Passwords Actualizados'], 200);
    	
	}
	
	public function checkPasswords(Request $request)
    {

		// Obtiene el objeto del Usuario Autenticado
		$user = Auth::user();
		// Obtiene el ID del Usuario Autenticado
		$id = Auth::id();	
		
    	$user = User::where('id', $id)->first();
		
		if($user->password_2 != '' && $user->password_3 != '')
			return response()->json(['msg' => 'ON'], 200);
		else
			return response()->json(['msg' => 'OFF'], 200);
    	
	}
	
    public function register(Request $request)
    {
    	$user = User::where('email', $request->email)->first();

    	if(isset($user->id))
    	{
    		return response()->json(['error' => 'Usuario ya existe'], 400);
    	}

    	$user = new User();

    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);
    	$user->name = $request->name;
    	$user->save();

    	Auth::login($user);

    	return response()->json($user, 200);
    	
	}
	
	public function getDbBlocks(){

        return ['blocks' => Block::orderBy('height', 'DESC')->limit(12)->get()];
    
	}
	
    public function logout()
    {
    	Auth::logout();
    }
}
