<?php

namespace Chat\Validation;

use Illuminate\Support\Arr;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Class Validator
 * @method static self existsInTable()
 * @package Chat\Validation
 */
class Validator
{
    protected array $errors = [];

    /**
     * Validate an array of values and fields
     *
     * @param array                           $values
     * @param \Respect\Validation\Validator[] $rules
     *
     * @return self
     */
    public function validateArray(array $values, array $rules): self
    {
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName($field)->assert(Arr::get($values, $field, null));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }

        return $this;
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
