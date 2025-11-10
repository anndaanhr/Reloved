import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Create dummy Echo object immediately to prevent WebSocket connection attempts
// Since we're using 'log' driver (no Redis), Reverb is disabled
window.Echo = {
    channel: () => ({
        listen: () => ({ stop: () => {} }),
        subscribed: () => {},
        error: () => {},
    }),
    private: () => ({
        listen: () => ({ stop: () => {} }),
        subscribed: () => {},
        error: () => {},
    }),
    leave: () => {},
    disconnect: () => {},
};

// Only initialize real Echo if explicitly enabled via environment variable
// Set VITE_ENABLE_REVERB=true in .env to enable Reverb
if (import.meta.env.VITE_ENABLE_REVERB === 'true' && import.meta.env.VITE_REVERB_APP_KEY) {
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
        }).catch(() => {
            // If Pusher fails to load, keep dummy Echo
            console.warn('Pusher failed to load, using dummy Echo');
        });
    }).catch(() => {
        // If Echo fails to load, keep dummy Echo
        console.warn('Laravel Echo failed to load, using dummy Echo');
    });
}
