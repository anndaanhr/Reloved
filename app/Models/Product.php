<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'condition',
        'brand',
        'size',
        'model',
        'stock',
        'deal_method',
        'status',
        'view_count',
        'favorite_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'deal_method' => 'array',
        'view_count' => 'integer',
        'favorite_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('title') && empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isFavoriteBy(string $userId): bool
    {
        return $this->wishlists()->where('user_id', $userId)->exists();
    }

    public function primaryImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class, 'id', 'product_id')
            ->where('is_primary', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByProvince($query, $province)
    {
        return $query->whereHas('user', function ($q) use ($province) {
            $q->where('province', $province);
        });
    }

    public function scopeByLocation($query, $city, $province = null)
    {
        return $query->whereHas('user', function ($q) use ($city, $province) {
            $q->where('city', $city);
            if ($province) {
                $q->where('province', $province);
            }
        });
    }

    /**
     * Full-text search menggunakan PostgreSQL
     * Menggunakan to_tsvector untuk indexing dan plainto_tsquery untuk query
     * Fallback ke ILIKE jika full-text search tidak menemukan hasil
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereRaw("to_tsvector('indonesian', title || ' ' || COALESCE(description, '')) @@ plainto_tsquery('indonesian', ?)", [$search])
            ->orWhere('title', 'ilike', "%{$search}%")
            ->orWhere('description', 'ilike', "%{$search}%");
    }

    /**
     * Scope untuk Produk Spotlight
     * Menggunakan algoritma scoring berdasarkan:
     * - View count (40%)
     * - Favorite count (30%)
     * - Seller rating (20%)
     * - Recency (10% - produk dalam 30 hari terakhir)
     */
    public function scopeSpotlight($query, int $limit = 5)
    {
        return $query->available()
            ->with(['user', 'images'])
            ->whereHas('user', function ($q) {
                // Seller dengan rating minimal 4.0 atau minimal 3 review
                $q->where(function ($subQ) {
                    $subQ->where('rating_avg', '>=', 4.0)
                        ->orWhere('review_count', '>=', 3);
                });
            })
            ->selectRaw('products.*, 
                (
                    (COALESCE(products.view_count, 0) * 0.4) +
                    (COALESCE(products.favorite_count, 0) * 0.3) +
                    (CASE WHEN products.created_at >= CURRENT_TIMESTAMP - INTERVAL \'30 days\' THEN 10 ELSE 0 END) +
                    (CASE WHEN EXISTS (
                        SELECT 1 FROM users 
                        WHERE users.id = products.user_id 
                        AND users.rating_avg >= 4.5
                    ) THEN 20 ELSE 0 END)
                ) as spotlight_score')
            ->orderByDesc('spotlight_score')
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    /**
     * Scope untuk Barang Terbaru
     * Produk yang baru diposting dalam 7 hari terakhir, diurutkan dari terbaru
     */
    public function scopeLatestProducts($query, int $limit = 5)
    {
        return $query->available()
            ->with(['user', 'images'])
            ->where('created_at', '>=', now()->subDays(7))
            ->latest('created_at')
            ->limit($limit);
    }

    /**
     * Scope untuk rekomendasi berdasarkan kategori favorit user
     */
    public function scopeRecommendedByCategory($query, array $categoryIds, int $limit = 5)
    {
        if (empty($categoryIds)) {
            return $query->available()
                ->with(['user', 'images'])
                ->inRandomOrder()
                ->limit($limit);
        }

        return $query->available()
            ->with(['user', 'images'])
            ->whereIn('category_id', $categoryIds)
            ->orderByDesc('view_count')
            ->orderByDesc('favorite_count')
            ->limit($limit);
    }

    /**
     * Scope untuk rekomendasi berdasarkan lokasi user
     */
    public function scopeRecommendedByLocation($query, ?string $province, ?string $city, int $limit = 5)
    {
        $query = $query->available()->with(['user', 'images']);

        if ($city) {
            $query->byLocation($city, $province);
        } elseif ($province) {
            $query->byProvince($province);
        }

        return $query->orderByDesc('view_count')
            ->orderByDesc('favorite_count')
            ->limit($limit);
    }

    /**
     * Scope untuk rekomendasi umum (untuk guest atau fallback)
     * Produk dari seller terpercaya dengan performa bagus
     */
    public function scopeRecommendedGeneral($query, int $limit = 5)
    {
        return $query->available()
            ->with(['user', 'images'])
            ->whereHas('user', function ($q) {
                // Seller dengan rating minimal 3.5
                $q->where('rating_avg', '>=', 3.5)
                    ->where('review_count', '>=', 1);
            })
            ->where(function ($q) {
                // Produk dengan view_count > 0 atau favorite_count > 0
                $q->where('view_count', '>', 0)
                    ->orWhere('favorite_count', '>', 0);
            })
            ->orderByDesc('view_count')
            ->orderByDesc('favorite_count')
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function decrementStock(int $quantity = 1): void
    {
        $this->decrement('stock', $quantity);
        if ($this->stock <= 0) {
            $this->update(['status' => 'sold']);
        }
    }
}
