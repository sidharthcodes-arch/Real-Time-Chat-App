/*
|------------------------------------------------------------------------------
| Real-time client setup (Laravel Echo)
|------------------------------------------------------------------------------
| This is intentionally not wired up.
|
| Once you have chosen and configured a broadcasting driver on the backend,
| configure Echo here and uncomment the `import './echo'` line in app.js.
|
| laravel-echo and pusher-js are already installed (see package.json). Most
| self-hosted and managed options (Reverb, Pusher, Soketi, Ably) speak the
| pusher protocol, so the shape below is a starting point. Adjust it to the
| driver you pick. All connection values must come from VITE_-prefixed env
| vars, never hardcoded.
|
| Example for Laravel Reverb:
|
| import Echo from 'laravel-echo';
| import Pusher from 'pusher-js';
|
| window.Pusher = Pusher;
|
| window.Echo = new Echo({
|     broadcaster: 'reverb',
|     key: import.meta.env.VITE_REVERB_APP_KEY,
|     wsHost: import.meta.env.VITE_REVERB_HOST,
|     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
|     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
|     forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
|     enabledTransports: ['ws', 'wss'],
| });
*/

export {};
