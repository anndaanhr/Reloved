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
                                            @php
                                                $isSeller = Auth::id() === $conversation->seller_id;
                                                $isBuyer = Auth::id() === $conversation->buyer_id;
                                                $isPending = $message->offer_status === 'pending';
                                                $isCounterOffer = $message->offer_status === 'counter_offer';
                                                $canCounter = $message->offer_counter_count < \App\Models\Offer::MAX_COUNTER_COUNT;
                                                $isOwnOffer = $message->sender_id === Auth::id();
                                            @endphp
                                            
                                            {{-- Seller actions for buyer's initial offer --}}
                                            @if($isPending && !$isOwnOffer && $isSeller)
                                                <div class="flex gap-2 mt-2" id="offer-actions-{{ $message->id }}">
                                                    <button onclick="acceptOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-primary text-white rounded-8 hover:opacity-90 transition">
                                                        Terima
                                                    </button>
                                                    <button onclick="rejectOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-red-500 text-white rounded-8 hover:bg-red-600 transition">
                                                        Tolak
                                                    </button>
                                                    @if($canCounter)
                                                        <button onclick="openCounterOfferModal('{{ $conversation->id }}', '{{ $message->id }}', {{ $message->offer_amount }}, {{ $message->offer_counter_count }}, {{ $conversation->product->price }})" class="text-xs px-3 py-1 bg-yellow-500 text-white rounded-8 hover:bg-yellow-600 transition">
                                                            Tawar Balik
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            {{-- Buyer actions for seller's counter offer --}}
                                            @if($isCounterOffer && !$isOwnOffer && $isBuyer && $canCounter)
                                                <div class="flex gap-2 mt-2" id="offer-actions-{{ $message->id }}">
                                                    <button onclick="acceptOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-primary text-white rounded-8 hover:opacity-90 transition">
                                                        Terima
                                                    </button>
                                                    <button onclick="rejectOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-red-500 text-white rounded-8 hover:bg-red-600 transition">
                                                        Tolak
                                                    </button>
                                                    <button onclick="openBuyerCounterOfferModal('{{ $conversation->id }}', '{{ $message->id }}', {{ $message->offer_amount }}, {{ $message->offer_counter_count }}, {{ $conversation->product->price }})" class="text-xs px-3 py-1 bg-yellow-500 text-white rounded-8 hover:bg-yellow-600 transition">
                                                        Tawar Lagi
                                                    </button>
                                                </div>
                                            @endif
                                            
                                            {{-- Show status if offer is accepted or rejected --}}
                                            @if(in_array($message->offer_status, ['accepted', 'rejected']))
                                                <p class="text-xs mt-2 opacity-75 italic">
                                                    @if($message->offer_status === 'accepted')
                                                        ✓ Tawaran ini telah diterima
                                                    @else
                                                        ✗ Tawaran ini telah ditolak
                                                    @endif
                                                </p>
                                            @endif
                                        @endif
                                        
                                        {{-- Receipt Message --}}
                                        @if($message->message_type === 'receipt')
                                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-lg p-4 mt-2 max-w-md shadow-sm">
                                                <div class="text-sm whitespace-pre-line leading-relaxed text-gray-800">
                                                    {!! preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-bold text-gray-900">$1</strong>', e($message->message)) !!}
                                                </div>
                                            </div>
                                        @elseif($message->message)
                                            <p class="text-sm">{{ $message->message }}</p>
                                        @endif
                                    </div>
                                    <p class="text-xs text-text-tertiary mt-1">{{ $message->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}</p>
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

<!-- Custom Modal for Offers -->
<div id="offer-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-16 p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 id="offer-modal-title" class="text-lg font-semibold text-text-primary">Tawar Harga</h3>
            <button onclick="closeOfferModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="offer-modal-content" class="space-y-4">
            <!-- Content will be dynamically inserted -->
        </div>
        <div id="offer-modal-actions" class="flex gap-2 mt-6">
            <!-- Actions will be dynamically inserted -->
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

    async function loadMessages() {
        try {
            const response = await fetch(`/chat/${conversationId}?ajax=1`, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const html = await response.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newMessages = doc.querySelector('#messages-container');

            if (newMessages) {
                messagesContainer.innerHTML = newMessages.innerHTML;
                scrollToBottom();
                
                // Disable buttons for accepted/rejected offers
                disableOfferButtons();
            }
        } catch (error) {
            console.log("Load messages error:", error);
        }
    }

    function disableOfferButtons() {
        // Find all offer action divs and disable buttons if offer is accepted/rejected
        const actionDivs = messagesContainer.querySelectorAll('[id^="offer-actions-"]');
        actionDivs.forEach(div => {
            // Check if parent message has status text indicating accepted/rejected
            const messageElement = div.closest('.flex');
            if (messageElement) {
                const statusText = messageElement.textContent;
                // Also check for offer status in the message bubble
                const messageBubble = messageElement.querySelector('.rounded-10');
                if (messageBubble) {
                    const bubbleText = messageBubble.textContent;
                    if (bubbleText.includes('telah diterima') || bubbleText.includes('telah ditolak') || 
                        bubbleText.includes('Status: Accepted') || bubbleText.includes('Status: Rejected')) {
                        div.querySelectorAll('button').forEach(btn => {
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                            btn.style.pointerEvents = 'none';
                            // Remove onclick handlers
                            const newBtn = btn.cloneNode(true);
                            btn.parentNode.replaceChild(newBtn, btn);
                        });
                    }
                }
            }
        });
    }

    setInterval(() => {
        loadMessages();
    }, 5000);


    console.log("Realtime disabled → using polling mode only");


    /**
     * SEND MESSAGE
     */
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
                loadMessages();
                scrollToBottom();
            } else {
                showOfferModal('Error', `<p class="text-red-600">${data.error || 'Gagal mengirim pesan'}</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            showOfferModal('Error', '<p class="text-red-600">Terjadi kesalahan saat mengirim pesan</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
        }
    });


    /** RENDER MANUAL MESSAGE (tidak dipakai jika polling berjalan) */
    function addMessageToChat(messageData) {
        const isOwnMessage = messageData.sender_id == currentUserId;

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

    /**
     * OFFER FUNCTIONS — Custom Modal Implementation
     */

    // Modal helper functions
    function showOfferModal(title, content, actions) {
        document.getElementById('offer-modal-title').textContent = title;
        document.getElementById('offer-modal-content').innerHTML = content;
        document.getElementById('offer-modal-actions').innerHTML = actions;
        document.getElementById('offer-modal').classList.remove('hidden');
    }

    function closeOfferModal() {
        document.getElementById('offer-modal').classList.add('hidden');
        document.getElementById('offer-modal-content').innerHTML = '';
        document.getElementById('offer-modal-actions').innerHTML = '';
    }

    // Close modal on outside click
    document.getElementById('offer-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeOfferModal();
        }
    });

    // Buyer: Make initial offer
    function openMakeOfferModal(conversationId, productPrice) {
        const content = `
            <div>
                <p class="text-sm text-gray-700 mb-2">Harga produk: <span class="font-semibold">Rp ${productPrice.toLocaleString('id-ID')}</span></p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan tawaran Anda:</label>
                <input 
                    type="number" 
                    id="offer-amount-input" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="0"
                    min="1"
                    max="${productPrice - 1}"
                    step="1000"
                >
                <p class="text-xs text-gray-500 mt-2">Tawaran harus lebih rendah dari harga produk</p>
            </div>
        `;
        
        const actions = `
            <button onclick="closeOfferModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </button>
            <button onclick="submitMakeOffer('${conversationId}', ${productPrice})" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition">
                Kirim Tawaran
            </button>
        `;
        
        showOfferModal('Tawar Harga', content, actions);
    }

    function submitMakeOffer(conversationId, productPrice) {
        const amount = parseFloat(document.getElementById('offer-amount-input').value);
        if (!amount || isNaN(amount) || amount <= 0 || amount >= productPrice) {
            showOfferModal('Error', '<p class="text-red-600">Tawaran tidak valid. Harus lebih rendah dari harga produk.</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            return;
        }
        closeOfferModal();
        makeOffer(conversationId, amount);
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
                loadMessages();
            } else {
                showOfferModal('Error', `<p class="text-red-600">${data.error || 'Gagal membuat tawaran'}</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            }
        } catch (error) {
            console.error('Error making offer:', error);
            showOfferModal('Error', '<p class="text-red-600">Terjadi kesalahan saat membuat tawaran</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
        }
    }

    // Accept offer
    async function acceptOffer(conversationId, messageId) {
        const content = '<p class="text-gray-700">Apakah Anda yakin ingin menerima tawaran ini?</p>';
        const actions = `
            <button onclick="closeOfferModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </button>
            <button onclick="submitAcceptOffer('${conversationId}', '${messageId}')" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition">
                Terima
            </button>
        `;
        showOfferModal('Terima Tawaran', content, actions);
    }

    async function submitAcceptOffer(conversationId, messageId) {
        closeOfferModal();
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
                loadMessages();
                // Disable buttons after action
                setTimeout(disableOfferButtons, 100);
            } else {
                showOfferModal('Error', `<p class="text-red-600">${data.error || 'Gagal menerima tawaran'}</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            }
        } catch (error) {
            console.error('Error accepting offer:', error);
            showOfferModal('Error', '<p class="text-red-600">Terjadi kesalahan saat menerima tawaran</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
        }
    }

    // Reject offer
    async function rejectOffer(conversationId, messageId) {
        const content = '<p class="text-gray-700">Apakah Anda yakin ingin menolak tawaran ini?</p>';
        const actions = `
            <button onclick="closeOfferModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </button>
            <button onclick="submitRejectOffer('${conversationId}', '${messageId}')" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Tolak
            </button>
        `;
        showOfferModal('Tolak Tawaran', content, actions);
    }

    async function submitRejectOffer(conversationId, messageId) {
        closeOfferModal();
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
                loadMessages();
                // Disable buttons after action
                setTimeout(disableOfferButtons, 100);
            } else {
                showOfferModal('Error', `<p class="text-red-600">${data.error || 'Gagal menolak tawaran'}</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            }
        } catch (error) {
            console.error('Error rejecting offer:', error);
            showOfferModal('Error', '<p class="text-red-600">Terjadi kesalahan saat menolak tawaran</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
        }
    }

    // Seller: Counter offer
    function openCounterOfferModal(conversationId, messageId, currentAmount, counterCount, productPrice) {
        const maxCounter = {{ \App\Models\Offer::MAX_COUNTER_COUNT }};
        if (counterCount >= maxCounter) {
            showOfferModal('Batas Tercapai', `<p class="text-red-600">Batas tawaran sudah tercapai (maksimal ${maxCounter} kali).</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            return;
        }

        const content = `
            <div>
                <p class="text-sm text-gray-700 mb-2">Tawaran saat ini: <span class="font-semibold">Rp ${currentAmount.toLocaleString('id-ID')}</span></p>
                <p class="text-sm text-gray-700 mb-2">Harga produk: <span class="font-semibold">Rp ${productPrice.toLocaleString('id-ID')}</span></p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan tawaran balik Anda:</label>
                <input 
                    type="number" 
                    id="counter-amount-input" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="0"
                    min="${currentAmount + 1}"
                    max="${productPrice - 1}"
                    step="1000"
                >
                <p class="text-xs text-gray-500 mt-2">Tawaran balik harus lebih tinggi dari tawaran sebelumnya dan lebih rendah dari harga produk</p>
            </div>
        `;
        
        const actions = `
            <button onclick="closeOfferModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </button>
            <button onclick="submitCounterOffer('${conversationId}', '${messageId}', ${currentAmount}, ${productPrice})" class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                Tawar Balik
            </button>
        `;
        
        showOfferModal('Tawar Balik', content, actions);
    }

    function submitCounterOffer(conversationId, messageId, currentAmount, productPrice) {
        const amount = parseFloat(document.getElementById('counter-amount-input').value);
        if (!amount || isNaN(amount) || amount <= currentAmount || amount >= productPrice) {
            showOfferModal('Error', '<p class="text-red-600">Tawaran balik harus lebih tinggi dari tawaran sebelumnya dan lebih rendah dari harga produk.</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            return;
        }
        closeOfferModal();
        counterOffer(conversationId, messageId, amount);
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
                loadMessages();
            } else {
                showOfferModal('Error', `<p class="text-red-600">${data.error || 'Gagal membuat tawaran balik'}</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            }
        } catch (error) {
            console.error('Error countering offer:', error);
            showOfferModal('Error', '<p class="text-red-600">Terjadi kesalahan saat membuat tawaran balik</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
        }
    }

    // Buyer: Counter offer after seller counter
    function openBuyerCounterOfferModal(conversationId, messageId, currentAmount, counterCount, productPrice) {
        const maxCounter = {{ \App\Models\Offer::MAX_COUNTER_COUNT }};
        if (counterCount >= maxCounter) {
            showOfferModal('Batas Tercapai', `<p class="text-red-600">Batas tawaran sudah tercapai (maksimal ${maxCounter} kali).</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            return;
        }

        const content = `
            <div>
                <p class="text-sm text-gray-700 mb-2">Tawaran saat ini: <span class="font-semibold">Rp ${currentAmount.toLocaleString('id-ID')}</span></p>
                <p class="text-sm text-gray-700 mb-2">Harga produk: <span class="font-semibold">Rp ${productPrice.toLocaleString('id-ID')}</span></p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan tawaran Anda:</label>
                <input 
                    type="number" 
                    id="buyer-counter-amount-input" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="0"
                    min="1"
                    max="${productPrice - 1}"
                    step="1000"
                >
                <p class="text-xs text-gray-500 mt-2">Tawaran harus lebih rendah dari harga produk</p>
            </div>
        `;
        
        const actions = `
            <button onclick="closeOfferModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </button>
            <button onclick="submitBuyerCounterOffer('${conversationId}', '${messageId}', ${productPrice})" class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                Tawar Lagi
            </button>
        `;
        
        showOfferModal('Tawar Lagi', content, actions);
    }

    function submitBuyerCounterOffer(conversationId, messageId, productPrice) {
        const amount = parseFloat(document.getElementById('buyer-counter-amount-input').value);
        if (!amount || isNaN(amount) || amount <= 0 || amount >= productPrice) {
            showOfferModal('Error', '<p class="text-red-600">Tawaran tidak valid. Harus lebih rendah dari harga produk.</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            return;
        }
        closeOfferModal();
        buyerCounterOffer(conversationId, messageId, amount);
    }

    async function buyerCounterOffer(conversationId, messageId, amount) {
        try {
            const response = await fetch(`/chat/${conversationId}/offers/${messageId}/buyer-counter`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                },
                body: JSON.stringify({ offer_amount: amount }),
            });

            const data = await response.json();
            if (data.success) {
                loadMessages();
            } else {
                showOfferModal('Error', `<p class="text-red-600">${data.error || 'Gagal membuat tawaran'}</p>`, '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
            }
        } catch (error) {
            console.error('Error buyer countering offer:', error);
            showOfferModal('Error', '<p class="text-red-600">Terjadi kesalahan saat membuat tawaran</p>', '<button onclick="closeOfferModal()" class="w-full px-4 py-2 bg-primary text-white rounded-lg">OK</button>');
        }
    }

    scrollToBottom();
    
    // Disable buttons on initial load
    disableOfferButtons();
</script>

@endpush
@endif
@endsection

