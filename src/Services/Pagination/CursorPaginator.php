<?php
declare(strict_types=1);

namespace Chat\Services\Pagination;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Pagination\CursorInterface;
use Slim\Http\Request;

class CursorPaginator
{
    protected string $type;
    protected string $operator;
    protected string $field;

    public function __construct(bool $goDown = null, string $cursorField = null, string $cursorType = null)
    {
        $goDown      = $goDown ?? false;
        $cursorField = $cursorField ?? 'id';
        $cursorType  = $cursorType ?? 'int';
        // @TODO Proper exception
        throw_unless(in_array($cursorType, ['int', 'string']), new InvalidArgumentException());
        $this->operator = $goDown ? '>' : '<';
        $this->field    = $cursorField;
        $this->type     = $cursorType;
    }

    protected const CURSOR_PARAMETER   = 'cursor';
    protected const LIMIT_PARAMETER    = 'limit';
    protected const PREVIOUS_PARAMETER = 'prev';
    protected const DEFAULTS
                                       = [
            self::CURSOR_PARAMETER   => null,
            self::PREVIOUS_PARAMETER => null,
            self::LIMIT_PARAMETER    => 10,
        ];

    public function paginate(Request $request, Builder $builder): CursorInterface
    {
        $fetch     = fn($param) => $this->cast($request->getParam($param, self::DEFAULTS[$param]));
        $cursor    = $fetch(self::CURSOR_PARAMETER);
        $previous  = $fetch(self::PREVIOUS_PARAMETER);
        $limit     = $fetch(self::LIMIT_PARAMETER);
        $newCursor = $this->applyCursor($builder, $cursor, $limit);
        return new Cursor($cursor, $previous, $newCursor, min($builder->count($this->field), $limit));
    }

    protected function applyCursor(Builder $builder, $cursor, $limit)
    {
        if ($cursor) {
            $builder->where($this->field, $this->operator, $cursor);
        }
        $builder->take($limit);
        return $this->cast($builder->pluck($this->field)->last());
    }

    protected function cast($value)
    {
        if ($value !== null) {
            settype($value, $this->type);
        }
        return $value;
    }
}
