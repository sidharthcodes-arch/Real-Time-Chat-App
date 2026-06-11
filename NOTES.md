# Notes

## WebSocket / Broadcasting Approach

I chose **Laravel Reverb** as the WebSocket server.

**Alternatives considered:**
- **Pusher** — external managed service, requires an account and has usage limits on the free tier. Adds an external dependency.
- **Soketi** — self-hosted, Pusher-compatible, but requires more setup and configuration.
- **Reverb** — first-party Laravel package, free, runs locally, zero external dependencies, minimal configuration. Best fit for this task.

## Processes Required

Three processes must be running simultaneously:

1. `php artisan serve` — Laravel HTTP server on port 8000. Handles REST API requests, saves messages to DB, fires broadcast events.
2. `npm run dev` — Vite dev server. Compiles and serves Vue/JS files to the browser.
3. `php artisan reverb:start` — Reverb WebSocket server on port 8080. Manages persistent browser connections and forwards broadcast events to connected clients.

## Message Path — Browser A to Browser B

1. User types a message and hits Send in Browser A
2. Vue calls `axios.post('/messages')` → Laravel saves the message to the database
3. Laravel fires the `MessageSent` event
4. `MessageSent` implements `ShouldBroadcast` → Laravel POSTs the event payload to Reverb
5. Reverb receives the payload and looks up all browsers subscribed to the `chat` channel
6. Reverb pushes the payload over the open WebSocket connection to Browser B
7. Laravel Echo in Browser B receives the `message.sent` event
8. Vue pushes the message into the `messages` array → UI updates instantly

## What I Would Add With More Time

- **Typing indicator** — we can broadcast an  event such that  when the user is typing, show it in the UI
- **Online users list** — we can do something  to track and display who is currently online
- **Message deduplication** — currently the sender relies on the WebSocket broadcast to see their own message. A more robust approach would be to use something else as of now  I don't know ,what it would be but , I would love to find out and implement .