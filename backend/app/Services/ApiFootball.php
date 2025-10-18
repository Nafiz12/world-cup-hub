<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiFootball{


    protected string $base="";
    protected string $key="";


    public function __construct()
    {
        $this->base = rtrim((string) config('services.apifootball.base').'/');
        $this->key = (string) config('services.apifootball.key');


    }

    protected function get(string $path, array $query = []): array
    {


        if ($this->key === '') {
            throw new \RuntimeException('API-Football key is empty. Check .env APIFOOTBALL_KEY and run: php artisan config:clear');
        }

        $url = $this->base.ltrim($path,'/');


        $resp= http::withHeaders([
            'x-apisports-key' => $this->key,
            'accept'          => 'application/json',
        ])->get($url,$query)->throw();;

        return $resp->json()??[];
    }


    public function playersByTeamSeason(int $teamId, int $season): array
    {
        
        return $this->get('players', [
            'team'   => $teamId,
            'season' => $season,
        ]);
    }


    public function teamsByLeagueSeason(int $leagueId, int $season): array
    {
        return $this->get('teams', [
            'league' => $leagueId,
            'season' => $season,
        ]);
    }

}
