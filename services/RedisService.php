<?php
namespace app\services;

use Predis\Client;

class RedisService
{

    protected static $redis;

    protected function __construct($server, array $parameters, array $options)
    {
        static::$$server = new Client($parameters, $options);
    }


    public static function getRedis()
    {
        $config = [
            'parameters' => [
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 6379,
            ],
            'options' => [
                'parameters' => [
                    #'password' => '37dde601c7de3b0a',
                    'password' => '123456',
                    'database' => 4,
                ]
            ]
        ];

        if (!static::$redis) {
            new static('redis', $config['parameters'], $config['options']);
        }
        return static::$redis;
    }
}