<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $formData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            "mobile"=>$request->mobile,
            "type"=> $request->type,
            "avatar"=>$request->avatar,
            "website"=>$request->website,
            "verify_code"=>$request->verify_code,
            "verified_at"=>$request->verified_at
        ];
  
        $formData['password'] = bcrypt($request->password);
  
        $user = User::create($formData);        
  
        return response()->json([ 
            'user' => $user, 
            'token' => $user->createToken('passportToken')->accessToken
        ], 200);
          
    }
  
    // public function login(Request $request)
    // {
    //     $credentials = [
    //         'email'    => $request->email,
    //         'password' => $request->password
    //     ];
  
    //     if (Auth::attempt($credentials)) 
    //     {
    //         $token = Auth::user()->createToken('passportToken')->accessToken;
             
    //         return response()->json([
    //             'user' => Auth::user(), 
    //             'token' => $token
    //         ], 200);
    //     }
  
    //     return response()->json([
    //         'error' => 'Unauthorised'
    //     ], 401);
  
    // }
}
