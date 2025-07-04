<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PlayerSelection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function getCurrentSession()
    {
        $session = GameSession::where('is_active', true)->first();

        if (!$session) {
            $session = $this->createNewSession();
        }

        if ($session && Carbon::now()->greaterThan(Carbon::parse($session->end_time))) {
            $this->getSessionResults($session->id);
        }

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

    public function createNewSession()
    {

        $startTime = Carbon::now()->setMicroseconds(0);
        $endTime = $startTime->copy()->addSeconds(20);

        return GameSession::create([
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
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

            $winnerIds = PlayerSelection::where('game_session_id', $session->id)
                ->where('is_winner', true)
                ->pluck('user_id');

            User::whereIn('id', $winnerIds)->increment('wins');
        }
    }
    public function getSessionResults($sessionId)
    {
        GameSession::where('is_active', true)->update(['is_active' => false]);

        $this->determineWinners();

        $session = GameSession::with(['selections.user'])
            ->findOrFail($sessionId);

        $user = auth()->user();
        $userSelection = $session->selections()
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'won' => $userSelection->is_winner,
            'selected_number' => $userSelection->selected_number,
            'winning_number' => $session->winning_number,
            'session' => $session
        ]);
    }
}
