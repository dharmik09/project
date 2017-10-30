<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('developer')->user();

    //dd($users);

    return view('developer.home');
})->name('home');

