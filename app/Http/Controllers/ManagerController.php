<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller
{
    public function sendResponse($data, $message, $status = 200){
        $response =[
            'data' => $data,
            'message' => $message
        ];
        return response()->json($response, $status);
     }


     public function sendError($errorData, $message, $status =500){
        $response =[];
        $response['message'] = $message;
        if (!empty($errorData)) {
            $response['data'] = $errorData;
     }
     return response()->json($response, $status);
    }

    public function __construct(){
        $this->middleware('auth:api', ['except'=>['managerLogin']]);
    }

    public function managerLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email:rfc,filter,dns',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(),'Validation Error', 422);

        }

        if(!$token = auth()->attempt($validator->validated())){
            return $this->sendError([], "Invalid login credentials", 400);
        }

         return $this-> createNewToken($token);
    }


    public function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL()* 60,
            'user'=>auth()->user()
        ]);
    }


}
