<?php

namespace Database\Seeders\clusters\Init;

use Database\Seeders\ChatGroupClusterSeeder;

class TextLen 
{
    public function __construct($min = 0, $max = 0)
    {
        $this->min = $min; 
        $this->max = $max;
    }

    public function getMin()
    {
        if($this->min) return $this->min;

        return $this->useDefaultMin();
    }

    public function getMax()
    {
        if($this->max) return $this->max;

        return $this->useDefaultMax();
    }

    private function useDefaultMin()
    {
        return ChatGroupClusterSeeder::MIN_TEXT_LEN;
    }

    private function useDefaultMax()
    {
        return ChatGroupClusterSeeder::MAX_TEXT_LEN;
    }
}