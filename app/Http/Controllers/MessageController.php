<?php

namespace App\Http\Controllers;

use App\Events\NewMessageSent;
use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get Messages for specific chat
     * @param \App\Http\Requests\GetMessageRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(GetMessageRequest $request): JsonResponse
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
     * Store message in chat room
     * @param \App\Http\Requests\StoreMessageRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        $message = ChatMessage::create($data);
        $this->sentNotificationToOther($message->load('user'));
        return response()->json([
            'data' => $message->load('user'),
            'status' => 'sucess',
            'message' => 'Message created Sucessfully'
        ], 201);
    }

    private function sentNotificationToOther(ChatMessage $chatMessage)
    {
        $chatId = $chatMessage->chat_id;
        broadcast(new NewMessageSent($chatMessage))->toOthers();
    }
}
