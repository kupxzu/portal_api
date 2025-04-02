<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\TeacherAuthController;
use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes for authentication
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('teacher/login', [TeacherAuthController::class, 'login']);
Route::post('student/login', [StudentAuthController::class, 'login']);

// Admin protected routes (check for admin ability/permission)
Route::middleware(['auth:sanctum', 'ability:admin'])->prefix('admin')->group(function () {
    Route::post('logout', [AdminAuthController::class, 'logout']);
    
    // Teacher management routes
    Route::apiResource('teachers', TeacherController::class);
    Route::put('teachers/{id}/status', [TeacherController::class, 'updateStatus']);
    
    // Student management routes
    Route::apiResource('students', StudentController::class);
    Route::put('students/{id}/status', [StudentController::class, 'updateStatus']);
});

// Teacher protected routes
Route::middleware(['auth:sanctum', 'ability:teacher'])->prefix('teacher')->group(function () {
    Route::post('logout', [TeacherAuthController::class, 'logout']);
    Route::get('profile', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });
});

// Student protected routes
Route::middleware(['auth:sanctum', 'ability:student'])->prefix('student')->group(function () {
    Route::post('logout', [StudentAuthController::class, 'logout']);
    Route::get('profile', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });
});