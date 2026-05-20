<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SystemLogController;
use Illuminate\Support\Facades\Route;

// Autenticação (pública)
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Autorizações
    Route::get('/authorizations', [AuthorizationController::class, 'index']);
    Route::post('/authorizations', [AuthorizationController::class, 'store']);
    Route::post('/authorizations/{authorization}/approve', [AuthorizationController::class, 'approveByTeacher']);
    Route::post('/authorizations/{authorization}/reject', [AuthorizationController::class, 'rejectByTeacher']);

    // Movimentações
    Route::get('/movements', [MovementController::class, 'index']);
    Route::post('/authorizations/{authorization}/register-movement', [MovementController::class, 'register']);

    // Usuários (Admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/teachers', [UserController::class, 'getTeachers']);
    Route::get('/guardians', [UserController::class, 'getGuardians']);

    // Alunos
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::put('/students/{student}', [StudentController::class, 'update']);
    Route::delete('/students/{student}', [StudentController::class, 'destroy']);
    Route::get('/students-all', [StudentController::class, 'listAll']);

    // Turmas
    Route::get('/classes', [SchoolClassController::class, 'index']);
    Route::post('/classes', [SchoolClassController::class, 'store']);
    Route::put('/classes/{class}', [SchoolClassController::class, 'update']);
    Route::delete('/classes/{class}', [SchoolClassController::class, 'destroy']);
    Route::get('/classes-all', [SchoolClassController::class, 'listAll']);

    // Logs (Admin)
    Route::get('/logs', [SystemLogController::class, 'index']);
});
