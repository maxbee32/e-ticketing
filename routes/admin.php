<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route:: post("admin-signup","App\Http\Controllers\AdminController@adminSignUp");

Route::post("admin-login", "App\Http\Controllers\AdminController@adminLogin");

Route::get("admin/{email}", "App\Http\Controllers\AdminController@adminDetail");
