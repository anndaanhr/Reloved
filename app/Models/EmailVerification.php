<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailVerification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Generate OTP baru untuk email
     * - Hapus OTP lama untuk email yang sama (satu email hanya punya satu OTP aktif)
     * - Generate 6 digit random OTP
     * - OTP berlaku 15 menit
     */
    public static function generate(string $email): self
    {
        // Hapus OTP lama untuk email ini (prevent multiple OTP)
        self::where('email', $email)->delete();

        // Generate 6 digit OTP (100000 - 999999)
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        return self::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15), // OTP berlaku 15 menit
            'is_used' => false,
        ]);
    }

    /**
     * Cek apakah OTP masih valid
     * OTP valid jika: belum digunakan dan belum expired
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }
}

