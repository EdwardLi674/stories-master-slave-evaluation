<?php

use Illuminate\Support\Facades\Route;
use App\Models\Story;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\web\StoryController;
use App\Http\Controllers\web\DashboardController;
use App\Http\Controllers\web\ReplicationController;

Route::get('/', function () {
    return redirect()->route('list-story');
});

Route::controller(StoryController::class)->group(function () {
    Route::get('/story/list', 'listStory')->name('list-story');
    Route::post('/story/create', 'createStory')->name('create-story');
    Route::post('/story/update', 'updateStory')->name('update-story');
    Route::post('/story/delete', 'deleteStory')->name('delete-story');
});

Route::controller(ReplicationController::class)->group(function () {
    Route::get('/replication', 'index')->name('replication');
    Route::post('/replicate/table', 'startReplication')->name('replicate-table');
    Route::get('/replication/progress', 'getProgress')->name('replication-progress');
});