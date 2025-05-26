<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\TestController;
use App\Http\Controllers\TestAssignmentController;
use App\Http\Controllers\CompletedTestController;
use App\Http\Controllers\GradeController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Tests
    Route::apiResource('tests', TestController::class);
    Route::get('/public-tests', [TestController::class, 'publicIndex']);
    Route::get('/tests/{test}/completed-students', [CompletedTestController::class, 'getCompletedStudents']);
    Route::get('/tests/{test}/students/{user}/answers', [CompletedTestController::class, 'getStudentAnswers']);
    Route::post('/tests/{test}/students/{user}/grade', [CompletedTestController::class, 'saveGrade']);
    Route::get('/tests/{test}/grades', [CompletedTestController::class, 'getTestGrades']);
    
    // Test assignments
    Route::post('tests/{test}/assign', [TestAssignmentController::class, 'assign']);
    Route::post('tests/{test}/enroll', [TestAssignmentController::class, 'enroll']);

    // Test taking
    Route::get('/tests/{test_id}/questions', [TestController::class, 'getQuestions']);
    Route::post('/tests/{test_id}/submit', [TestController::class, 'submitAnswers']);

    //Results
    Route::get('/student/completed-tests', [GradeController::class, 'getCompletedTestsWithGrades']);
});
