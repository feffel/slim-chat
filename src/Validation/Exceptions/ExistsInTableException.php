<?php

namespace Chat\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class ExistsInTableException extends ValidationException
{

    public static $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => '{{name}} does not exist',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} already exists',
        ],
    ];
}
