<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function likeOrUnLike($post_id)
    {
        try {
            $post = Post::find($post_id);
            if (!$post) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Post Not Found!',
                ], 404);
            }
            $user_id = Auth::user()->id;
            $like = $post->likes()->where('user_id', $user_id)->first();
            if (!$like) {
                Like::create([
                    'post_id' => $post_id,
                    'user_id' => $user_id
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Liked'
                ], 200);
            }
            $like->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'unliked'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Like $like) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Like $like) {}
}
