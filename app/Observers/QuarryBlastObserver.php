<?php

namespace App\Observers;

use App\Models\QuarryBlast;
use App\Models\QuarryBlastingMaterialUsed;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Services\DayClosureService;

class QuarryBlastObserver
{
    /**
     * Handle the QuarryBlast "saved" event.
     */
    public function saved(QuarryBlast $blast): void
    {
        if ($blast->wasChanged('date')) {
            DayClosureService::checkAllowed($blast->getOriginal('date'));
        }
        DayClosureService::checkAllowed($blast->date);

        $tag = OperationalTag::firstOrCreate([
            'operational_unit_id' => $blast->operational_unit_id,
            'name' => 'Blasting Materials',
            'type' => 'expense',
        ]);

        // Aggregate total cost of all materials used in this blast
        $totalCost = (float) QuarryBlastingMaterialUsed::where('quarry_blast_id', $blast->id)->sum('amount');

        OperationalRecord::updateOrCreate(
            ['quarry_blast_id' => $blast->id],
            [
                'operational_unit_id' => $blast->operational_unit_id,
                'operational_tag_id' => $tag->id,
                'date' => $blast->date,
                'quantity' => $blast->holes_blasted,
                'rate' => $blast->holes_blasted > 0 ? $totalCost / $blast->holes_blasted : $totalCost,
                'amount' => $totalCost,
                'remarks' => "Auto-generated from Blast Log #{$blast->blast_number} (Holes: {$blast->holes_blasted})",
            ]
        );
    }

    /**
     * Handle the QuarryBlast "deleted" event.
     */
    public function deleted(QuarryBlast $blast): void
    {
        $record = OperationalRecord::where('quarry_blast_id', $blast->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    /**
     * Handle the QuarryBlast "restored" event.
     */
    public function restored(QuarryBlast $blast): void
    {
        $this->saved($blast);
    }
}
