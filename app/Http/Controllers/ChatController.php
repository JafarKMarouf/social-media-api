<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetChatRequest;
use App\Http\Requests\StoreChatRequest;
use App\Models\Chat;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetChatRequest $request)
    {
        try {
            $data = $request->validated();

            $isPrivate = 1;
            if ($request->has('is_private')) {
                $isPrivate = (int)$data['is_private'];
            }
            $chats = Chat::where('is_private', $isPrivate)
                ->hasParticipant(Auth::user()->id)
                // ->whereHas('messages')
                ->with('lastMessage.user', 'participants.user')
                ->latest('updated_at')
                ->get();
            return response()->json([
                'data' => $chats,
                'status' => 'succes',
                'message' => 'Okay',

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * create a new chat
     * @param \App\Http\Requests\StoreChatRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreChatRequest $request): JsonResponse
    {

        try {
            $data = $this->prepareStoreData($request);

            if ($data['userId'] == $data['otherUserId']) {
                return response()->json([
                    'status' => 'faild',
                    'message' => 'bad request, You can not create a chat with your own'
                ], 400);
            }
            $previusChat = $this->getPreviusChat($data['otherUserId']);

            if ($previusChat == null) {

                $chat = Chat::create($data['data']);
                $chat->participants()->createMany([
                    [
                        'user_id' => $data['userId'],
                    ],
                    [
                        'user_id' => $data['otherUserId'],
                    ]
                ]);
                $chat->refresh()->load(
                    'lastMessage.user',
                    'participants.user'
                );
                return response()->json([
                    'data' => $chat,
                    'status' => 'sucess',
                    'message' => 'Chat Created Sucessfully'
                ], 201);
            }
            return response()->json([
                'data' => $previusChat->load(
                    'lastMessage.user',
                    'participants.user'
                ),
                'status' => 'sucess',
                'message' => 'Okay'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * check if userId and otherUserId has a previus chat
     * @param int $otherUserId
     * @return mixed
     */
    private function getPreviusChat(int $otherUserId): mixed
    {
        $userId = Auth::user()->id;

        return Chat::where('is_private', 1)
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereHas('participants', function ($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })->first();
    }

    /**
     * prepare data to store a chat
     * @param \App\Http\Requests\StoreChatRequest $request
     * @return array
     */
    private function prepareStoreData(StoreChatRequest $request): array
    {
        $data = $request->validated();
        $otherUserId = (int)$data['user_id'];
        unset($data['user_id']);
        $data['created_by'] = Auth::user()->id;

        return [
            'otherUserId' => $otherUserId,
            'userId' => Auth::user()->id,
            'data' => $data,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        try {
            $chat->load('lastMessage.user', 'participants.user');
            return response()->json([
                'data' => $chat,
                'status' => 'succes',
                'message' => 'Okay',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
