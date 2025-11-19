<?php

namespace Agenciafmd\Cvcrm\Jobs;

use Agenciafmd\Cvcrm\Services\LeadIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendConversionsToCvcrm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function handle()
    {
        if (!config('laravel-cvcrm.token') || !config('laravel-cvcrm.email') || !config('laravel-cvcrm.url')) {
            return false;
        }

        $data = $this->data;

        $cvcrmService = new LeadIntegrationService();
        $cvcrmService->sendIntegrationResponse($data);
    }
}
