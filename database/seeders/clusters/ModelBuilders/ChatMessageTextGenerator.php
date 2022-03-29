<?php

namespace Database\Seeders\clusters\ModelBuilders;

use App\Models\ChatMessages;
use Faker\Factory as Faker;

use Database\Seeders\clusters\Contracts\Cluster;

class ChatMessageTextGenerator implements Cluster 
{
    public $numMessages, $messagesTexts;

    public function __construct($numMessages, $minTextLen, $maxTextLen)
    {
        $this->numMessages = $numMessages;
        $this->minTextLen = $minTextLen;  
        $this->maxTextLen = $maxTextLen;

        $this->messagesTexts = [];
        $this->faker = Faker::create();
    }

    public function build()
    {
        for($i = 0; $i < $this->numMessages; $i++){
            $this->messagesTexts[] = [
                'text' => $this->faker->realText($this->generateTextLength())
            ];
        }
        return $this->messagesTexts;
    }

    private function generateTextLength()
    {
        return rand($this->minTextLen, $this->maxTextLen);
    }

}