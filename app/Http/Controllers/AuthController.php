<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //

    public function __construct(){

        $this->middleware('auth:api',['except'=>['register','login']]);
    }
    public function register(Request $request){
        $this->validate($request,[
            'username'  =>  'required|string',
            'email'     =>  'required|string|unique:users',
            'password'  =>  'required|string'   
        ]);

        try{
                $user = new User;
                $user->username = $request->input('username');
                $user->email    = $request->input('email');
                $user->password = app('hash')->make($request->input('password'));
                $user->save(); 

                return response()->json([
                            'entity' => 'user',
                            'action' => 'create',
                            'result' => 'success'

                ], 200);

        }catch(\Exception $e ){
            return response()->json([
                        'entity' => 'user',
                        'action' => 'create',
                        'result' => 'failed'
            ], 409);
        }
        
    }
    public function login(Request $request){

        $this->validate($request,[
            'email' => 'required|string',
            'password' => 'required|string'

        ]);

        $credentials = $request->only('email','password');
        if(! $token = Auth::attempt($credentials)){

            return response()->toJson([
                'message' => 'Unauthorzied'                
            ], 401);

        }
        return $this->respondWithToken($token);
    }
    public function me(){

        return response()->json([
            auth()->user()
        ]);
    }
}
