<?php

namespace App\Observers;

use App\Models\ClientTransaction;
use App\Services\Dashboard\OwnerDashboardService;

class ClientTransactionObserver
{
    protected OwnerDashboardService $ownerDashboardService;

    public function __construct(OwnerDashboardService $ownerDashboardService)
    {
        $this->ownerDashboardService = $ownerDashboardService;
    }

    /**
     * Handle the ClientTransaction "saved" event.
     */
    public function saved(ClientTransaction $transaction): void
    {
        $this->ownerDashboardService->clearCache();
    }

    /**
     * Handle the ClientTransaction "deleted" event.
     */
    public function deleted(ClientTransaction $transaction): void
    {
        $this->ownerDashboardService->clearCache();
    }

    /**
     * Handle the ClientTransaction "restored" event.
     */
    public function restored(ClientTransaction $transaction): void
    {
        $this->ownerDashboardService->clearCache();
    }
}
