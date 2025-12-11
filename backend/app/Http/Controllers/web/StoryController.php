<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Story;

class StoryController extends Controller
{
    // List Stories
    public function listStory()
    {
        $stories = Story::all();
        $active = 'story';
        $data = compact('stories', 'active');  
        return view('story.index', $data);
    } 
    
    // Create Story
    public function createStory(Request $request)
    {
        // Validate input
        $request->validate([
            'story_title' => 'required',
            'story_description' => 'required',
        ]);

        $story = new Story();
        $story->title = $request->input('story_title');
        $story->body = $request->input('story_description');
        $story->save();

        return redirect()->route('list-story')->with([
            'status'=>'success',
            'message'=> 'Story has been created successfully.',
        ]);
    }

    // Update Story
    public function updateStory(Request $request)
    {
        // Validate input
        $request->validate([
            'story_id' => 'required',
            'story_title' => 'required',
            'story_description' => 'required',
        ]);
        
        $id = $request->story_id;
        $story = Story::find($id);
        $story->title = $request->input('story_title');
        $story->body = $request->input('story_description');
        $story->save();

        return redirect()->route('list-story')->with([
            'status'=>'success',
            'message'=> 'Story has been updated successfully.',
        ]);
    }

    // Delete Story
    public function deleteStory(Request $request)
    {
        $request->validate([
            'story_id' => 'required',
        ]);

        $story = Story::find($request->story_id);
        $story->delete();

        return redirect()->route('list-story')->with([
            'status'=>'success',
            'message'=> 'Story has been deleted successfully.',
        ]);
    }
}