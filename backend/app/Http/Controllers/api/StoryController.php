<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Story;

class StoryController extends Controller
{
    public function index()
    {
        // Retrieve all stories from the database
        $stories = Story::on('mysql_slave')->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Listed the stories successfully',
            'result' => $stories
        ], 200);
    }
}