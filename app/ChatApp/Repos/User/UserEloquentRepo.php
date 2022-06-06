<?php

namespace App\ChatApp\Repos\User;

use App\ChatApp\Repos\User\Contracts\UserRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserEloquentRepo implements UserRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return User::class;
    }

    /**
     * Returns users which: 
     *      id's are not in @param array of $ids,
     *      email_verified_at is not null, @todo how to check if its data, is it even smart idea?
     *      first_name or last_name or full name matches @param $str
     * 
     * Purpose: 
     *      finding users to create group with,
     *      finding users to add to group,
     */
    public function getNetQuery(string $str, array $ids = [])
    {
        $query = $this->getModel()::query();

        $inWildCards = "%{$str}%";
        
        if($ids)
            $query->whereNotIn('id', $ids);

        $query
            ->whereNotNull('email_verified_at')
            ->where(function($query) use($inWildCards) {
                $query
                    ->where('email', "like", $inWildCards)
                    ->orWhere('first_name', "like", $inWildCards)
                    ->orWhere('last_name', "like", $inWildCards)
                    ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), "like", $inWildCards);
            });

        $query
            ->select(['id', 'first_name', 'last_name', 'email', 'thumbnail', 'image'])
            ->limit(10);

        return $query->get();
    }
}