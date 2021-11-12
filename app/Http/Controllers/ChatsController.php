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
                    } else {
                        $chat_name = $chat_name . ', ' . $user['username'];
                    }
                }
            }
            $chat->chat_name = $chat_name;
            $chat->isGroupChat = true;

            $chat->save();
            $latestChat = Chat::find($chat->id);
            $latestChat->users()->attach($userIds);
        }
    }

    public function getChats()
    {
        $userId = auth()->id();

        $user = User::find($userId)->first();
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

    public function sendPrivateMessage(Request $request)
    {
        $chat = User::find(auth()->id())
            ->chats()
            ->where('isGroupChat', false)
            ->first();

        if (!$chat) {
            $chat = new Chat();
            $chat_name = User::find($request->to);

            $chat->chat_name = $chat_name->username;
            $chat->save();
            $chat->users()->attach([auth()->id(), $request->to]);
            $chat->messages()->create([
                'content' => $request->message,
                'sender_id' => auth()->id(),
                'user_id' => $request->to,
            ]);
            $chat->save();
        } else {
            $chat->messages()->create([
                'content' => $request->message,
                'sender_id' => auth()->id(),
                'user_id' => $request->to,
            ]);

            $chat->save();
        }
    }
}
