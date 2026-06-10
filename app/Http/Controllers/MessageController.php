<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * GET /messages
     * Return the most recent messages, oldest first for display.
     */
    public function index()
    {
        return Message::query()
            ->latest()
            ->take(50)
            ->get()
            ->sortBy('id')
            ->values();
    }

    /**
     * POST /messages
     * Validate, persist, then announce the new message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create($validated);

        // A domain event already fires on every new message.
        //
        // Right now this event does NOT reach the browser. It is not broadcast
        // anywhere. Making it broadcast to all connected clients in real time
        // is the core of the task. See app/Events/MessageSent.php.
        MessageSent::dispatch($message);

        return response()->json($message, 201);
    }
}
