<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TicketController;


Route::apiResource('tickets', TicketController::class);


