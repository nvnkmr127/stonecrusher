<?php

namespace App\Observers;

use App\Models\QuarryDrillingLog;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Services\DayClosureService;

class QuarryDrillingLogObserver
{
    public function saved(QuarryDrillingLog $log): void
    {
        if ($log->wasChanged('date')) {
            DayClosureService::checkAllowed($log->getOriginal('date'));
        }
        DayClosureService::checkAllowed($log->date);

        $tag = OperationalTag::firstOrCreate([
            'operational_unit_id' => $log->operational_unit_id,
            'name' => 'Borewells',
            'type' => 'expense',
        ]);

        OperationalRecord::updateOrCreate(
            ['quarry_drilling_log_id' => $log->id],
            [
                'operational_unit_id' => $log->operational_unit_id,
                'operational_tag_id' => $tag->id,
                'date' => $log->date,
                'quantity' => $log->total_feet,
                'rate' => $log->rate_per_foot,
                'amount' => $log->net_amount,
                'remarks' => "Auto-generated from Drilling Log #{$log->id} (Holes: {$log->no_of_holes}, Feet: {$log->total_feet})",
            ]
        );
    }

    public function deleted(QuarryDrillingLog $log): void
    {
        $record = OperationalRecord::where('quarry_drilling_log_id', $log->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    public function restored(QuarryDrillingLog $log): void
    {
        $this->saved($log);
    }
}
