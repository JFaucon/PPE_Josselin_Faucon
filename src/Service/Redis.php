<?php

namespace App\Service;

use Predis\Client;

class Redis
{
    public function getClient(): Client
    {


        return new Client([
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6379,
            'password' => 'redis_password'
        ]);
    }
}