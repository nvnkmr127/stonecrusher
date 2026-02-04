<?php

namespace App\Listeners;

use App\Events\GatePassCancelled;
use App\Services\SalesService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleGatePassCancellation
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
    public function handle(GatePassCancelled $event): void
    {
        $this->salesService->cancelTransaction($event->gatePass);
    }
}
