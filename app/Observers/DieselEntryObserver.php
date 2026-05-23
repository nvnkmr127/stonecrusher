<?php

namespace App\Observers;

use App\Models\DieselEntry;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\Setting;
use App\Services\DayClosureService;

class DieselEntryObserver
{
    /**
     * Handle the DieselEntry "saved" event.
     */
    public function saved(DieselEntry $dieselEntry): void
    {
        // Enforce day closure check
        if ($dieselEntry->wasChanged('date')) {
            DayClosureService::checkAllowed($dieselEntry->getOriginal('date'));
        }
        DayClosureService::checkAllowed($dieselEntry->date);

        if ($dieselEntry->operational_unit_id) {
            $tag = OperationalTag::firstOrCreate([
                'operational_unit_id' => $dieselEntry->operational_unit_id,
                'name' => 'Diesel Used',
                'type' => 'expense',
            ]);

            $rate = Setting::get('default_diesel_rate', 100.00);
            $amount = $dieselEntry->liters * $rate;
            
            $vehicleNumber = $dieselEntry->vehicle ? $dieselEntry->vehicle->registration_number : 'N/A';
            $remarks = "Auto-generated from Diesel Issue (Vehicle: {$vehicleNumber}, Driver: {$dieselEntry->driver_name})";

            OperationalRecord::updateOrCreate(
                ['diesel_entry_id' => $dieselEntry->id],
                [
                    'operational_unit_id' => $dieselEntry->operational_unit_id,
                    'operational_tag_id' => $tag->id,
                    'date' => $dieselEntry->date,
                    'quantity' => $dieselEntry->liters,
                    'rate' => $rate,
                    'amount' => $amount,
                    'remarks' => $remarks,
                ]
            );
        } else {
            // Delete if operational unit was removed
            $record = OperationalRecord::where('diesel_entry_id', $dieselEntry->id)->first();
            if ($record) {
                DayClosureService::checkAllowed($record->date);
                $record->delete();
            }
        }
    }

    /**
     * Handle the DieselEntry "deleted" event.
     */
    public function deleted(DieselEntry $dieselEntry): void
    {
        $record = OperationalRecord::where('diesel_entry_id', $dieselEntry->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    /**
     * Handle the DieselEntry "restored" event.
     */
    public function restored(DieselEntry $dieselEntry): void
    {
        $this->saved($dieselEntry);
    }
}
