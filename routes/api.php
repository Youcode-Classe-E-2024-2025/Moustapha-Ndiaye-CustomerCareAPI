<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketHistoryController;


Route::apiResource('tickets', TicketController::class);

Route::get('/tickets/{id}/history', [TicketHistoryController::class, 'history']);

