<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\StoryController;

Route::get('/stories', [StoryController::class, 'index']);