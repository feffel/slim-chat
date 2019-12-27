<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;

class CreateMessagesAndConversations extends Migration
{
    public function up()
    {
        $this->schema->create(
            'messages',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('content', 1000);
                $table->unsignedInteger('author_id');
                $table->unsignedInteger('conversation_id');
                $table->foreign('author_id')
                    ->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            }
        );
        $this->schema->create(
            'conversations',
            function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
            }
        );
        $this->schema->table(
            'messages',
            function (Blueprint $table) {
                $table->foreign('conversation_id')
                    ->references('id')->on('conversations')->onDelete('cascade');
            }
        );
        $this->schema->create(
            'conversation_participants',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('conversation_id');
                $table->unsignedInteger('user_id');
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['conversation_id', 'user_id']);
            }
        );
    }

    public function down()
    {
        $this->schema->dropIfExists('conversation_participants');
        $this->schema->dropIfExists('conversations');
        $this->schema->dropIfExists('messages');
    }
}
