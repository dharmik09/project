<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('sponsor')->user();

    //dd($users);

    return view('sponsor.home');
})->name('home');

