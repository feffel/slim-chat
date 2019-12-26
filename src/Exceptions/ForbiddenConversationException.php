<?php
declare(strict_types=1);

namespace Chat\Exceptions;

use InvalidArgumentException;
use Throwable;

class ForbiddenConversationException extends InvalidArgumentException
{
    public CONST E_CODE = 102;

    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, self::E_CODE, $previous);
    }
}
