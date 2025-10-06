<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiFootball{


    protected string $base;
    protected string $key;

    public function _construct()
    {
        $this->base = rtrim(config('service.api.football.base').'/');
        $this->key= (string) config('services.apifootball.key');
    }

    protected function get(){
        
    }

}
