<?php
declare(strict_types=1);

namespace Chat\Transformers;

use Chat\Models\Conversation;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class ConversationTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [
            'participants',
            'messages',
        ];
    protected $defaultIncludes
        = [
            'participants',
        ];

    public function transform(Conversation $conversation): array
    {
        return [
            'id'        => (int)$conversation->id,
            'updatedAt' => optional($conversation->updated_at)->toIso8601String(),
        ];
    }

    public function includeParticipants(Conversation $conversation): Collection
    {
        return $this->collection($conversation->participants, new UserTransformer());
    }

    public function includeMessages(Conversation $conversation): Collection
    {
        return $this->collection(
            $conversation->messages->sortByDesc(fn($v, $k) => $v->updated_at)->take(5),
            new MessageTransformer()
        );
    }
}
