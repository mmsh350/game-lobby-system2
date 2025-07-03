<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PlayerSelection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // public function getCurrentSession()
    // {
    //     $session = GameSession::where('is_active', true)->first();

    //     if (!$session) {
    //         $session = $this->createNewSession();
    //     }

    //     return response()->json([
    //         'session' => $session,
    //         'time_left' => max(0, Carbon::now()->diffInSeconds(Carbon::parse($session->end_time), false))
    //     ]);
    // }
    // public function getCurrentSession()
    // {
    //     $session = GameSession::where('is_active', true)->first();

    //     $check_time = $session->end_time - carbon::now();

    //     if (!$session) {
    //         $session = $this->createNewSession();
    //     }
    //     if ($check_time < ) {
    //         $session = $this->createNewSession();
    //     }

    //     // // Calculate time left as integer seconds
    //     // $timeLeft = now()->diffInSeconds(Carbon::parse($session->end_time), false);
    //     // $timeLeft = max(0, $timeLeft);

    //     return response()->json([
    //         'session' => $session,
    //         // 'time_left' => $timeLeft
    //     ]);
    // }

    public function getCurrentSession()
    {
        $session = GameSession::where('is_active', true)->first();

        // If no active session exists, create a new one
        if (!$session) {
            $session = $this->createNewSession();
        }
        // If session exists but end time has passed, create a new one
        else if (Carbon::now()->greaterThan(Carbon::parse($session->end_time))) {
            $session = $this->createNewSession();
        }

        // Calculate time left in seconds (for the response if needed)
        $timeLeft = Carbon::now()->diffInSeconds(Carbon::parse($session->end_time), false);
        $timeLeft = max(0, $timeLeft);

        return response()->json([
            'session' => $session,
        ]);
    }

    public function joinSession(Request $request)
    {
        $request->validate(['selected_number' => 'required|integer|between:1,10']);

        $user = $request->user();
        $session = GameSession::where('is_active', true)->first();

        if (!$session) {
            return response()->json(['message' => 'No active session'], 400);
        }

        // Check if user already joined this session
        if (PlayerSelection::where('user_id', $user->id)
            ->where('game_session_id', $session->id)
            ->exists()
        ) {
            return response()->json(['message' => 'Already joined this session'], 400);
        }

        PlayerSelection::create([
            'user_id' => $user->id,
            'game_session_id' => $session->id,
            'selected_number' => $request->selected_number
        ]);

        return response()->json(['message' => 'Joined session successfully']);
    }

    public function getLeaderboard()
    {
        $topPlayers = User::orderBy('wins', 'desc')
            ->take(10)
            ->get(['username', 'wins']);

        return response()->json($topPlayers);
    }

    // private function createNewSession()
    // {
    //     // End any existing sessions
    //     GameSession::where('is_active', true)->update(['is_active' => false]);

    //     // Determine winners for ending sessions
    //     $this->determineWinners();

    //     $startTime = Carbon::now();
    //     $endTime = $startTime->copy()->addSeconds(20);

    //     return GameSession::create([
    //         'start_time' => $startTime,
    //         'end_time' => $endTime,
    //         'is_active' => true
    //     ]);
    // }
    public function createNewSession()
    {
        // End any existing sessions
        GameSession::where('is_active', true)->update(['is_active' => false]);

        // Determine winners for ending sessions
        $this->determineWinners();

        $startTime = Carbon::now()->setMicroseconds(0); // Remove microseconds
        $endTime = $startTime->copy()->addSeconds(20);

        return GameSession::create([
            'start_time' => $startTime->format('Y-m-d H:i:s'), // Explicit format
            'end_time' => $endTime->format('Y-m-d H:i:s'),    // Explicit format
            'is_active' => true
        ]);
    }
    private function determineWinners()
    {
        $sessions = GameSession::where('is_active', false)
            ->whereNull('winning_number')
            ->get();

        foreach ($sessions as $session) {
            $session->winning_number = rand(1, 10);
            $session->save();

            PlayerSelection::where('game_session_id', $session->id)
                ->where('selected_number', $session->winning_number)
                ->update(['is_winner' => true]);

            // Update user win counts
            $winnerIds = PlayerSelection::where('game_session_id', $session->id)
                ->where('is_winner', true)
                ->pluck('user_id');

            User::whereIn('id', $winnerIds)->increment('wins');
        }
    }
}
