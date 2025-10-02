<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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


Route::get('/profile/{Nama}/{NPM}/{Kelas}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
Route::post('/profile/upload', [App\Http\Controllers\ProfileController::class, 'upload'])->name('profile.upload');

// Direct route to Ananda's profile
Route::get('/profile', function () {
    return redirect()->route('profile', [
        'Nama' => 'Ananda Anhar Subing',
        'NPM' => '2317051082', 
        'Kelas' => 'A'
    ]);
})->name('my.profile');

Route::get('/', function () {
    return view(view: 'welcome');
});

Route::get('/user', [UserController::class, 'index']);
Route::get('/user/table', [UserController::class, 'table'])->name('user.table');
Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
Route::post('/user', [UserController::class, 'store'])->name('user.store');