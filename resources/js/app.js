import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.Echo = {
    channel: () => ({
        listen: () => ({ stop: () => {} }),
    }),
    private: () => ({
        listen: () => ({ stop: () => {} }),
    }),
    leave: () => {},
    disconnect: () => {},
};


if (
    import.meta.env.VITE_ENABLE_REVERB === 'true' &&
    import.meta.env.VITE_REVERB_APP_KEY
) {
    import('laravel-echo').then(({ default: Echo }) => {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;

            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: import.meta.env.VITE_REVERB_APP_KEY,
                wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
                wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
                wssPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });

            console.log("Realtime Echo berhasil aktif!");
        });
    });
}

/**
 * -------------------------------------------------------------------
 * Listener Realtime Chat
 * -------------------------------------------------------------------
 * <div id="chat-box" data-conversation-id="{{ $conversation->id }}"></div>
 * -------------------------------------------------------------------
 */
document.addEventListener("DOMContentLoaded", () => {
    const chatBox = document.getElementById("chat-box");
    if (!chatBox) return; // halaman bukan halaman chat

    const conversationId = chatBox.dataset.conversationId;
    console.log("Subscribed to: conversation." + conversationId);

    window.Echo.private(`conversation.${conversationId}`)
        .listen(".message.sent", (event) => {
            console.log("Realtime message received:", event);

            const div = document.createElement("div");
            div.classList.add("message");
            div.innerHTML = `<b>${event.message.user.name}</b>: ${event.message.body}`;

            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        });
});
