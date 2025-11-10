<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'message_type',
        'offer_amount',
        'offer_status',
        'offer_counter_count',
        'is_read',
    ];

    protected $casts = [
        'offer_amount' => 'decimal:2',
        'offer_counter_count' => 'integer',
        'is_read' => 'boolean',
    ];

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }
}
