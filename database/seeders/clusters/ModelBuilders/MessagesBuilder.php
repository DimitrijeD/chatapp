<?php

namespace Database\Seeders\clusters\ModelBuilders;

use App\Models\ChatMessage;

class MessagesBuilder 
{
    public $assembledMessageModels, $chat_group_id;

    public function __construct($chat_group_id)
    {
        $this->assembledMessageModels = [];
        $this->chat_group_id = $chat_group_id;
    }

    /**
     * Populate message models with given data
     */
    public function fillAssembledMessageModels(array $data, array $properties)
    {
        if(count($this->assembledMessageModels) != count($data)){
            dd('Number of message models does not match number of time cluster dates. Exit!');
        }

        for($i = 0; $i < count($this->assembledMessageModels); $i++){
            foreach($properties as $property){
                $this->assembledMessageModels[$i][$property] = $data[$i][$property];
            }
        }
        return $this->assembledMessageModels;
    }

    /**
     * Create array of message properties for each user cluster block.
     */
    public function assembleMessageModels(array $clusteredMessages)
    {
        foreach($clusteredMessages as $cluster){
            for($i = 0; $i < $cluster['clusterSize']; $i++){           
                $this->assembledMessageModels[] = [
                    'chat_group_id' => $this->chat_group_id,
                    'user_id' => $cluster['user']->id,
                    'text' => null,
                    'updated_at' => null,
                    'created_at' => null,
                ]; 
            }
        }
        return $this->assembledMessageModels;
    }

    public function bulkCreateModels()
    {
        $savingStatus = ChatMessage::insert($this->assembledMessageModels);
        if(!$savingStatus){
            dd('Saving message models failed unexpectedly!');
        }
    }

    public function getChatGroupMessages()
    {
        return ChatMessage::where('chat_group_id', $this->chat_group_id)->get();
    }
}