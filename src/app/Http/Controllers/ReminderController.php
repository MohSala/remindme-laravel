<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    // List reminders
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $reminders = Reminder::where('user_id', Auth::id())
            ->orderBy('remind_at', 'asc')
            ->limit($limit)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => [
                'reminders' => $reminders,
                'limit' => $limit
            ]
        ]);
    }

    // Create reminder
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'remind_at' => 'required|integer',
            'event_at' => 'required|integer'
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'remind_at' => $request->remind_at,
            'event_at' => $request->event_at,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $reminder
        ]);
    }

    // View reminder
    public function show($id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $reminder
        ]);
    }

    // Update reminder
    public function update(Request $request, $id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        $reminder->update($request->only(['title', 'description', 'remind_at', 'event_at']));

        return response()->json([
            'ok' => true,
            'data' => $reminder
        ]);
    }

    // Delete reminder
    public function destroy($id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);
        $reminder->delete();

        return response()->json([
            'ok' => true
        ]);
    }
}

