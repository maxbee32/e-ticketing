<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\EticketPaid;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Notifications\Notification;

class TicketController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api', ['except'=>['eticketNotify']]);
    }



    public function eticketNotify(){

        //$user = User::first();


        $billData = [
            'name' => '#007 Bill',
            'body' => 'You have received a new bill.',
            'thanks' => 'Thank you',
            'text' => '$600',
            'offer' => url('/'),
            'bill_id' => 30061
        ];

        // Notification::send($user, new EticketPaid(
        //      $ticketId,
        //  $firstName,
        //  $LstName,
        //  $NumberOfTicket,
        //  $Image,
        //  $total,
        //  $Country,
        //  $reservationDate,

    // ));
    }
}

