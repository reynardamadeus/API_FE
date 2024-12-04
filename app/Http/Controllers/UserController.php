<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request){
        try{

        $validation = Validator::make( $request->all(), [
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if($validation->fails()){
            $errors = array();

            foreach($validation->errors()->getMessages() as $key => $values)
            {
                array_push($errors, $values[0]);
            }

            return response()->json([
                'status' => 400,
                'data' => [
                    'message' => $errors
                ]
                ], 400);
        }


        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'status' => 400,
                'data' => [
                    'message' => 'The email or password is invalid']
            ]);
        }

        $token = $user->createToken($user->email)->plainTextToken;
        return response()->json([
            'status' => 200,
            'data' => [
                'message' => 'Login successful',
                'email' => $request->email,
                'token' => $token
            ]
        ], 200);

        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'data' => [
                    'message' => $e->getMessage()]
            ], 500);
        }
    }
}
