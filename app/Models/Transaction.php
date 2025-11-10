<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'seller_id',
        'price',
        'deal_method',
        'status',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'tracking_number',
        'meetup_location',
        'seller_confirmed_at',
        'shipping_confirmed_at',
        'received_confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'seller_confirmed_at' => 'datetime',
        'shipping_confirmed_at' => 'datetime',
        'received_confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopeForBuyer($query, $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    public function scopeForSeller($query, $userId)
    {
        return $query->where('seller_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'menunggu_transaksi');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['menunggu_transaksi', 'barang_dikirim', 'paket_diterima']);
    }

    // Methods
    public function canBeShipped(): bool
    {
        return $this->status === 'menunggu_transaksi' && $this->deal_method === 'shipping';
    }

    public function canBeReceived(): bool
    {
        return $this->status === 'barang_dikirim' && $this->deal_method === 'shipping';
    }

    public function canBeCompleted(): bool
    {
        if ($this->deal_method === 'meetup') {
            return $this->status === 'menunggu_transaksi';
        }
        return $this->status === 'paket_diterima';
    }

    public function markAsShipped(string $trackingNumber): void
    {
        $this->update([
            'status' => 'barang_dikirim',
            'tracking_number' => $trackingNumber,
            'shipping_confirmed_at' => now(),
        ]);
    }

    public function markAsReceived(): void
    {
        $this->update([
            'status' => 'paket_diterima',
            'received_confirmed_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'selesai',
            'completed_at' => now(),
        ]);
    }
}
