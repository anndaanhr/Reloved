<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'seller_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
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

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function getLatestMessageAttribute()
    {
        if (!$this->relationLoaded('messages')) {
            return $this->messages()->latest()->first();
        }
        
        return $this->messages->sortByDesc('created_at')->first();
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)
              ->orWhere('seller_id', $userId);
        });
    }

    public function scopeWithProduct($query)
    {
        return $query->whereNotNull('product_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function updateLastMessageAt(): void
    {
        $this->update(['last_message_at' => now()]);
    }
}
