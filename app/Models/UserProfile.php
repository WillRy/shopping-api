<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{

    protected $fillable = [
        'photo',
        'phone_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
