<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetController;
use App\Http\Controllers\TimeSlotController;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;


Route::middleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
])->group(function () {
    // Place all API routes that need session support here
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/', [AuthController::class, 'showLogin'])->name('login.page');
    Route::post('/', [AuthController::class, 'login'])->name('login');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

//    Route::get('/meet', [MeetController::class, 'index'])->name('meet');
    Route::get('/meet/{url}', [MeetController::class, 'index'])->name('meet');

    Route::get('/join-meeting/{url}', [MeetController::class, 'joinMeeting'])->name('join-meeting');


    Route::get('/agora', function() {
        return view('agora');
    })->name('agora');
    // Other routes...


    Route::get('/current-backend-guy', [AuthController::class, 'getCurrentBackendGuy']);

    Route::post('/time-slots-add', [TimeSlotController::class, 'addTimeSlot']);
    Route::put('/time-slots/{id}', [TimeSlotController::class, 'updateTimeSlot']);
    Route::delete('/time-slots/{id}', [TimeSlotController::class, 'deleteTimeSlot']);
  
Route::post('/generate-timeslots/{backendGuy}', [TimeSlotController::class, 'generateTimeSlots'])->name('generate.timeslots');
});


