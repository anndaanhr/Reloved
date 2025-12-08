<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Send automatic receipt message to chat when tracking number is provided
     */
    public function sendShippingReceipt(Transaction $transaction): ?Message
    {
        // Only send if tracking number is provided
        if (!$transaction->tracking_number) {
            return null;
        }

        // Find or create conversation for this transaction
        $conversation = Conversation::firstOrCreate(
            [
                'product_id' => $transaction->product_id,
                'buyer_id' => $transaction->buyer_id,
                'seller_id' => $transaction->seller_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        // Generate receipt message
        $receiptMessage = $this->generateReceiptMessage($transaction);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $transaction->seller_id, // Message from seller
            'message' => $receiptMessage,
            'message_type' => 'receipt',
            'is_read' => false,
        ]);

        // Update conversation last message timestamp
        $conversation->updateLastMessageAt();

        return $message;
    }

    /**
     * Generate formatted receipt message with transaction details
     */
    private function generateReceiptMessage(Transaction $transaction): string
    {
        $transaction->load(['product', 'buyer', 'seller']);

        $product = $transaction->product;
        $seller = $transaction->seller;
        $buyer = $transaction->buyer;

        // Format price
        $price = 'Rp ' . number_format($transaction->price, 0, ',', '.');
        $shippingCost = $transaction->shipping_cost 
            ? 'Rp ' . number_format($transaction->shipping_cost, 0, ',', '.')
            : '-';
        $total = $transaction->shipping_cost 
            ? 'Rp ' . number_format($transaction->price + $transaction->shipping_cost, 0, ',', '.')
            : $price;

        // Format deal method
        $dealMethod = $transaction->deal_method === 'shipping' ? 'Pengiriman' : 'COD / Meet Up';

        // Build receipt message with HTML-like formatting
        $message = "📦 **INFORMASI PENGIRIMAN**\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        // Product info
        $message .= "🛍️ **Produk:**\n";
        $message .= "   {$product->title}\n\n";
        
        // Price info
        $message .= "💰 **Harga:**\n";
        $message .= "   {$price}\n\n";
        
        // Deal method
        $message .= "📋 **Metode Transaksi:**\n";
        $message .= "   {$dealMethod}\n\n";

        // Shipping details (only if shipping)
        if ($transaction->deal_method === 'shipping') {
            $message .= "🚚 **Detail Pengiriman:**\n";
            
            if ($transaction->origin_city_name && $transaction->destination_city_name) {
                $message .= "   Dari: {$transaction->origin_city_name}\n";
                $message .= "   Ke: {$transaction->destination_city_name}\n";
            }
            
            if ($transaction->shipping_courier && $transaction->shipping_service) {
                $courier = strtoupper($transaction->shipping_courier);
                $service = ucfirst($transaction->shipping_service);
                $message .= "   Kurir: {$courier} - {$service}\n";
            }
            
            $message .= "   Ongkir: {$shippingCost}\n";
            $message .= "\n";
            
            // Tracking number
            $message .= "📮 **Nomor Resi:**\n";
            $message .= "   {$transaction->tracking_number}\n\n";
            
            // Tracking link (if available)
            $trackingLink = $this->getTrackingLink($transaction->shipping_courier, $transaction->tracking_number);
            if ($trackingLink) {
                $message .= "🔗 **Lacak Paket:**\n";
                $message .= "   {$trackingLink}\n\n";
            }
        } else {
            // Meetup location
            if ($transaction->meetup_location) {
                $message .= "📍 **Lokasi Meet Up:**\n";
                $message .= "   {$transaction->meetup_location}\n\n";
            }
        }

        // Total
        if ($transaction->deal_method === 'shipping' && $transaction->shipping_cost) {
            $message .= "💵 **Total Pembayaran:**\n";
            $message .= "   {$total}\n\n";
        }

        // Seller info
        $message .= "👤 **Penjual:**\n";
        $message .= "   {$seller->name}\n\n";

        // Buyer info
        $message .= "👤 **Pembeli:**\n";
        $message .= "   {$buyer->name}\n\n";

        // Timestamp
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📅 " . now()->format('d M Y, H:i') . " WIB\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";

        return $message;
    }

    /**
     * Get tracking link based on courier
     */
    private function getTrackingLink(?string $courier, string $trackingNumber): ?string
    {
        if (!$courier || !$trackingNumber) {
            return null;
        }

        $courier = strtolower($courier);
        $trackingNumber = urlencode($trackingNumber);

        $links = [
            'jne' => "https://www.jne.co.id/id/tracking/trace?awb={$trackingNumber}",
            'jnt' => "https://www.jet.co.id/id/tracking?awb={$trackingNumber}",
            'tiki' => "https://www.tiki.id/id/tracking?awb={$trackingNumber}",
            'sicepat' => "https://www.sicepat.com/checkAwb?awb={$trackingNumber}",
            'pos' => "https://www.posindonesia.co.id/id/tracking?awb={$trackingNumber}",
            'anteraja' => "https://www.anteraja.id/id/tracking?awb={$trackingNumber}",
            'ninja' => "https://www.ninjaxpress.co.id/id/tracking?awb={$trackingNumber}",
        ];

        return $links[$courier] ?? null;
    }
}

