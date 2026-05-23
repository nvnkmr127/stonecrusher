<?php

namespace App\Observers;

use App\Models\QuarryLabourSheet;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Services\DayClosureService;

class QuarryLabourSheetObserver
{
    public function saved(QuarryLabourSheet $sheet): void
    {
        if ($sheet->wasChanged('date')) {
            DayClosureService::checkAllowed($sheet->getOriginal('date'));
        }
        DayClosureService::checkAllowed($sheet->date);

        $tag = OperationalTag::firstOrCreate([
            'operational_unit_id' => $sheet->operational_unit_id,
            'name' => 'Labour',
            'type' => 'expense',
        ]);

        OperationalRecord::updateOrCreate(
            ['quarry_labour_sheet_id' => $sheet->id],
            [
                'operational_unit_id' => $sheet->operational_unit_id,
                'operational_tag_id' => $tag->id,
                'date' => $sheet->date,
                'quantity' => $sheet->no_of_workers,
                'rate' => $sheet->rate_per_worker,
                'amount' => $sheet->net_amount,
                'remarks' => "Auto-generated from Labour Sheet #{$sheet->id} (Workers: {$sheet->no_of_workers}, Rate: {$sheet->rate_per_worker})",
            ]
        );
    }

    public function deleted(QuarryLabourSheet $sheet): void
    {
        $record = OperationalRecord::where('quarry_labour_sheet_id', $sheet->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    public function restored(QuarryLabourSheet $sheet): void
    {
        $this->saved($sheet);
    }
}
