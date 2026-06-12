<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveScoresRouteTest extends TestCase
{
    public function test_live_scores_route_returns_fifa_world_cup_2026_matches_only(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-11 12:00:00', 'UTC'));

        Http::fake([
            'https://api.fifa.com/api/v3/calendar/matches*' => Http::response([
                'Results' => [
                    [
                        'IdMatch' => '1',
                        'MatchNumber' => 1,
                        'Date' => '2026-06-11T19:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group A']],
                        'Home' => ['Score' => 2, 'TeamName' => [['Description' => 'Mexico']]],
                        'Away' => ['Score' => 0, 'TeamName' => [['Description' => 'South Africa']]],
                        'MatchStatus' => 0,
                    ],
                    [
                        'IdMatch' => '2',
                        'MatchNumber' => 2,
                        'Date' => '2026-06-12T02:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group A']],
                        'Home' => ['Score' => 2, 'TeamName' => [['Description' => 'Korea Republic']]],
                        'Away' => ['Score' => 1, 'TeamName' => [['Description' => 'Czechia']]],
                        'MatchStatus' => 0,
                    ],
                    [
                        'IdMatch' => '3',
                        'MatchNumber' => 3,
                        'Date' => '2026-06-12T19:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group B']],
                        'Home' => ['Score' => 0, 'TeamName' => [['Description' => 'Canada']]],
                        'Away' => ['Score' => 0, 'TeamName' => [['Description' => 'Bosnia and Herzegovina']]],
                        'MatchStatus' => 1,
                    ],
                    [
                        'IdMatch' => '4',
                        'MatchNumber' => 4,
                        'Date' => '2026-06-13T01:00:00Z',
                        'CompetitionName' => [['Description' => 'Premier League']],
                        'StageName' => [['Description' => 'League']],
                        'GroupName' => [['Description' => 'Group D']],
                        'Home' => ['Score' => 1, 'TeamName' => [['Description' => 'Team A']]],
                        'Away' => ['Score' => 1, 'TeamName' => [['Description' => 'Team B']]],
                        'MatchStatus' => 0,
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/live-scores?date=2026-06-11');

        $response->assertOk();
        $response->assertJsonCount(3);
        $response->assertJsonPath('0.match_number', 1);
        $response->assertJsonPath('0.home_team.name', 'Mexico');
        $response->assertJsonPath('0.away_team.name', 'South Africa');
        $response->assertJsonPath('0.stage_name', 'First Stage');
        $response->assertJsonMissing(['name' => 'Team A']);

        Carbon::setTestNow();
    }

    public function test_fixtures_route_returns_all_world_cup_matches(): void
    {
        Http::fake([
            'https://api.fifa.com/api/v3/calendar/matches*' => Http::response([
                'Results' => [
                    [
                        'IdMatch' => '1',
                        'MatchNumber' => 1,
                        'Date' => '2026-06-11T19:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group A']],
                        'Home' => ['Score' => 2, 'TeamName' => [['Description' => 'Mexico']]],
                        'Away' => ['Score' => 0, 'TeamName' => [['Description' => 'South Africa']]],
                        'MatchStatus' => 0,
                    ],
                    [
                        'IdMatch' => '2',
                        'MatchNumber' => 2,
                        'Date' => '2026-06-12T02:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group A']],
                        'Home' => ['Score' => 2, 'TeamName' => [['Description' => 'Korea Republic']]],
                        'Away' => ['Score' => 1, 'TeamName' => [['Description' => 'Czechia']]],
                        'MatchStatus' => 0,
                    ],
                    [
                        'IdMatch' => '3',
                        'MatchNumber' => 3,
                        'Date' => '2026-06-12T19:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group B']],
                        'Home' => ['Score' => 0, 'TeamName' => [['Description' => 'Canada']]],
                        'Away' => ['Score' => 0, 'TeamName' => [['Description' => 'Bosnia and Herzegovina']]],
                        'MatchStatus' => 1,
                    ],
                    [
                        'IdMatch' => '4',
                        'MatchNumber' => 4,
                        'Date' => '2026-06-13T01:00:00Z',
                        'CompetitionName' => [['Description' => 'FIFA World Cup™']],
                        'StageName' => [['Description' => 'First Stage']],
                        'GroupName' => [['Description' => 'Group D']],
                        'Home' => ['Score' => 1, 'TeamName' => [['Description' => 'USA']]],
                        'Away' => ['Score' => 1, 'TeamName' => [['Description' => 'Paraguay']]],
                        'MatchStatus' => 0,
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/fixtures');

        $response->assertOk();
        $response->assertJsonCount(4);
        $response->assertJsonPath('0.match_number', 1);
        $response->assertJsonPath('3.home_team.name', 'USA');
    }
}
