<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberMessageReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_message_id',
        'author_id',
        'body',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(MemberMessage::class, 'member_message_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
