@extends('layouts.app')

@section('title', 'Chat - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8" x-data="chat()">
    <!-- Accept Offer Modal -->
    <div x-show="showAcceptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="showAcceptModal = false" class="bg-white rounded-16 shadow-lg p-6 w-full max-w-sm">
            <h3 class="text-lg font-bold text-text-primary mb-4">Terima Tawaran</h3>
            <p class="text-sm text-text-secondary mb-6">Apakah Anda yakin ingin menerima tawaran ini? Tindakan ini tidak dapat dibatalkan.</p>
            <p x-show="acceptOfferErrorMessage" x-text="acceptOfferErrorMessage" class="text-sm text-red-600 mb-4"></p>
            
            <div class="flex justify-end gap-4">
                <button @click="showAcceptModal = false" class="text-sm font-semibold text-text-secondary px-4 py-2 rounded-10 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button @click="confirmAcceptOffer()" class="text-sm font-semibold text-white bg-primary px-4 py-2 rounded-10 hover:bg-primary/90 transition">
                    Ya, Terima
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Offer Modal -->
    <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="showRejectModal = false" class="bg-white rounded-16 shadow-lg p-6 w-full max-w-sm">
            <h3 class="text-lg font-bold text-text-primary mb-4">Tolak Tawaran</h3>
            <p class="text-sm text-text-secondary mb-6">Apakah Anda yakin ingin menolak tawaran ini?</p>
            <p x-show="rejectOfferErrorMessage" x-text="rejectOfferErrorMessage" class="text-sm text-red-600 mb-4"></p>
            
            <div class="flex justify-end gap-4">
                <button @click="showRejectModal = false" class="text-sm font-semibold text-text-secondary px-4 py-2 rounded-10 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button @click="confirmRejectOffer()" class="text-sm font-semibold text-white bg-red-500 px-4 py-2 rounded-10 hover:bg-red-600 transition">
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>

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
                            <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
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
                                                    <button @click="acceptOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-primary text-white rounded-8 hover:opacity-90 transition">
                                                        Terima
                                                    </button>
                                                    <button @click="rejectOffer('{{ $conversation->id }}', '{{ $message->id }}')" class="text-xs px-3 py-1 bg-red-500 text-white rounded-8 hover:bg-red-600 transition">
                                                        Tolak
                                                    </button>
                                                    <button @click="openCounterOfferModal('{{ $conversation->id }}', '{{ $message->id }}', {{ $message->offer_amount }}, {{ $message->offer_counter_count }})" class="text-xs px-3 py-1 bg-yellow-500 text-white rounded-8 hover:bg-yellow-600 transition">
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
                                <button @click="openMakeOfferModal('{{ $conversation->id }}', {{ $conversation->product->price }})" class="text-sm bg-yellow-500 text-white px-4 py-2 rounded-10 font-semibold hover:bg-yellow-600 transition">
                                    💰 Tawar Harga
                                </button>
                            </div>
                        @endif
                        <form id="message-form" @submit.prevent="sendMessage" class="flex gap-2">
                            @csrf
                            <input 
                                type="text" 
                                x-model="newMessage"
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

    <!-- Counter Offer Modal -->
    <div x-show="showCounterOfferModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="showCounterOfferModal = false" class="bg-white rounded-16 shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold text-text-primary mb-4">Buat Penawaran Balik</h3>
            <p class="text-sm text-text-secondary mb-4">
                Tawaran saat ini: <span class="font-semibold">Rp <span x-text="counterOfferOriginalAmount.toLocaleString('id-ID')"></span></span>.
                Masukkan harga penawaran baru Anda di bawah ini.
            </p>
            
            <div class="space-y-2">
                <label for="counter-offer-input" class="block text-sm font-medium text-text-primary">Harga Tawar Balik (Rp)</label>
                <input type="number" id="counter-offer-input" x-model.number="counterOfferInput" class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition" placeholder="e.g. 120000">
                <p x-show="errorMessage" x-text="errorMessage" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button @click="showCounterOfferModal = false" class="text-sm font-semibold text-text-secondary px-4 py-2 rounded-10 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button @click="submitCounterOffer()" class="text-sm font-semibold text-white bg-primary px-4 py-2 rounded-10 hover:bg-primary/90 transition">
                    Kirim Tawar Balik
                </button>
            </div>
        </div>
    </div>

    <!-- Make Offer Modal -->
    <div x-show="showMakeOfferModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="showMakeOfferModal = false" class="bg-white rounded-16 shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold text-text-primary mb-4">Ajukan Penawaran</h3>
            <p class="text-sm text-text-secondary mb-4">
                Harga produk: <span class="font-semibold">Rp <span x-text="makeOfferProductPrice.toLocaleString('id-ID')"></span></span>.
                Tawaran Anda harus lebih rendah dari harga produk.
            </p>
            
            <div class="space-y-2">
                <label for="make-offer-input" class="block text-sm font-medium text-text-primary">Harga Penawaran Anda (Rp)</label>
                <input type="number" id="make-offer-input" x-model.number="makeOfferInput" class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition" placeholder="e.g. 80000">
                <p x-show="errorMessage" x-text="errorMessage" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button @click="showMakeOfferModal = false" class="text-sm font-semibold text-text-secondary px-4 py-2 rounded-10 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button @click="submitMakeOffer()" class="text-sm font-semibold text-white bg-primary px-4 py-2 rounded-10 hover:bg-primary/90 transition">
                    Kirim Penawaran
                </button>
            </div>
        </div>
    </div>
</div>

@if($conversation)
@push('scripts')
<script>
    function chat() {
        return {
            // State
            newMessage: '',
            showCounterOfferModal: false,
            counterOfferConversationId: null,
            counterOfferMessageId: null,
            counterOfferOriginalAmount: 0,
            counterOfferInput: '',
            showMakeOfferModal: false,
            makeOfferConversationId: null,
            makeOfferProductPrice: 0,
            makeOfferInput: '',
            errorMessage: '',
            showAcceptModal: false,
            acceptOfferConversationId: null,
            acceptOfferMessageId: null,
            showRejectModal: false,
            rejectOfferConversationId: null,
            rejectOfferMessageId: null,
            acceptOfferErrorMessage: '',
            rejectOfferErrorMessage: '',

            // Init
            init() {
                const messagesContainer = document.getElementById('messages-container');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
                
                if (typeof window.Echo !== 'undefined') {
                    console.log('Echo is defined, subscribing to channel.');
                    window.Echo.private('chat.{{ $conversation->id }}')
                        .listen('.MessageSent', (e) => {
                            console.log('Message received via Echo:', e.message);
                            this.appendMessage(e.message);
                            this.loadMessages(); // Reload to update offer statuses and buttons
                        });
                } else {
                    console.log("Realtime disabled → using polling mode only");
                    setInterval(() => this.loadMessages(), 5000);
                }
            },

            // Methods
            appendMessage(message) {
                if (document.querySelector(`[data-message-id="${message.id}"]`)) {
                    return;
                }

                const messagesContainer = document.getElementById('messages-container');
                const messageEl = document.createElement('div');
                const isSender = message.sender_id === {{ Auth::id() }};
                
                messageEl.className = `flex ${isSender ? 'justify-end' : 'justify-start'}`;
                messageEl.setAttribute('data-message-id', message.id);

                let senderName = '';
                if (!isSender && message.sender) {
                    senderName = `<p class="text-xs text-text-secondary mb-1">${message.sender.name}</p>`;
                }

                let offerDetails = '';
                if (message.message_type === 'offer') {
                    const status = message.offer_status.charAt(0).toUpperCase() + message.offer_status.slice(1).replace('_', ' ');
                    offerDetails = `
                        <div class="mb-2">
                            <p class="text-sm font-semibold">Tawaran: Rp ${Number(message.offer_amount).toLocaleString('id-ID')}</p>
                            <p class="text-xs opacity-75">Status: ${status}</p>
                        </div>
                    `;
                }

                const messageContent = message.message ? `<p class="text-sm">${message.message}</p>` : '';

                const messageDate = new Date(message.created_at);
                const time = `${String(messageDate.getHours()).padStart(2, '0')}:${String(messageDate.getMinutes()).padStart(2, '0')}`;

                messageEl.innerHTML = `
                    <div class="max-w-xs lg:max-w-md">
                        ${senderName}
                        <div class="rounded-10 p-3 ${isSender ? 'bg-primary text-white' : 'bg-gray-100 text-text-primary'}">
                            ${offerDetails}
                            ${messageContent}
                        </div>
                        <p class="text-xs text-text-tertiary mt-1">${time}</p>
                    </div>
                `;

                messagesContainer.appendChild(messageEl);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            },

            async loadMessages() {
                try {
                    const response = await fetch(`{{ route('chat.show', $conversation->id) }}?ajax=1`, {
                        headers: { "X-Requested-With": "XMLHttpRequest" }
                    });
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");
                    const newMessages = doc.querySelector('#messages-container');
                    if (newMessages) {
                        const messagesContainer = document.getElementById('messages-container');
                        const isScrolledToBottom = messagesContainer.scrollHeight - messagesContainer.clientHeight <= messagesContainer.scrollTop + 1;
                        
                        messagesContainer.innerHTML = newMessages.innerHTML;
                        
                        if (isScrolledToBottom) {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                    }
                } catch (error) {
                    console.log("Load messages error:", error);
                }
            },

          async sendMessage() {
            if (!this.newMessage.trim()) return;

            try {
                const response = await fetch(`/chat/${this.conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify({
                        message: this.newMessage,
                        offer_amount: null, // penting karena controller cek 2 field ini
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.newMessage = '';
                    this.appendMessage(data.message);
                } else {
                    alert('Gagal mengirim pesan: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Gagal mengirim pesan');
            }
        }


            acceptOffer(conversationId, messageId) {
                this.acceptOfferConversationId = conversationId;
                this.acceptOfferMessageId = messageId;
                this.acceptOfferErrorMessage = '';
                this.showAcceptModal = true;
            },

            async confirmAcceptOffer() {
                this.acceptOfferErrorMessage = '';
                try {
                    const response = await fetch(`/chat/${this.acceptOfferConversationId}/offers/${this.acceptOfferMessageId}/accept`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.appendMessage(data.message);
                        this.loadMessages(); // Reload to update buttons and statuses
                        this.showAcceptModal = false;
                    } else {
                        this.acceptOfferErrorMessage = data.error || 'Gagal menerima tawaran.';
                    }
                } catch (error) { 
                    console.error('Error accepting offer:', error); 
                    this.acceptOfferErrorMessage = 'Gagal menerima tawaran.';
                }
            },

            rejectOffer(conversationId, messageId) {
                this.rejectOfferConversationId = conversationId;
                this.rejectOfferMessageId = messageId;
                this.rejectOfferErrorMessage = '';
                this.showRejectModal = true;
            },

            async confirmRejectOffer() {
                this.rejectOfferErrorMessage = '';
                try {
                    const response = await fetch(`/chat/${this.rejectOfferConversationId}/offers/${this.rejectOfferMessageId}/reject`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.appendMessage(data.message);
                        this.loadMessages(); // Reload to update buttons and statuses
                        this.showRejectModal = false;
                    } else {
                        this.rejectOfferErrorMessage = data.error || 'Gagal menolak tawaran.';
                    }
                } catch (error) { 
                    console.error('Error rejecting offer:', error);
                    this.rejectOfferErrorMessage = 'Gagal menolak tawaran.';
                }
            },

            // Counter Offer Modal
            openCounterOfferModal(conversationId, messageId, currentAmount, counterCount) {
                const maxCounter = {{ \App\Models\Offer::MAX_COUNTER_COUNT }};
                if (counterCount >= maxCounter) {
                    alert(`Batas tawaran sudah tercapai (maksimal ${maxCounter} kali).`);
                    return;
                }
                this.counterOfferConversationId = conversationId;
                this.counterOfferMessageId = messageId;
                this.counterOfferOriginalAmount = currentAmount;
                this.counterOfferInput = '';
                this.errorMessage = '';
                this.showCounterOfferModal = true;
            },

            async submitCounterOffer() {
                this.errorMessage = '';
                if (!this.counterOfferInput || isNaN(this.counterOfferInput) || this.counterOfferInput <= 0) {
                    this.errorMessage = 'Masukkan jumlah yang valid.';
                    return;
                }
                if (this.counterOfferInput <= this.counterOfferOriginalAmount) {
                    this.errorMessage = 'Tawaran balik harus lebih tinggi dari tawaran sebelumnya.';
                    return;
                }

                try {
                    const response = await fetch(`/chat/${this.counterOfferConversationId}/offers/${this.counterOfferMessageId}/counter`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ counter_amount: this.counterOfferInput }),
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.showCounterOfferModal = false;
                        this.appendMessage(data.message);
                        this.loadMessages(); // Reload to update buttons and statuses
                    } else {
                        this.errorMessage = data.error || 'Gagal mengirim tawar balik.';
                    }
                } catch (error) {
                    console.error('Error countering offer:', error);
                    this.errorMessage = 'Terjadi kesalahan.';
                }
            },

            // Make Offer Modal
            openMakeOfferModal(conversationId, productPrice) {
                this.makeOfferConversationId = conversationId;
                this.makeOfferProductPrice = productPrice;
                this.makeOfferInput = '';
                this.errorMessage = '';
                this.showMakeOfferModal = true;
            },

            async submitMakeOffer() {
                this.errorMessage = '';
                if (!this.makeOfferInput || isNaN(this.makeOfferInput) || this.makeOfferInput <= 0) {
                    this.errorMessage = 'Masukkan jumlah yang valid.';
                    return;
                }
                if (this.makeOfferInput >= this.makeOfferProductPrice) {
                    this.errorMessage = 'Tawaran harus lebih rendah dari harga produk.';
                    return;
                }

                try {
                    const response = await fetch(`/chat/${this.makeOfferConversationId}/offers`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ offer_amount: this.makeOfferInput }),
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.showMakeOfferModal = false;
                        this.appendMessage(data.message);
                    } else {
                        this.errorMessage = data.error || 'Gagal membuat tawaran.';
                    }
                } catch (error) {
                    console.error('Error making offer:', error);
                    this.errorMessage = 'Terjadi kesalahan.';
                }
            }
        }
    }
</script>
@endpush
@endif
@endsection
