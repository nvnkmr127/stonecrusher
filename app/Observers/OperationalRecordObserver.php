<?php

namespace App\Observers;

use App\Models\OperationalRecord;
use App\Services\Crusher\CrusherProfitService;
use App\Services\Quarry\QuarryCostService;
use App\Services\Finance\ProfitLossService;
use App\Services\Dashboard\OwnerDashboardService;

class OperationalRecordObserver
{
    protected CrusherProfitService $profitService;
    protected QuarryCostService $costService;
    protected ProfitLossService $profitLossService;
    protected OwnerDashboardService $ownerDashboardService;

    public function __construct(
        CrusherProfitService $profitService,
        QuarryCostService $costService,
        ProfitLossService $profitLossService,
        OwnerDashboardService $ownerDashboardService
    ) {
        $this->profitService = $profitService;
        $this->costService = $costService;
        $this->profitLossService = $profitLossService;
        $this->ownerDashboardService = $ownerDashboardService;
    }

    /**
     * Handle the OperationalRecord "saved" event.
     */
    public function saved(OperationalRecord $record): void
    {
        $this->profitService->clearCache($record->operational_unit_id);
        $this->costService->clearCache($record->operational_unit_id);
        $this->profitLossService->clearCache();
        $this->ownerDashboardService->clearCache();
    }

    /**
     * Handle the OperationalRecord "deleted" event.
     */
    public function deleted(OperationalRecord $record): void
    {
        $this->profitService->clearCache($record->operational_unit_id);
        $this->costService->clearCache($record->operational_unit_id);
        $this->profitLossService->clearCache();
        $this->ownerDashboardService->clearCache();
    }

    /**
     * Handle the OperationalRecord "restored" event.
     */
    public function restored(OperationalRecord $record): void
    {
        $this->profitService->clearCache($record->operational_unit_id);
        $this->costService->clearCache($record->operational_unit_id);
        $this->profitLossService->clearCache();
        $this->ownerDashboardService->clearCache();
    }
}
