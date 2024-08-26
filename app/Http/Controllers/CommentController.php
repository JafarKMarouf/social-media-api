<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($post_id)
    {
        try {
            $post = Post::find($post_id);
            if (!$post) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'post not found'
                ], 404);
            }
            $comments = $post->comments()->with('user:id,name,image')->get();
            return response()->json([
                'status' => 'success',
                'data' => $comments,
                'message' => 'Comments fetched Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, $post_id)
    {
        try {
            $post = Post::find($post_id);
            if (!$post) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'post not found'
                ], 404);
            }
            $user_id = Auth::user()->id;
            $comment = Comment::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'comment_body' => $request->comment_body,
            ]);
            return response()->json([
                'status' => 'success',
                'data' => $comment,
                'message' => 'Comments fetched Successfully'

            ], 201);
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
    public function show($comment_id)
    {
        try {
            $comment = Comment::find($comment_id);
            if (!$comment) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'comment not found'
                ], 404);
            }
            $comment = Comment::query()->where('id', $comment_id)
                ->with('user:id,name,image')
                ->get(['id', 'user_id', 'comment_body', 'created_at']);
            return response()->json([
                'status' => 'success',
                'data' => $comment,
                'message' => 'Comment fetched Successfully'

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, $comment_id)
    {
        try {
            $comment = Comment::find($comment_id);
            if (!$comment) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'comment not found'
                ], 404);
            }
            $user_id = Auth::user()->id;
            if ($comment->user_id != $user_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied.'
                ], 403);
            }
            $comment->update([
                'comment_body' => $request->comment_body ?? $comment->comment_body,
            ]);
            return response()->json([
                'status' => 'success',
                'data' => $comment,
                'message' => 'Comments updated Successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($comment_id)
    {
        try {
            $comment = Comment::find($comment_id);
            if (!$comment) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'comment not found'
                ], 404);
            }
            $user_id = Auth::user()->id;
            if ($comment->user_id != $user_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied.'
                ], 403);
            }
            $comment->delete();
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Comments deleted Successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
