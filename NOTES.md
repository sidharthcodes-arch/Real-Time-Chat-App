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

## Typing Indicator

When a user types, the frontend POSTs to `/typing` with their name. Laravel fires a `UserTyping` event which broadcasts on the `chat` channel. Other browsers receive the `user.typing` event via Echo and display "X is typing..." for 1 second. This approach goes through Laravel instead of using Echo whispers because whispers require presence channels.

## Online Users List

Implemented using Laravel cache and a public channel. When a user joins the chat, the frontend POSTs to `/join`. Laravel stores the name in cache and broadcasts an updated users list via `UsersOnlineUpdated` event. When a user closes the tab, a native `fetch` request with `keepalive: true` POSTs to `/leave` to ensure the request completes before the browser closes. The online list updates in real time across all connected browsers.

## Reconnection Handling

Echo uses pusher-js under the hood which handles automatic reconnection natively. We added a visual indicator in the UI — a green dot when connected and a red dot with "Reconnecting..." text when the connection drops. This uses Echo's connector bindings:
- `connected` → green dot
- `disconnected` / `connecting` → red dot

## What I Would Add With More Time

- **Message deduplication** — use socket IDs to skip the sender's own broadcast
- **Persistent online users** — current implementation uses cache which expires. A database backed approach would be more reliable.
- **Multiple rooms** — extend the channel system to support multiple chat rooms
- **Read receipts** — show when a message has been seen by other users