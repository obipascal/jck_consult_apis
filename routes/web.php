<?php

use App\Mail\AccountVerification;
use App\Mail\CustomerEnquiry;
use Illuminate\Support\Facades\Route;
use JCKCon\Enums\UsersPermissions;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/", function () {
	return (new CustomerEnquiry("Obi Pascal", "obitechinvent@gmail.com", "09125256272", "When is the courses starting", "Program Starting Date."))->render();
});
