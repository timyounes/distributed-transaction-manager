<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

// with api prefix = transactions e.g /transactions/start
Route::post('/start', [TransactionController::class, 'startTransaction']);
Route::post('/commit/{transactionId}', [TransactionController::class, 'commitTransaction']);
Route::post('/rollback/{transactionId}', [TransactionController::class, 'rollbackTransaction']);

Route::fallback(function(){
    return response()->json(['message' => 'Route Not Found.'], 404);
});
