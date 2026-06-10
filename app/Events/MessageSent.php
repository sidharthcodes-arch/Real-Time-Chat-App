<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/*
|------------------------------------------------------------------------------
| THE TASK LIVES HERE (and in resources/js).
|------------------------------------------------------------------------------
| This event is dispatched every time a message is stored, but right now it
| goes nowhere the browser can see.
|
| To make the app real-time, this event needs to broadcast on a channel that
| the frontend subscribes to. Things worth thinking about:
|
|   - which Laravel contract turns this into a broadcast event
|   - which channel it broadcasts on, and whether that channel should be
|     public, private, or presence
|   - the event name and payload the frontend will listen for
|   - whether the sender should receive its own broadcast, and how to avoid
|     rendering the same message twice
|   - which processes must be running for the broadcast to actually go out
|
| You are free to restructure this however you like. It does not have to stay
| an event.
|------------------------------------------------------------------------------
*/
class MessageSent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Message $message)
    {
    }
}
