<?php

use App\Http\Controllers\AccountSettlementController;
use App\Http\Controllers\AccountSummaryController;
use Illuminate\Support\Facades\Route;

Route::get('accounts/{account}/summary', AccountSummaryController::class)
    ->name('accounts.summary');
Route::post('accounts/{account}/settle', AccountSettlementController::class)
    ->name('accounts.settle');
