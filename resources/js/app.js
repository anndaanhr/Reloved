import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * -------------------------------------------------------
 * 1. HAPUS conversationId = null (ini bikin error)
 * -------------------------------------------------------
 */
// window.conversationId = null;  <-- HAPUS INI TOTAL


/**
 * -------------------------------------------------------
 * 2. Dummy Echo (hanya fallback)
 * -------------------------------------------------------
 */
window.Echo = {
    channel: () => ({ listen: () => ({ stop: () => {} }) }),
    private: () => ({ listen: () => ({ stop: () => {} }) }),
    leave: () => {},
    disconnect: () => {},
};


/**
 * -------------------------------------------------------
 * 3. Alpine Component
 * -------------------------------------------------------
 */
window.chat = function () {
    return {
        newMessage: "",
        messages: [],

        async sendMessage() {

            // CEK: ID harus ada
            if (!window.conversationId) {
                console.error("conversationId is NULL!");
                return;
            }

            if (!this.newMessage.trim()) return;

            try {
                const response = await fetch(`/chat/${window.conversationId}/send`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({ message: this.newMessage }),
                });

                const data = await response.json();

                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = "";
                } else {
                    console.error(data.error);
                }
            } catch (e) {
                console.error("Send failed:", e);
            }
        }
    };
};


/**
 * -------------------------------------------------------
 * 4. Load Real Reverb/Echo
 * -------------------------------------------------------
 */
if (import.meta.env.VITE_REVERB_APP_KEY) {
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
 * -------------------------------------------------------
 * 5. Listener Realtime
 * -------------------------------------------------------
 */
document.addEventListener("DOMContentLoaded", () => {
    const chatBox = document.getElementById("chat-box");
    if (!chatBox) return;

    window.conversationId = chatBox.dataset.conversationId;

    console.log("conversationId loaded:", window.conversationId);
    console.log("Subscribed to: conversation." + window.conversationId);

    window.Echo.private(`conversation.${window.conversationId}`)
        .listen(".message.sent", (event) => {  // <-- HARUS ADA TITIK
            console.log("Realtime message received:", event);

            const div = document.createElement("div");
            div.classList.add("message");
            div.innerHTML = `<b>${event.message.sender.name}</b>: ${event.message.message}`;

            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        });
});
