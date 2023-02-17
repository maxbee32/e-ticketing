<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class ticket extends Model
{
    use  HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'email',
        'ticket_id',
        'firstname',
        'lastname',
        'gender',
        'country',
        'reservation_date',
        'no_of_ticket',
        'total',
        'created_at'
    ];




    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
