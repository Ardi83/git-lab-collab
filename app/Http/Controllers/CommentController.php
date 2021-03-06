<?php

namespace App\Http\Controllers;

use App\Comment;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $comment = new Comment;

        $comment->content = $request->input('content');
        $comment->movie_tmdb_id = $request->input('movie_tmdb_id');
        $comment->user_id = $request->user()->id;
        $comment->review_id = $request->input('review_id');
        if ($request->user()->role_id === 1 || $request->user()->role_id === 3) {
            $comment->approved = 1;
        }
        
        $comment->user_name = User::where('id', $comment->user_id)->first()->name;

        $comment->save();
        
        Cache::forget('comments' . $comment->movie_tmdb_id);
        Cache::forget('comments' . $comment->user_id);
        Cache::forget('approved_comments' . $comment->movie_tmdb_id);
        Cache::forget('approved_comments' . $comment->user_id);
        
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $user_id = $request->user()->id;
        $comment_id = $request->comment_id;
        $content = $request->input('content');
        $updatedComment = Comment::where('id', $comment_id)->first();
        $updatedComment->content = $content;
    
        $updatedComment->save();

        Cache::forget('comments' . $comment->movie_tmdb_id);
        Cache::forget('comments' . $user_id);
        Cache::forget('approved_comments' . $comment->movie_tmdb_id);
        Cache::forget('approved_comments' . $user_id);
        
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment, Request $request)
    {
        $user_id = $request->user()->id;
        $comment_id = $comment->id;

        $toDelete = Comment::where(
            'id',
            $comment_id
        )->where(
            'user_id',
            $user_id
        )->delete();

        Cache::forget('comments' . $comment->movie_tmdb_id);
        Cache::forget('comments' . $user_id);
        Cache::forget('approved_comments' . $comment->movie_tmdb_id);
        Cache::forget('approved_comments' . $user_id);
    }
}
