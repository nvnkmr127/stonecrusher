<?php

namespace App\Observers;

use App\Models\QuarrySecondaryBlasting;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Services\DayClosureService;

class QuarrySecondaryBlastingObserver
{
    public function saved(QuarrySecondaryBlasting $sb): void
    {
        if ($sb->wasChanged('date')) {
            DayClosureService::checkAllowed($sb->getOriginal('date'));
        }
        DayClosureService::checkAllowed($sb->date);

        $tag = OperationalTag::firstOrCreate([
            'operational_unit_id' => $sb->operational_unit_id,
            'name' => 'Secondary Blasting',
            'type' => 'expense',
        ]);

        OperationalRecord::updateOrCreate(
            ['quarry_secondary_blasting_id' => $sb->id],
            [
                'operational_unit_id' => $sb->operational_unit_id,
                'operational_tag_id' => $tag->id,
                'date' => $sb->date,
                'quantity' => $sb->no_of_holes,
                'rate' => $sb->no_of_holes > 0 ? $sb->amount / $sb->no_of_holes : $sb->amount,
                'amount' => $sb->net_amount,
                'remarks' => "Auto-generated from Secondary Blasting Log #{$sb->id} (Holes: {$sb->no_of_holes})",
            ]
        );
    }

    public function deleted(QuarrySecondaryBlasting $sb): void
    {
        $record = OperationalRecord::where('quarry_secondary_blasting_id', $sb->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    public function restored(QuarrySecondaryBlasting $sb): void
    {
        $this->saved($sb);
    }
}
