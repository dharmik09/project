<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('teenager')->user();

//    dd($users);

    return view('teenager.home');
})->name('home');

