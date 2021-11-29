<?php

namespace App\Http\Controllers;

use App\Events\JoinedRoomEvent;
use App\Events\MessageSentEvent;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use Throwable;

use function PHPUnit\Framework\throwException;

class ChatsController extends Controller
{
    public function checkIfUserExists(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['user' => null], 400);
        } else {
            return response()->json(['user' => $user], 200);
        }
    }
    public function createChat(Request $request)
    {
        $users = [];
        $users = $request->chatUsers;
        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = $user['id'];
        }
        $userIds[] = auth()->id();
        if (count($users) === 0) {
            return response()->json(['message' => 'no Chat was created'], 400);
        } else {
            $chat = new Chat();
            if (isset($request->chatName)) {
                $chat->chat_name = $request->chatName;
            } else {
                $chat_name = '';

                foreach ($users as $key => $user) {
                    if ($key == 0) {
                        $chat_name = $user['username'];
                    } elseif ($key < 3) {
                        $chat_name = $chat_name . ', ' . $user['username'];
                    } elseif ($key > 3) {
                        $chat_name = $chat_name . '...';
                    }
                }
                $chatDb = Chat::where('chat_name', $chat_name)->first();
                if ($chatDb) {
                    return response()->json([
                        'error' =>
                            'Es existiert bereits ein Chat mit diesen Users',
                    ]);
                } else {
                    $chat->creator = $chat->user_id = auth()->id();
                    $chat->chat_name = $chat_name;
                    $chat->isGroupChat = true;

                    $chat->save();
                    $latestChat = Chat::find($chat->id);
                    $latestChat->users()->attach($userIds);
                    return response()->json(['chat', $latestChat], 201);
                }
            }
        }
    }

    public function getChats()
    {
        $user = auth()->user();

        $chatsWithAuth = $user->chats;

        $chatWithUsers = [];

        foreach ($chatsWithAuth as $chat) {
            $chatWithUsers[] = new ChatResource($chat);
        }

        return response()->json([
            'chatsWithUsers' => $chatWithUsers,
        ]);
    }

    public function getChatById(Request $request, $id)
    {
        $chat = Chat::find($id);

        if ($chat) {
            broadcast(new JoinedRoomEvent($chat, auth()->user()))->toOthers();
            return response()->json(['chat' => new ChatResource($chat)]);
        } else {
            return response()->json(['error' => 'no Chat was found']);
        }
    }

    public function sendMessage(Request $request)
    {
        $content = $request->message;
        $chatId = $request->chatId;

        if ($content) {
            $message = new Message([
                'chat_id' => $chatId,
                'sender_id' => auth()->id(),
                'content' => $content,
            ]);
            $message->save();

            $authUser = User::find(auth()->id());
            $chat = Chat::find($chatId);

            broadcast(
                new MessageSentEvent(new MessageResource($message), $authUser)
            )->toOthers();

            // try {

            // } catch (BroadcastException $err) {
            //     return response()->json(['error' => report($err)]);
            // }

            return response()->json([
                'message' => new MessageResource($message),
            ]);
        } else {
        }
    }
    public function getMessagesByChatId(Request $request, $id)
    {
        $chat = Chat::find($id);
        $messages = $chat->messages;

        return response()->json([
            'messages' => MessageResource::collection($messages),
        ]);
    }
    // public function getPrivateChat(Request $request)
    // {
    //     $chat = Chat::find($id);
    //     $messages = $chat->messages;

    //     return response()->json([
    //         'messages' => new ChatResource($messages),
    //     ]);
    // }

    public function sendPrivateMessage(Request $request)
    {
        $chat = User::find(auth()->id())
            ->chats()
            ->where('isGroupChat', false)
            ->first();
        $user = User::where('username', $request->to)->first();
        if (!$chat) {
            $chat = new Chat();
            $chat_name = $user->username;
            $chat->creator = auth()->id();
            $chat->chat_name = $chat_name;
            $chat->save();
            $chat->users()->attach([auth()->id(), $user->id]);
            $chat->messages()->create([
                'content' => $request->message,
                'sender_id' => auth()->id(),
                'user_id' => $user->id,
            ]);
            $chat->save();
            $chat->save();
            return response()->json([
                'chat' => new ChatResource($chat),
            ]);
        } else {
            $chat->messages()->create([
                'content' => $request->message,
                'sender_id' => auth()->id(),
                'user_id' => $user->id,
            ]);

            $chat->save();
            return response()->json([
                'message' => new MessageResource($chat->messages->last()),
            ]);
        }
    }

    public function getChatWithPrivateMessages(Request $request, $username)
    {
        $chat = new ChatResource(Chat::where('chat_name', $username)->first());
        return response()->json(['chat' => $chat], 200);
    }
}
