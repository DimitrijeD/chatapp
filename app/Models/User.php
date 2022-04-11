<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\AccountVerification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        'thumbnail',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->belongsToMany(ChatGroup::class, 'group_participants', 'user_id', 'group_id')
            ->withPivot(['last_message_seen_id', 'group_id', 'updated_at']);
    }

    public function account_verification()
    {
        return $this->hasOne(AccountVerification::class);
    }

    // public function group($group_id){
    //     return $this->belongsToMany(ChatGroup::class, 'group_participants')
    //         ->wherePivot('group_id', $group_id);
    // }
}