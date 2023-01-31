<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Admin;
use App\Mail\VerifyEmail;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller


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
        $this->middleware('auth:api', ['except'=>['adminSignUp', 'adminLogin','adminLogout','adminverifyEmail','adminresendPin','adminforgotPassword', 'adminverifyPin','adminresetPassword']]);
    }


    public function adminSignUp(Request $request){
        $validator = Validator::make($request-> all(),[
            'email' => 'required|string|email:rfc,filter,dns|unique:admins',
            'password'=> 'required|string|min:6|confirmed'

        ]);

        if($validator-> fails()){

            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $user_status  = Admin:: where("email", $request->email)->first();

            if(!is_null($user_status)){
                return $this->sendError([], "Whoops! email already registered", 400);
            }

        $user = Admin:: create(array_merge(
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
              'message'=>'Admin registered successfully.
               Please check your email for a 6-digit pin to verify your email.',
              'token'=>$token
          ], 201);
}



    public function adminLogin(Request $request){
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


//         $validator = Validator::make($request->all(),
//         [
//             "username"  => "required",
//             "password"  =>  "required"
//         ]);

//         if($validator->fails()){
//             return response()->json(["status" =>"failed","validation_error" => $validator->errors()]);
//         }
// //check if email exists in db
//         $email_status  = Admin::where("email",$request->email)->first();
//         $token = $email_status->createToken('apiToken')->plainTextToken;

//         //if email exists we check password for the same email
//         if(!is_null($email_status)){
//             $password_status = Admin::where("email", $request->email)->where("password", bcrypt($request->password))->first();

//             if(!is_null($password_status)){
//                 $user   =$this->adminDetail($request->email);
//                 return response()->json(["status" =>$this->status_code, "success" => true, "message" =>"Logged in successfully", "data" => $user, "token"=>$token]);
//             }
//             else{
//                 return response()->json(["status" => "failed", "success" => false, "message" => "Incorrect password. Unable to login."]);
//             }
//         }

//         else{
//             return response()->json(["status" => "failed" ,"success" =>false,"message" => "Email doesn't exist. Unable to login."]);
//         }




    }

    public function adminverifyEmail(Request $request){
        $validator = Validator::make($request->all(),[
            'code'=> 'required',
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);
        }

        $user = Admin::where('email',$request->email);
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


    public function adminResendPin(Request $request){
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




    public function adminForgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);

        }

        $verify = Admin::where('email', $request->all()['email'])->exists();

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



    public function adminVerifyPin(Request $request)
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


    public function adminResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->sendError(['success' => false, 'message' => $validator->errors()], 422);
        }

        $user = Admin::where('email',$request->email);
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





    public function adminLogout(){
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
            'user'=>auth()->user()
        ]);
    }

    }



//     public function adminLogout(Request $request){

//         try{
//             if($request->user()->tokens()->delete()){
//                 return response()->json(["status" =>$this->status_code, "success" => true,
//                 "message" => "Logged out successfully"]);
//             }else{
//                 return response()->json(["status" =>"failed",
//                 "errors" => "an error occured while logging out"],501);
//             }
//          } catch(Exception $e){
//                 return response()->json(["status" =>"failed",
//                 "errors" => "an exceptional error occured"],501);
//             }

//     }


//     public function adminDetail($email){
//         $user   = array();
//         if($email != "") {
//             $user  = Admin::where("email", $email)->first();
//             return $user;
//         }
//     }
// }
