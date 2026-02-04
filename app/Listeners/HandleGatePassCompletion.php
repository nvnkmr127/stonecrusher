<?php

namespace App\Listeners;

use App\Events\GatePassCompleted;
use App\Services\SalesService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleGatePassCompletion
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SalesService $salesService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GatePassCompleted $event): void
    {
        $this->salesService->createOrUpdateTransaction($event->gatePass);
    }
}
