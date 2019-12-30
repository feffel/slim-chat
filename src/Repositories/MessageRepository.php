<?php
declare(strict_types=1);

namespace Chat\Repositories;

use Chat\Models\Conversation;
use Chat\Models\Message;
use Chat\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MessageRepository extends BaseRepository
{
    protected function getAliases(): array
    {
        return [
            Message::TABLE      => 'm',
            Conversation::TABLE => 'c',
        ];
    }

    protected function getModel(): string
    {
        return Message::class;
    }

    public function filterByConversation(Conversation $conversation): Builder
    {
        $m = $this->getAliases()[Message::TABLE];
        return Message::query()
            ->select("$m.*")
            ->from(Message::TABLE, $m)
            ->where("$m.conversation_id", '=', $conversation->id);
    }

    public function create(User $user, Conversation $conversation, string $content): Message
    {
        $message          = new Message();
        $message->content = $content;
        $message->conversation()->associate($conversation);
        $message->author()->associate($user);
        $message->save();
        return $message;
    }
}
