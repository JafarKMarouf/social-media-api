<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetMessageRequest $request)
    {
        $data = $request->validated();

        $chatId =   $data['chat_id'];
        $currentPage = $data['page'];
        $pageSize = $data['page_size'] ?? 15;
        $messages = ChatMessage::where('chat_id', $chatId)
            ->with('user')
            ->latest('created_at')
            ->simplePaginate(
                $pageSize,
                ['*'],
                'page',
                $currentPage
            );
        return response()->json([
            'data' => $messages->getCollection(),
            'status' => 'sucess',
            'message' => 'Okay'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        $message = ChatMessage::create($data);
        return response()->json([
            'data' => $message->load('user'),
            'status' => 'sucess',
            'message' => 'Message created Sucessfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
