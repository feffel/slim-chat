<?php
declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    protected Builder $schema;

    protected function init(): void
    {
        $this->schema = Capsule::schema();
    }
}
