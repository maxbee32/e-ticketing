<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    use HasFactory;




    // public function isExpire()
    // {
    //     if($this->created_at > now()->addHour()){
    //         $this-> delete();
    //     }
    // }


    public $table = 'reset_code_password';
    public $timestamps = false;
    protected $primaryKey = 'email';


    protected $fillable = [
        'email',
        'code',
        'created_at'
    ];

}
