<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $post = Post::query()
                ->orderByDesc('created_at')
                ->with('user:id,name,image')
                ->withCount('comments')
                ->withCount('likes')
                ->get(['id', 'post_body', 'image', 'created_at']);
            return response()->json([
                'status' => 'success',
                'data' => $post,
                'message' => 'posts fetched successfully!',
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
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user_id = Auth::user()->id;
            $post = Post::create([
                'user_id' => $user_id,
                'post_body' => $request->post_body,
                'image' => $request->image,
            ]);
            return response()->json([
                'status' => 'success',
                'data' => $post,
                'message' => 'Post created Successfully'
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
    public function show($id): JsonResponse
    {
        try {
            $post_id =  Post::find($id);

            if ($post_id) {
                $post = Post::query()->where('id', $id)
                    ->with('user:id,name,image')
                    ->withCount('comments')
                    ->withCount('likes')
                    ->get(['id', 'post_body', 'image', 'created_at']);
                return response()->json([
                    'status' => 'success',
                    'data' => $post,
                    'message' => 'Post fetched Successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Post not found'
                ], 404);
            }
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
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {

        try {
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Post Not Found'
                ], 404);
            }
            $user_id = Auth::user()->id;
            if ($user_id != $post->user_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission deined',
                ], 403);
            }

            $post->update([
                'post_body' => $request->post_body ?? $post->post_body,
                'image' => $request->image ?? $post->image,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $post,
                'message' => 'Post Updated Successfully'
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
    public function destroy($id)
    {
        try {
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Post Not Found'
                ], 404);
            }
            $user_id = Auth::user()->id;
            if ($user_id != $post->user_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission deined',
                ], 403);
            }
            $post->delete();
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Post deleted Sucessfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
