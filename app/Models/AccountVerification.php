<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountVerification extends Model
{
    use HasFactory;

    const EMAIL_TYPE = 'EMAIL_VERIFICATION';
    const EMAIL_HASH_LENGTH = 64;

    const TYPES = [
        self::EMAIL_TYPE,
    ];

    protected $table = 'account_verifications';

    protected $fillable = [
        'code',
        'type',
        'user_id',
        'num_of_attempts',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
