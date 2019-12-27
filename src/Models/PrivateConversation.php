<?php
declare(strict_types=1);

namespace Chat\Models;

use Illuminate\Database\Capsule\Manager as DB;

class PrivateConversation extends Conversation
{
    /** @var string */
    protected $table = 'conversations';

    protected static function create(User $a, User $b): self
    {
        $conversation = new self();
        $conversation->save();
        $conversation->participants()->saveMany([$a, $b]);
        return $conversation;
    }

    protected static function find(User $a, User $b): ?self
    {
        $users = array_map(fn(User $user) => $user->id, [$a, $b]);
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
            ->whereIn('cp.user_id', $users)
            ->whereIn('cp2.user_id', $users)
            ->groupBy('cp.conversation_id')
            ->pluck('cp.conversation_id')
            ->first();
        return $conversationId ? self::query()->find($conversationId) : null;
    }

    public static function getByParticipants(User $a, User $b): self
    {
        return self::find($a, $b) ?? self::create($a, $b);
    }
}
