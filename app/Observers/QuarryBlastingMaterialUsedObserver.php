<?php

namespace App\Observers;

use App\Models\QuarryBlastingMaterialUsed;

class QuarryBlastingMaterialUsedObserver
{
    public function saved(QuarryBlastingMaterialUsed $material): void
    {
        if ($material->blast) {
            $material->blast->touch(); // forces QuarryBlast saved event to fire and sync totals
        }
    }

    public function deleted(QuarryBlastingMaterialUsed $material): void
    {
        if ($material->blast) {
            $material->blast->touch();
        }
    }
}
