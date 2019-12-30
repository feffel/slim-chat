<?php
declare(strict_types=1);

namespace Chat\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

abstract class BaseRepository
{
    abstract protected function getAliases(): array;

    abstract protected function getModel(): string;

    public function alias(string $table, string $field): string
    {
        // @TODO Proper exception
        throw_unless(Arr::has($this->getAliases(), $table), new InvalidArgumentException());
        return sprintf('%s.%s', $this->getAliases()[$table], $field);
    }

    public function find($id = null): ?Model
    {
        return $this->getModel()::query()->where('id', $id)->first();
    }
}
