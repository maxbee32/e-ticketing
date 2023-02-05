<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'image',
        'firstname',
        'lastname',
        'gender',
        'country',
        'region',
        'city',
        'phone_number',
        'reservation_date',
        'reservation_time',
        'no_of_ticket'

    ];

    protected $hidden =[

    ];


    public function User(){
        return $this->belongsTo('App\User');
    }
}
