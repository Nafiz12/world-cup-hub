<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorldcupHistory;

class WorldCupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $worldCups = [
            ['year' => 1930, 'host' => 'Uruguay', 'winner' => 'Uruguay'],
            ['year' => 1934, 'host' => 'Italy', 'winner' => 'Italy'],
            ['year' => 1938, 'host' => 'France', 'winner' => 'Italy'],
            ['year' => 1950, 'host' => 'Brazil', 'winner' => 'Uruguay'],
            ['year' => 1954, 'host' => 'Switzerland', 'winner' => 'West Germany'],
            ['year' => 1958, 'host' => 'Sweden', 'winner' => 'Brazil'],
            ['year' => 1962, 'host' => 'Chile', 'winner' => 'Brazil'],
            ['year' => 1966, 'host' => 'England', 'winner' => 'England'],
            ['year' => 1970, 'host' => 'Mexico', 'winner' => 'Brazil'],
            ['year' => 1974, 'host' => 'West Germany', 'winner' => 'West Germany'],
            ['year' => 1978, 'host' => 'Argentina', 'winner' => 'Argentina'],
            ['year' => 1982, 'host' => 'Spain', 'winner' => 'Italy'],
            ['year' => 1986, 'host' => 'Mexico', 'winner' => 'Argentina'],
            ['year' => 1990, 'host' => 'Italy', 'winner' => 'West Germany'],
            ['year' => 1994, 'host' => 'USA', 'winner' => 'Brazil'],
            ['year' => 1998, 'host' => 'France', 'winner' => 'France'],
            ['year' => 2002, 'host' => 'Korea Republic/Japan', 'winner' => 'Brazil'],
            ['year' => 2006, 'host' => 'Germany', 'winner' => 'Italy'],
            ['year' => 2010, 'host' => 'South Africa', 'winner' => 'Spain'],
            ['year' => 2014, 'host' => 'Brazil', 'winner' => 'Germany'],
            ['year' => 2018, 'host' => 'Russia', 'winner' => 'France'],
            ['year' => 2022, 'host' => 'Qatar', 'winner' => 'Argentina'],
        ];

        foreach ($worldCups as $cup) {
            WorldcupHistory::create($cup);
        }
    }
}
