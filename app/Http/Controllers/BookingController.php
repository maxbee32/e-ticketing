<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller

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
        $this->middleware('auth:api', ['except'=>['storeReservation']]);
    }

              //store booking
    public function storeReservation(Request $request){
        $validator= Validator::make($request-> all(),[
            'image' => 'nullable|dimensions:max_width=500,max_heigt=500|size=5000',
            'firstname'=> 'required|string',
            'lastname'=> 'required|string',
            'gender'=> 'required|in:Male,Female',
            'country'=> 'required|string',
            'region'=> 'required|string',
            'city'=> 'required|string',
            'phone_number'=> 'required|regex:/^(\+\d{1,3}[- ]?)?\d{10}$/|min:10',
            'reservation_date'=> 'required|date',
            'reservation_time'=>'required|date_format:H:i',
            'no_of_ticket'=>'required|numeric'

        ]);

        if($validator-> fails()){

            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        if(Carbon::now()> $request->reservation_date){
            return $this->sendError([
                'success'=> false, 'message' => "Date in the past is not allowed. Kindly select a current date"
            ], 400);
        }
        // $currentuserid = Auth::user()->id;
        //$user = DB::table('users')->where('id', $request->id)->first();
      //  $booking =
         Booking::create(array_merge(
            ['user_id' => optional(auth()->user())->id],
            $validator-> validated()
        ));

        return $this->sendResponse(
            ['success'=>'true',
            'message'=>'Reservation completed successfully.'
        ], 201);
    }
}
