@extends('layouts.app')

@section('title', 'Chat - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-xl font-bold text-text-primary mb-6">Pesan Saya</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Conversations List -->
            <div class="lg:col-span-1 bg-white rounded-16 border border-border overflow-hidden">
                <div class="p-4 border-b border-border">
                    <h2 class="text-sm font-semibold text-text-primary">Percakapan</h2>
                </div>
                <div class="overflow-y-auto max-h-[600px]">
                    @foreach($conversations as $conv)
                        <a href="{{ route('chat.show', $conv->id) }}" class="block p-4 border-b border-border hover:bg-gray-50 transition {{ request()->route('id') == $conv->id ? 'bg-primary-50' : '' }}">
                            <div class="flex items-start gap-3">
                                <!-- Avatar -->
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    @if($conv->product && $conv->product->images->count() > 0)
                                        <img src="{{ $conv->product->images->first()->cloudinary_url }}" alt="{{ $conv->product->title }}" class="w-full h-full object-cover rounded-full">
                                    @else
                                        <span class="text-text-secondary font-semibold text-sm">{{ substr($conv->product->title ?? 'P', 0, 1) }}</span>
                                    @endif
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-text-primary truncate">
                                        @if($conv->product)
                                            {{ $conv->product->title }}
                                        @else
                                            {{ $conv->buyer_id === Auth::id() ? $conv->seller->name : $conv->buyer->name }}
                                        @endif
                                    </p>
                                    @if($conv->messages->count() > 0)
                                        <p class="text-xs text-text-secondary truncate mt-1">
                                            {{ $conv->messages->first()->message ?? 'Pesan' }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-text-tertiary mt-1">
                                        {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Chat Area -->
            <div class="lg:col-span-2 bg-white rounded-16 border border-border flex flex-col" style="height: 600px;">
                @if($conversation)
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-border flex items-center gap-3">
                        @if($conversation->product)
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                                @if($conversation->product->images->count() > 0)
                                    <img src="{{ $conversation->product->images->first()->cloudinary_url }}" alt="{{ $conversation->product->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-text-primary truncate">{{ $conversation->product->title }}</p>
                                <p class="text-xs text-text-secondary">
                                    {{ $conversation->buyer_id === Auth::id() ? $conversation->seller->name : $conversation->buyer->name }}
                                </p>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <span class="text-text-secondary font-semibold text-sm">{{ substr($conversation->buyer_id === Auth::id() ? $conversation->seller->name : $conversation->buyer->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-text-primary">
                                    {{ $conversation->buyer_id === Auth::id() ? $conversation->seller->name : $conversation->buyer->name }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Messages -->
                    <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4">
                        @foreach($conversation->messages as $message)
                            <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md">
                                    @if($message->sender_id !== Auth::id())
                                        <p class="text-xs text-text-secondary mb-1">{{ $message->sender->name }}</p>
                                    @endif
                                    <div class="rounded-10 p-3 {{ $message->sender_id === Auth::id() ? 'bg-primary text-white' : 'bg-gray-100 text-text-primary' }}">
                                        @if($message->message_type === 'offer')
                                            <div class="mb-2">
                                                <p class="text-sm font-semibold">Tawaran: Rp {{ number_format($message->offer_amount, 0, ',', '.') }}</p>
                                                <p class="text-xs opacity-75">Status: {{ ucfirst(str_replace('_', ' ', $message->offer_status ?? 'pending')) }}</p>
                                                @if($message->offer_counter_count > 0)
                                                    <p class="text-xs opacity-75">Tawaran ke-{{ $message->offer_counter_count + 1 }} dari {{ \App\Models\Offer::MAX_COUNTER_COUNT }}</p>
                                                @endif
                                            </div>
                                            @if($message->offer_status === 'pending' && $message->sender_id !== Auth::id() && Auth::id() === $conversation->seller_id)
                                                <div class="flex gap-2 mt-2">
                                                    <button onclick="acceptOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-primary text-white rounded-8 hover:opacity-90 transition">
                                                        Terima
                                                    </button>
                                                    <button onclick="rejectOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-red-500 text-white rounded-8 hover:bg-red-600 transition">
                                                        Tolak
                                                    </button>
                                                    <button onclick="openCounterOfferModal('{{ $conversation->id }}', '{{ $message->id }}', {{ $message->offer_amount }}, {{ $message->offer_counter_count }})" class="text-xs px-3 py-1 bg-yellow-500 text-white rounded-8 hover:bg-yellow-600 transition">
                                                        Tawar Balik
                                                    </button>
                                                </div>
                                            @endif
                                        @endif
                                        @if($message->message)
                                            <p class="text-sm">{{ $message->message }}</p>
                                        @endif
                                    </div>
                                    <p class="text-xs text-text-tertiary mt-1">{{ $message->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Message Input -->
                    <div class="p-4 border-t border-border">
                        @if($conversation->product && Auth::id() === $conversation->buyer_id)
                            <!-- Buyer: Can make offer -->
                            <div class="mb-2">
                                <button onclick="openMakeOfferModal('{{ $conversation->id }}', {{ $conversation->product->price }})" class="text-sm bg-yellow-500 text-white px-4 py-2 rounded-10 font-semibold hover:bg-yellow-600 transition">
                                    💰 Tawar Harga
                                </button>
                            </div>
                        @endif
                        <form id="message-form" class="flex gap-2">
                            @csrf
                            <input 
                                type="text" 
                                id="message-input" 
                                name="message" 
                                placeholder="Ketik pesan..."
                                class="flex-1 h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                                autocomplete="off"
                            >
                            <button 
                                type="submit" 
                                class="bg-primary text-white px-6 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition"
                            >
                                Kirim
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-gray-500">
                        <p>Pilih percakapan untuk mulai chat</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($conversation)
@push('scripts')
<script>
    const conversationId = '{{ $conversation->id }}';
    const currentUserId = '{{ Auth::id() }}';
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');

    // Polling untuk update messages (karena tidak ada Redis untuk Reverb)
    // Chat akan auto-refresh setiap 3 detik untuk melihat pesan baru
    let pollingInterval = null;
    
    function checkNewMessages() {
        // Simple reload untuk update messages
        // Bisa diimprove dengan API endpoint yang return JSON messages saja
        const currentUrl = window.location.href;
        fetch(currentUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // Check if page has new content (simplified approach)
                // In production, better to use API endpoint
                return response.text();
            }
        })
        .catch(error => {
            console.log('Polling error (non-critical):', error);
        });
    }
    
    // Start polling setiap 3 detik untuk check pesan baru
    pollingInterval = setInterval(() => {
        // Reload page untuk update messages (simple approach)
        // Note: Ini akan reload seluruh page, bisa diimprove dengan AJAX
        // Untuk sementara, user bisa refresh manual atau tunggu auto-reload
    }, 5000); // 5 detik
    
    // Try to use Echo if available (untuk future jika Redis tersedia)
    if (typeof window.Echo !== 'undefined') {
        try {
            window.Echo.join(`conversation.${conversationId}`)
                .listen('.message.sent', (e) => {
                    addMessageToChat(e);
                    scrollToBottom();
                    // Stop polling jika Echo berhasil connect
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                })
                .error((error) => {
                    console.log('Echo not available, using manual refresh instead');
                });
        } catch (error) {
            console.log('Echo not available, messages will update on page refresh');
        }
    } else {
        console.log('Echo not available. Messages will update when you refresh the page.');
    }

    // Send message
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch(`{{ route('chat.store', $conversation->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();
            
            if (data.success) {
                messageInput.value = '';
                // Reload messages after sending (karena tidak ada real-time)
                loadMessages();
                scrollToBottom();
            } else {
                alert('Gagal mengirim pesan: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Gagal mengirim pesan');
        }
    });

    function addMessageToChat(messageData) {
        const isOwnMessage = messageData.sender_id === currentUserId;
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`;
        
        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                ${!isOwnMessage ? `<p class="text-xs text-gray-600 mb-1">${messageData.sender_name}</p>` : ''}
                <div class="rounded-lg p-3 ${isOwnMessage ? 'bg-primary text-white' : 'bg-gray-100 text-gray-900'}">
                    ${messageData.message ? `<p class="text-sm">${messageData.message}</p>` : ''}
                </div>
                <p class="text-xs text-gray-500 mt-1">${new Date(messageData.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</p>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Offer functions
    function openMakeOfferModal(conversationId, productPrice) {
        const amount = prompt(`Masukkan tawaran Anda (maksimal Rp ${productPrice.toLocaleString('id-ID')}):`, '');
        if (amount && !isNaN(amount) && parseFloat(amount) > 0 && parseFloat(amount) < productPrice) {
            makeOffer(conversationId, parseFloat(amount));
        } else if (amount) {
            alert('Tawaran tidak valid. Harus lebih rendah dari harga produk.');
        }
    }

    async function makeOffer(conversationId, amount) {
        try {
            const response = await fetch(`/chat/${conversationId}/offers`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                },
                body: JSON.stringify({ offer_amount: amount }),
            });

            const data = await response.json();
            if (data.success) {
                // Message will be added via broadcast
            } else {
                alert('Gagal membuat tawaran: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error making offer:', error);
            alert('Gagal membuat tawaran');
        }
    }

    async function acceptOffer(conversationId, messageId) {
        if (!confirm('Terima tawaran ini?')) return;

        try {
            const response = await fetch(`/chat/${conversationId}/offers/${messageId}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                },
            });

            const data = await response.json();
            if (data.success) {
                // Message will be added via broadcast
            } else {
                alert('Gagal menerima tawaran: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error accepting offer:', error);
            alert('Gagal menerima tawaran');
        }
    }

    async function rejectOffer(conversationId, messageId) {
        if (!confirm('Tolak tawaran ini?')) return;

        try {
            const response = await fetch(`/chat/${conversationId}/offers/${messageId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                },
            });

            const data = await response.json();
            if (data.success) {
                // Message will be added via broadcast
            } else {
                alert('Gagal menolak tawaran: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error rejecting offer:', error);
            alert('Gagal menolak tawaran');
        }
    }

    function openCounterOfferModal(conversationId, messageId, currentAmount, counterCount) {
        const maxCounter = {{ \App\Models\Offer::MAX_COUNTER_COUNT }};
        if (counterCount >= maxCounter) {
            alert(`Batas tawaran sudah tercapai (maksimal ${maxCounter} kali).`);
            return;
        }

        const amount = prompt(`Masukkan tawaran balik Anda (harus lebih tinggi dari Rp ${currentAmount.toLocaleString('id-ID')}):`, '');
        if (amount && !isNaN(amount) && parseFloat(amount) > currentAmount) {
            counterOffer(conversationId, messageId, parseFloat(amount));
        } else if (amount) {
            alert('Tawaran balik harus lebih tinggi dari tawaran sebelumnya.');
        }
    }

    async function counterOffer(conversationId, messageId, amount) {
        try {
            const response = await fetch(`/chat/${conversationId}/offers/${messageId}/counter`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                },
                body: JSON.stringify({ counter_amount: amount }),
            });

            const data = await response.json();
            if (data.success) {
                // Message will be added via broadcast
            } else {
                alert('Gagal membuat tawaran balik: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error countering offer:', error);
            alert('Gagal membuat tawaran balik');
        }
    }

    // Scroll to bottom on load
    scrollToBottom();
</script>
@endpush
@endif
@endsection

