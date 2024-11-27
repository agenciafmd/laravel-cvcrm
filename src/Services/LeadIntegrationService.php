<?php

namespace Agenciafmd\Cvcrm\Services;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Collection;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LeadIntegrationService
{
    public function __construct()
    {
    }

    private function getClientRequest(): Client
    {
        $logger = new Logger('laravel-cvcrm');
        $logger->pushHandler(new StreamHandler(storage_path('logs/laravel-cvcrm-' . date('Y-m-d') . '.log')));

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

    public function sendIntegrationResponse(array $data = []): Collection
    {
        if (!config('laravel-cvcrm.token') || !config('laravel-cvcrm.email') || !config('laravel-cvcrm.url')) {
            return collect([
                'sucesso' => false
            ]);
        }

        $client = $this->getClientRequest();

        $data = $data + [
                "permitir_alteracao" => true,
                "acao" => "salvar"
            ];

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'email' => config('laravel-cvcrm.email'),
                'token' => config("laravel-cvcrm.token")
            ],
            'json' => $data,
        ];

        $response = $client->request('POST', config('laravel-cvcrm.url') . '/api/cvio/lead', $options);

        $resp = collect(json_decode((string)$response->getBody()));

        if ($resp['codigo'] == 200) {
            return $resp;
        } else {
            return collect([
                'sucesso' => false
            ]);
        }
    }
}
