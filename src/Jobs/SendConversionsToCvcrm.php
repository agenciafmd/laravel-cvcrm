<?php

namespace Agenciafmd\Cvcrm\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cookie;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SendConversionsToCvcrm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function handle()
    {
        if (!config('laravel-cvcrm.token') || !config('laravel-cvcrm.email') || !config('laravel-cvcrm.url')) {
            return false;
        }

        $client = $this->getClientRequest();

        $data =  $this->data + [
            "permitir_alteracao" => true
            ];

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'email' => config('laravel-cvcrm.email'),
                'token' => config("laravel-cvcrm.token")
            ],
            'json' => $data,
        ];

        $client->request('POST', config('laravel-cvcrm.url').'api/cvio/lead', $options);
    }

    private function getClientRequest()
    {
        $logger = new Logger('CVCRM');
        $logger->pushHandler(new StreamHandler(storage_path('logs/cvcrm-' . date('Y-m-d') . '.log')));

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter("{method} {uri} HTTP/{version} {req_body} | RESPONSE: {code} - {res_body}")
            )
        );

        return new Client([
            'timeout' => 60,
            'connect_timeout' => 60,
            'http_errors' => false,
            'verify' => false,
            'handler' => $stack,
        ]);
    }
}
