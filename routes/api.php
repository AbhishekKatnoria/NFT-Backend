<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return  $data = [

            'name'    => 'Nick Jones',
            'email'   => 'jones@gmail.com',
            'address' => 'Mohali',
    ];
});
