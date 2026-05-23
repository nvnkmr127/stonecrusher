<?php

namespace App\Observers;

use App\Models\GatePass;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\Project;
use App\Enums\GatePassStatus;
use App\Enums\ActivityType;
use App\Services\DayClosureService;

class GatePassObserver
{
    /**
     * Handle the GatePass "saved" event.
     */
    public function saved(GatePass $gatePass): void
    {
        // Check if this gate pass is completed, is a sale, and is not an internal project
        $status = $gatePass->status instanceof GatePassStatus ? $gatePass->status->value : $gatePass->status;
        $activityType = $gatePass->activity_type instanceof ActivityType ? $gatePass->activity_type->value : $gatePass->activity_type;

        $isInternal = false;
        if ($gatePass->project_id) {
            $project = $gatePass->project ?? Project::find($gatePass->project_id);
            if ($project && $project->is_internal) {
                $isInternal = true;
            }
        }

        $isSale = ($status === GatePassStatus::COMPLETED->value && $activityType === ActivityType::SALES->value && !$isInternal);

        if ($isSale) {
            // Enforce day closure check
            if ($gatePass->wasChanged('date')) {
                DayClosureService::checkAllowed($gatePass->getOriginal('date'));
            }
            DayClosureService::checkAllowed($gatePass->date);

            if ($gatePass->source_unit_id) {
                $tag = OperationalTag::firstOrCreate([
                    'operational_unit_id' => $gatePass->source_unit_id,
                    'name' => 'Metal Sale',
                    'type' => 'revenue',
                ]);

                $quantity = $gatePass->loading_quantity > 0 ? $gatePass->loading_quantity : $gatePass->net_weight;
                $rate = $gatePass->rate_per_ton;
                $amount = $gatePass->total_amount;
                $remarks = "Auto-generated from Gate Pass #{$gatePass->gate_pass_number}";

                OperationalRecord::updateOrCreate(
                    ['gate_pass_id' => $gatePass->id],
                    [
                        'operational_unit_id' => $gatePass->source_unit_id,
                        'operational_tag_id' => $tag->id,
                        'date' => $gatePass->date,
                        'quantity' => $quantity,
                        'rate' => $rate,
                        'amount' => $amount,
                        'remarks' => $remarks,
                    ]
                );
            }
        } else {
            // If the gate pass is not a valid sale (e.g. status changed, type changed, or is internal),
            // remove the operational record if it exists.
            $record = OperationalRecord::where('gate_pass_id', $gatePass->id)->first();
            if ($record) {
                DayClosureService::checkAllowed($record->date);
                $record->delete();
            }
        }
    }

    /**
     * Handle the GatePass "deleted" event.
     */
    public function deleted(GatePass $gatePass): void
    {
        $record = OperationalRecord::where('gate_pass_id', $gatePass->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    /**
     * Handle the GatePass "restored" event.
     */
    public function restored(GatePass $gatePass): void
    {
        $this->saved($gatePass);
    }
}
