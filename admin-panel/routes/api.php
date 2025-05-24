<?php
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AgoraTokenController;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;

use App\Http\Controllers\AuthController;

Route::middleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
])->group(function () {
    Route::get('/time-slots', [TimeSlotController::class, 'index']);
    Route::post('/schedule', [AssessmentController::class, 'store']);
    Route::post('/cancel-assessment', [AssessmentController::class, 'cancelAssessment']);

    Route::get('/available-slots', [TimeSlotController::class, 'getAvailableSlots']);
    Route::post('/reschedule', [TimeSlotController::class, 'reschedule']);

    Route::post('/generate-token', [AgoraTokenController::class, 'generateToken']);
    Route::get('/get-url/{username}', [AgoraTokenController::class, 'getUrl']);
    Route::post('/create-url', [AgoraTokenController::class, 'createUrl']);

    Route::get('/user-info/{id}', [AuthController::class, 'getUserInfo']);

    Route::get('/get-assessment/{username}', [AssessmentController::class, 'getAssessment']);
    Route::get(uri: '/assessment-requests/{assessmentRequestId}', action: [AssessmentController::class, 'getAssessmentRequest']);

    Route::get('/assessments/month', [AssessmentController::class, 'getAssessmentsForMonth']);
    Route::get('/assessments/date/{date}', [AssessmentController::class, 'getAssessmentsForDate']);
    Route::get('/assessments/pending', [AssessmentController::class, 'getPendingAssessments']);

    Route::get('/time-slots/date/{date}', [TimeSlotController::class, 'getTimeSlotsForDate']);
    Route::get('/time-slots/{id}', [TimeSlotController::class, 'getTimeSlotDetails']);

    Route::post('/app-login', [AuthController::class, 'appLogin']);
    Route::post('/save-rating', [AssessmentController::class, 'saveRating']);

    Route::get('/get-result/{username}', [AgoraTokenController::class, 'getResult']);

    Route::post('/changestatus', [AssessmentController::class, 'changeStatus']);
});

