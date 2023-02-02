<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\VerifyEmail;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
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
    $this->middleware('auth:api', ['except'=>['userSignUp', 'userLogin','userLogout','verifyEmail','resendPin','forgotPassword', 'verifyPin','resetPassword']]);
}

public function userLogin(Request $request){
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

public function userSignUp(Request $request){
    $validator = Validator::make($request-> all(),[
        'email' => 'required|string|email:rfc,filter,dns|unique:users',
        'password'=> 'required|string|min:6|confirmed'

    ]);

    if($validator-> fails()){

        return $this->sendError($validator->errors(), 'Validation Error', 422);
    }

    $user_status  = User:: where("email", $request->email)->first();

        if(!is_null($user_status)){
            return $this->sendError([], "Whoops! email already registered", 400);
        }

    $user = User:: create(array_merge(
            $validator-> validated(),
             ['password'=>bcrypt($request->password)]
            //  ['confirm_password'=>bcrypt($request->confirm_password)]


        ));

        if ($user ){
            $verify2 = DB::table('reset_code_password')->where([
                ['email',$request->all()['email']]
            ]);

          if($verify2->exists()){
            $verify2->delete();
        }

        $pin =rand(100000, 999999);
        DB::table('reset_code_password')->insert(
            [
                'email'=>$request->all()['email'],
                'code'=>$pin
            ]
    );

}

  Mail::to($request->email)->send(new VerifyEmail($pin));

    $token = $user->createToken('myapptoken')->plainTextToken;


        return $this->sendResponse(
            ['success'=>'true',
            'message'=>'User registered successfully.
             Please check your email for a 6-digit pin to verify your email.',
            'token'=>$token
        ], 201);
}



public function verifyEmail(Request $request){
    $validator = Validator::make($request->all(),[
        'code'=> '',
        'email' => 'email',
    ]);

    if($validator->fails()){
        return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);
    }

    $user = User::where('email',$request->email);
    $select = DB::table('reset_code_password')->where([
                                'email' => $request->email,
                                'code' => $request->code
                                  ]);


    if($select->get()->isEmpty()){
        return $this->sendError([
            'success'=> false, 'message' => "Invalid token"
        ], 400);
    }

    $difference = Carbon::now()->diffInSeconds($select->first()->created_at);
    if($difference > 3600){
        return $this->sendError([
            'success'=> false, 'message' => "Token Expired"
        ], 400);
    }


    $select = DB::table('reset_code_password')
    ->where('email', $request->email)
    ->where('code', $request->code)
    ->delete();

    $user->update([
        'email_verified_at'=> Carbon::now()
    ]);

    return $this->sendResponse(
        ['success' => true,
        'message'=>"Email is verified."], 201);


}


public function resendPin(Request $request){
    $validator = Validator::make($request->all(), [
        'email'=> 'required|email:rfc,filter,dns'
    ]);

    if($validator->fails()){
      return $this->sendError($validator->errors(),'Validation Error', 422);

    }

    $verify= DB::table('reset_code_password')->where([
        ['email', $request->all()['email']]
    ]);

    if($verify->exists()){
        $verify->delete();
    }


    $token= random_int(100000, 999999);
    $password_reset = DB::table('reset_code_password')->insert([
        'email' =>$request->all()['email'],
        'code'=> $token,
        'created_at'=> Carbon::now()
    ]);

    if($password_reset){
        Mail::to($request->all()['email'])->send(new VerifyEmail($token));

        return $this->sendResponse(
            ['success' => true,
            'message'=>"A verification mail has been resent."], 201);


    }

}




public function forgotPassword(Request $request){
    $validator = Validator::make($request->all(), [
        'email' => ['required', 'string', 'email', 'max:255'],
    ]);

    if ($validator->fails()) {
        return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);

    }

    $verify = User::where('email', $request->all()['email'])->exists();

    if ($verify) {
        $verify2 =  DB::table('reset_code_password')->where([
            ['email', $request->all()['email']]
        ]);

        if ($verify2->exists()) {
            $verify2->delete();
        }

        $token = random_int(100000, 999999);
        $password_reset = DB::table('reset_code_password')->insert([
            'email' => $request->all()['email'],
            'code' =>  $token,
            'created_at' => Carbon::now()
        ]);

        if ($password_reset) {
            Mail::to($request->all()['email'])->send(new ResetPassword($token));

            return $this->sendResponse(
                [
                    'success' => true,
                    'message' => "Please check your email for a 6 digit pin"
                ],
                200
            );
        }
    } else {
        return $this->sendError(
            [
                'success' => false,
                'message' => "This email does not exist"
            ],
            400
        );
    }
}



public function verifyPin(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => ['required', 'string', 'email', 'max:255'],
        'code' => ['required'],
    ]);

    if ($validator->fails()) {
        return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);
    }

    $check = DB::table('reset_code_password')->where([
        ['email', $request->all()['email']],
        ['code', $request->all()['code']],
    ]);

    if ($check->exists()) {
        $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
        if ($difference > 3600) {
            return $this->sendError(['success' => false, 'message' => "Token Expired"], 400);
        }

        $delete = DB::table('reset_code_password')->where([
            ['email', $request->all()['email']],
            ['code', $request->all()['code']],
        ])->delete();

        return $this->sendResponse(
            [
                'success' => true,
                'message' => "You can now reset your password"
            ],
            200
            );
    } else {
        return $this->sendError(
            [
                'success' => false,
                'message' => "Invalid token"
            ],
            401
        );
    }
}


public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => ['required', 'string', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
    ]);

    if ($validator->fails()) {
        return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);
    }

    $user = User::where('email',$request->email);
    $user->update([
        'password'=>bcrypt($request->password)
    ]);

    $token = $user->first()->createToken('myapptoken')->plainTextToken;

    return $this->sendResponse(
        [
            'success' => true,
            'message' => "Your password has been reset",
            'token'=>$token
        ],
        200
    );
}





public function userLogout(){
    try{

        auth()-> logout();
        return response()->json(['success'=>true,'message'=>'Logged out successfully']);
    }catch(\Exception $e){
        return response()->json(['success'=>false, 'message'=> $e->getMessage()]);

    }



}


public function createNewToken($token){
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL()* 60,
        // 'user'=>auth()->user()
        'message' => "Logged in successfully"
    ]);
}

}
