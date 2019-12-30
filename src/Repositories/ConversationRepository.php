<?php
declare(strict_types=1);

namespace Chat\Repositories;

use Chat\Models\Conversation;
use Chat\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Builder;

class ConversationRepository extends BaseRepository
{
    protected function getAliases(): array
    {
        return [
            Conversation::TABLE         => 'c',
            'conversation_participants' => 'cp',
        ];
    }

    protected function getModel(): string
    {
        return Conversation::class;
    }

    public function filterByParticipant(User $user): Builder
    {
        $c  = $this->getAliases()[Conversation::TABLE];
        $cp = $this->getAliases()['conversation_participants'];
        return Conversation::query()
            ->select('c.*')
            ->from(Conversation::TABLE, $c)
            ->join("conversation_participants AS $cp", "$cp.conversation_id", '=', "$c.id")
            ->where("$cp.user_id", '=', $user->id);
    }

    public function create(User $a, User $b): Conversation
    {
        $conversation = new Conversation();
        $conversation->save();
        $conversation->participants()->saveMany([$a, $b]);
        return $conversation;
    }

    public function get(User $a, User $b): ?Conversation
    {
        $privateConversations = DB::table('conversation_participants', 'cp')
            ->select('cp.conversation_id')
            ->groupBy('cp.conversation_id')
            ->havingRaw('COUNT(cp.conversation_id) = 2')
            ->pluck('cp.conversation_id');
        $conversationId = DB::table('conversation_participants', 'cp')
            ->select('cp.conversation_id')
            ->join('conversation_participants AS cp2', 'cp.conversation_id', '=', 'cp2.conversation_id')
            ->where('cp.id', '!=', 'cp2.id')
            ->whereIn('cp.conversation_id', $privateConversations)
            ->where('cp.user_id', $a->id)
            ->where('cp2.user_id', $b->id)
            ->groupBy('cp.conversation_id')
            ->pluck('cp.conversation_id')
            ->first();
        return $this->find($conversationId);
    }

    public function getOrCreate(User $a, User $b): Conversation
    {
        return $this->get($a, $b) ?? $this->create($a, $b);
    }
}
