<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('school')->user();

    //dd($users);

    return view('school.home');
})->name('home');

