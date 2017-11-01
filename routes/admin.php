<?php

// Route::get('/home', function () {
//     $users[] = Auth::user();
//     $users[] = Auth::guard()->user();
//     $users[] = Auth::guard('admin')->user();

//     //dd($users);

//     return view('home');
// })->name('admin.home');

Route::get('/home', 'Admin\DashboardController@index')->name('home');