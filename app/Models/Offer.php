<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'conversation_id',
        'product_id',
        'buyer_id',
        'seller_id',
        'amount',
        'status',
        'counter_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'counter_count' => 'integer',
    ];

    const MAX_COUNTER_COUNT = 5;

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function canCounter(): bool
    {
        return $this->counter_count < self::MAX_COUNTER_COUNT;
    }

    public function incrementCounter(): void
    {
        $this->increment('counter_count');
    }
}
