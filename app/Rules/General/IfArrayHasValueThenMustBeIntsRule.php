<?php

namespace App\Rules\General;

use Illuminate\Contracts\Validation\Rule;

class IfArrayHasValueThenMustBeIntsRule implements Rule
{
    protected $i_have_ids;

    public function __construct($i_have_ids)
    {
        $this->i_have_ids = $i_have_ids; 
        $this->failedOn = null;
    }

    public function passes($attribute, $value)
    {
        if(!is_array($this->i_have_ids))
            return false;
        
        if(empty($this->i_have_ids))
            return true;

        foreach($this->i_have_ids as $id){
            if(!is_numeric($id)){ 
                $this->failedOn = $id;
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        $msg = __("User ID-s provided in array must be numeric");
        if($this->failedOn)
            $msg .= __(", '{$this->failedOn}' given."); 

        return $msg;
    }
}
