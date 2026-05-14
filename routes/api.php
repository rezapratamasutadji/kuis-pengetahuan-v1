<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::prefix('quiz')->group(function (): void {
    Route::get('/participants', [QuizController::class, 'participants']);
    Route::get('/categories', [QuizController::class, 'categories']);
    Route::get('/categories/{category}', [QuizController::class, 'category']);
    Route::get('/categories/{category}/questions/{number}', [QuizController::class, 'question'])
        ->whereNumber('number');
});
