<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration
{
    public function up()
    {
        $this->schema->create(
            'users',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('username')->unique();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        $this->schema->dropIfExists('users');
    }
}
