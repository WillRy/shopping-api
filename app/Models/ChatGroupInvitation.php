<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatGroupInvitation extends Model
{

    use SoftDeletes;

    protected $fillable = ['total', 'expires_at', 'group_id'];
    protected $dates = ['expires_at', 'deleted_at']; // Carbon --> adiciona 00:00:00

    public function hasInvitation()
    {
        $expiresAt = $this->expires_at;
        $expiresAt->hour(23);
        $expiresAt->minute(59);
        $expiresAt->second(59);
        return $this->remaining > 0 && (!$this->expires_at or (new Carbon())->lessThanOrEqualTo($expiresAt));
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

}
