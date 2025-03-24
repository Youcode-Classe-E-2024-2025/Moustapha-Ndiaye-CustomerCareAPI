<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TicketController;

Route::prefix('api')->group(function () {
    Route::apiResource('tickets', TicketController::class);
});

