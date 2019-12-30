<?php
declare(strict_types=1);

namespace Chat\Transformers;

use Chat\Models\Message;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [
            'author',
        ];
    protected $defaultIncludes
        = [
            'author',
        ];

    public function transform(Message $message): array
    {
        return [
            'id'        => (int)$message->id,
            'content'   => $message->content,
            'createdAt' => optional($message->created_at)->toIso8601String(),
        ];
    }

    public function includeAuthor(Message $message): Item
    {
        return $this->item($message->author, new UserTransformer());
    }
}
