<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Repositories\Contracts\ChatInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public $chatRepo;

    public function __construct(ChatInterface $chatRepo)
    {
        $this->chatRepo = $chatRepo;
    }

    public function sendMessage()
    {
        $data = \request()->validate([
            'receiver_id' => 'required',
            'body' => 'required'
        ]);

        $sender = auth()->user();
        if (!$chat = $sender->getChatWithUser($data['receiver_id'])) {
            $chat = $this->chatRepo->create([]);
            $chat->participants()->sync([$sender->id, $data['receiver_id']]);
        }
        $chat->messageUsers()->attach($sender->id, ['body' => $data['body']]);
        return response()->success(new MessageResource($chat->messages()->latest()->first()));
    }

    public function getUserChats()
    {
        $user = auth()->user();
        $userChats = $user->chats()->with(['participants', 'messages'])->get();
        return response()->success(ChatResource::collection($userChats));
    }

    public function getChatMessage($chatId)
    {
        $chat = $this->chatRepo->find($chatId);
        $messages = $chat->messages()->withTrashed()->get();
        return MessageResource::collection($messages);
    }

    public function markChatAsRead($chatId)
    {
        $chat = $this->chatRepo->find($chatId);
        $chat->markAsReadForUser(auth()->id());

        return response()->success(new ChatResource($chat));
    }
    //TODO: implement this logic
    //FIXME: TEST
    public function deleteMessage($id)
    {
        $message = Message::query()->findOrFail($id);
        $this->authorize('delete', $message);
        $message->delete();
        return response()->success([],'Delete successfully');
    }

}
