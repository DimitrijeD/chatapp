<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ChatGroup;

class CreateChatGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('model_type')->default(ChatGroup::MODEL_TYPE_DEFAULT); 
            $table->string('chatting_type')->default(ChatGroup::CHATTING_TYPE_DEFAULT); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_groups');
    }
}
