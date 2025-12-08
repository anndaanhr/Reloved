<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function create(string $userId, string $type, string $title, string $message, $notifiable = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'is_read' => false,
        ]);
    }

    public function notifyNewMessage(User $user, $conversation): void
    {
        $senderName = $conversation->buyer_id === $user->id 
            ? $conversation->seller->name 
            : $conversation->buyer->name;
        
        $this->create(
            $user->id,
            'chat',
            'Pesan Baru',
            "Anda mendapat pesan baru dari {$senderName}",
            $conversation
        );
    }

    public function notifyNewOffer(User $user, $offer): void
    {
        $this->create(
            $user->id,
            'offer',
            'Tawaran Baru',
            "Anda mendapat tawaran baru sebesar Rp " . number_format($offer->amount, 0, ',', '.'),
            $offer
        );
    }

    public function notifyOfferAccepted(User $user, $offer): void
    {
        $this->create(
            $user->id,
            'offer',
            'Tawaran Diterima',
            "Tawaran Anda sebesar Rp " . number_format($offer->amount, 0, ',', '.') . " telah diterima",
            $offer
        );
    }

    public function notifyOfferRejected(User $user, $offer): void
    {
        $this->create(
            $user->id,
            'offer',
            'Tawaran Ditolak',
            "Tawaran Anda sebesar Rp " . number_format($offer->amount, 0, ',', '.') . " telah ditolak",
            $offer
        );
    }

    public function notifyTransactionCreated(User $user, $transaction): void
    {
        $this->create(
            $user->id,
            'transaction',
            'Transaksi Baru',
            "Transaksi baru untuk produk {$transaction->product->title}",
            $transaction
        );
    }

    public function notifyTransactionShipped(User $user, $transaction): void
    {
        $this->create(
            $user->id,
            'transaction',
            'Barang Dikirim',
            "Barang untuk transaksi {$transaction->product->title} telah dikirim",
            $transaction
        );
    }

    public function notifyTransactionReceived(User $user, $transaction): void
    {
        $this->create(
            $user->id,
            'transaction',
            'Paket Diterima',
            "Paket untuk transaksi {$transaction->product->title} telah diterima",
            $transaction
        );
    }

    public function notifyNewReview(User $user, $review): void
    {
        $this->create(
            $user->id,
            'review',
            'Review Baru',
            "Anda mendapat review baru dari {$review->reviewer->name}",
            $review
        );
    }

    public function notifyTransactionCancelled(User $user, $transaction, string $cancelledBy): void
    {
        $cancellerName = $cancelledBy === 'buyer' 
            ? $transaction->buyer->name 
            : $transaction->seller->name;
        
        $this->create(
            $user->id,
            'transaction',
            'Transaksi Dibatalkan',
            "Transaksi untuk produk {$transaction->product->title} telah dibatalkan oleh {$cancellerName}",
            $transaction
        );
    }
}

