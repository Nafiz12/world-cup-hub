<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\WorldcupHistoryController;
use App\Http\Controllers\ChatController;
use App\Services\ApiFootball;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/world-cup-history', [WorldcupHistoryController::class, 'index']);

Route::post('/chat', [ChatController::class, 'chat']);        // main endpoint for your Vue widget
Route::get('/test-openai', [ChatController::class, 'test']);  // quick probe in browser

Route::get('/live-scores', function (Request $request) {
    $current_Date=  now()->format('Y-m-d');
    if($current_Date < '2026-07-19'){
        $start_date =$current_Date;

    }else{
        $start_date = '2026-07-19';
    }

    try {
        $limit = (int) ($request->query('limit', 3) ?: 3);

        $response = Http::timeout(20)
            ->get('https://api.fifa.com/api/v3/calendar/matches', [
                'language' => 'en',
                'from' => $start_date,
                'to' => '2026-07-19',
                'IdCompetition' => 17,
            ]);

//        print_r($response->json());  die; // Debugging line to inspect the API response structure
        $matches = collect($response->json('Results', []))->map(function (array $match) {
                $statusCode = (int) ($match['MatchStatus'] ?? 0);
                $statusLabel = match ($statusCode) {
                    0 => 'Just finished',
                    1 => 'Have not started',
                    2, 3 => 'Live now',
                    default => 'Fixture update pending',
                };

                return [
                    'id' => $match['IdMatch'] ?? null,
                    'match_number' => (int) ($match['MatchNumber'] ?? 0),
                    'stage_name' => $match['StageName'][0]['Description'] ?? 'FIFA World Cup',
                    'group_name' => $match['GroupName'][0]['Description'] ?? null,
                    'datetime' => $match['Date'] ?? null,
                    'home_team' => [
                        'name' => $match['Home']['TeamName'][0]['Description'] ?? 'TBD',
                        'goals' => $match['Home']['Score'] ?? 0,
                    ],
                    'away_team' => [
                        'name' => $match['Away']['TeamName'][0]['Description'] ?? 'TBD',
                        'goals' => $match['Away']['Score'] ?? 0,
                    ],
                    'home_score' => $match['Home']['Score'] ?? 0,
                    'away_score' => $match['Away']['Score'] ?? 0,
                    'status_code' => $statusCode,
                    'status_label' => $statusLabel,
                    'status' => $statusLabel,
                ];
            })
            ->sortBy(['match_number', 'datetime'])
            ->values()
            ->take($limit)
            ->all();

        return response()->json($matches, $response->status());
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Live scores are currently unavailable.',
            'message' => $e->getMessage(),
        ], 502);
    }
});

Route::get('/fixtures', function (Request $request) {
    try {
        $response = Http::timeout(20)
            ->get('https://api.fifa.com/api/v3/calendar/matches', [
                'language' => 'en',
                'from' => '2026-06-11',
                'to' => '2026-07-19',
                'IdCompetition' => 17,
                'count' => 500,
            ]);

        $matches = collect($response->json('Results', []))
            ->map(function (array $match) {
                $statusCode = (int) ($match['MatchStatus'] ?? 0);
                $statusLabel = match ($statusCode) {
                    0 => 'Just finished',
                    1 => 'Have not started',
                    2, 3 => 'Live now',
                    default => 'Fixture update pending',
                };

                return [
                    'id' => $match['IdMatch'] ?? null,
                    'match_number' => (int) ($match['MatchNumber'] ?? 0),
                    'stage_name' => $match['StageName'][0]['Description'] ?? 'FIFA World Cup',
                    'group_name' => $match['GroupName'][0]['Description'] ?? null,
                    'datetime' => $match['Date'] ?? null,
                    'home_team' => [
                        'name' => $match['Home']['TeamName'][0]['Description'] ?? 'TBD',
                        'goals' => $match['Home']['Score'] ?? 0,
                    ],
                    'away_team' => [
                        'name' => $match['Away']['TeamName'][0]['Description'] ?? 'TBD',
                        'goals' => $match['Away']['Score'] ?? 0,
                    ],
                    'home_score' => $match['Home']['Score'] ?? 0,
                    'away_score' => $match['Away']['Score'] ?? 0,
                    'status_code' => $statusCode,
                    'status_label' => $statusLabel,
                    'status' => $statusLabel,
                ];
            })
            ->sortBy(['match_number', 'datetime'])
            ->values()
            ->all();

        return response()->json($matches, $response->status());
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Fixtures are currently unavailable.',
            'message' => $e->getMessage(),
        ], 502);
    }
});

Route::get('test/worldcup/players', function (Request $request, ApiFootball $api) {
    try {
        $team = (int) $request->query('team', 26);
        $season = (int) $request->query('season', 2022);

        return response()->json($api->playersByTeamSeason($team, $season));
    } catch (\Illuminate\Http\Client\RequestException $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Third-party API is unavailable.',
            'source' => 'thirdparty',
        ], 502);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Backend server error.',
            'source' => 'backend',
        ], 500);
    }
});

Route::get('/__debug/config-key', function () {
    return response()->json([
        'config_services_apifootball' => config('services.apifootball'),
        'key_strlen' => strlen((string) config('services.apifootball.key')),
    ]);
});

Route::get('/worldcup/teams', function (\Illuminate\Http\Request $r, ApiFootball $api) {
    try {
        $league = (int) $r->query('league', 1);   // World Cup league id
        $season = (int) $r->query('season', 2022);

        $raw = $api->teamsByLeagueSeason($league, $season);
        $items = array_map(function ($row) {
            $t = $row['team'] ?? [];
            return [
                'id'   => $t['id']   ?? null,
                'name' => $t['name'] ?? null,
                'logo' => $t['logo'] ?? null,
            ];
        }, $raw['response'] ?? []);

        return response()->json([
            'ok'    => true,
            'count' => count($items),
            'teams' => $items,
        ]);
    } catch (\Illuminate\Http\Client\RequestException $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Third-party API is unavailable.',
            'source' => 'thirdparty',
        ], 502);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => 'Backend server error.',
            'source' => 'backend',
        ], 500);
    }
});

