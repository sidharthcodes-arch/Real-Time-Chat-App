# Real-time Chat - Coding Test

A small Laravel + Vue app. The UI and the message storage are already built.
Your job is to make it real-time.

## What you are building

A single shared chat room. Open the app in two browser windows. When you send a
message in one window, it should appear in the other window instantly, with no
refresh.

## What already works (do not rebuild this)

- A Vue 3 chat interface: name gate, message list, composer.
- Messages are saved to the database and loaded on page open. Send a message,
  reload the page, and your history is still there.
- REST endpoints for listing and creating messages.
- A `MessageSent` event that fires whenever a message is stored.

## What does NOT work yet (this is the task)

Real-time delivery. Right now a second browser only sees new messages after a
manual reload. The `MessageSent` event fires, but nothing reaches the browser
over a live connection.

Your task is to wire up real-time delivery over WebSockets so that a message
sent in one browser shows up in another browser immediately.

The two obvious places to work:

- `app/Events/MessageSent.php` (backend)
- `resources/js/echo.js` and `resources/js/components/ChatApp.vue` (frontend)

You will also need to make configuration and infrastructure decisions. Working
out what those are is part of the test.

## Requirements

Must work:

1. Send a message in browser A, it appears in browser B with no refresh.
2. Message history still persists across a page reload (keep this working).

Nice to have (optional, only if you have time):

- A "user is typing" indicator.
- An online-users list.
- Sensible behaviour if the socket drops and reconnects.

Not required: visual polish, login/auth, multiple rooms, deployment.

## Running the base app locally

Requirements: PHP 8.2+, Composer, Node 18+.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

Then run it (two terminals):

```bash
# terminal 1
php artisan serve

# terminal 2
npm run dev
```

Open http://localhost:8000 in two windows and confirm: sending works, and
history survives a reload. Cross-window live updates will not work yet. That is
your job.

Note: getting the real-time piece working will likely require one or more
additional processes beyond the two above. Figuring out which ones, and why, is
part of the task.

Database defaults to SQLite, no setup needed. The file lives at
`database/database.sqlite`. You can switch to MySQL or Postgres in `.env` if you
prefer, but you do not need to.

## What to hand back

1. The repo with your commit history. Commit as you go, not one big dump.
2. A short `NOTES.md` covering:
   - Which WebSocket / broadcasting approach you chose and why. What were the
     alternatives and the tradeoffs?
   - Every process that must be running for the app to work, and what each does.
   - The path of a message from a keypress in browser A to it rendering in
     browser B.
   - What you would change or add with more time.
3. A 2 to 3 minute screen recording of the two-browser test, or be ready to demo
   it live.

Keep `NOTES.md` short and direct. We care more about your reasoning and that it
works than about length.

## Ground rules

- Using AI tools to write code is fine and expected. You should still be able to
  explain every decision and every line.
- Do not commit secrets. Keep credentials in `.env`, which is gitignored.
- Time box: aim for a focused half-day. Functional beats perfect.
