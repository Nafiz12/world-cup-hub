<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorldcupHistoryController;
use App\Http\Controllers\ChatController;
use App\Services\ApiFootball;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/world-cup-history', [WorldcupHistoryController::class, 'index']);

Route::post('/chat', [ChatController::class, 'chat']);        // main endpoint for your Vue widget
Route::get('/test-openai', [ChatController::class, 'test']);  // quick probe in browser

Route::get('test/worldcup/players',function (Request $request,ApiFootball $api){

        $team = (int) $request->query('team',26);
        $season = (int) $request->query('season',2022);

        return response()->json($api->playersByTeamSeason($team, $season));
});

Route::get('/__debug/config-key', function () {
    return response()->json([
        'config_services_apifootball' => config('services.apifootball'),
        'key_strlen' => strlen((string) config('services.apifootball.key')),
    ]);
});

Route::get('/worldcup/teams', function (\Illuminate\Http\Request $r, ApiFootball $api) {
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
});

