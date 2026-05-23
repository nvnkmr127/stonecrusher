<?php

namespace App\Http\Resources\Finance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfitLossResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'sales' => number_format($this->resource['sales'] ?? 0, 2, '.', ''),
            'crusher_expense' => number_format($this->resource['crusher_expense'] ?? 0, 2, '.', ''),
            'quarry_expense' => number_format($this->resource['quarry_expense'] ?? 0, 2, '.', ''),
            'labour' => number_format($this->resource['labour'] ?? 0, 2, '.', ''),
            'diesel' => number_format($this->resource['diesel'] ?? 0, 2, '.', ''),
            'other_expense' => number_format($this->resource['other_expense'] ?? 0, 2, '.', ''),
            'net_profit' => number_format($this->resource['net_profit'] ?? 0, 2, '.', ''),
        ];

        if (isset($this->resource['period'])) {
            $data['period'] = $this->resource['period'];
        }

        if (isset($this->resource['year'])) {
            $data['year'] = $this->resource['year'];
        }

        if (isset($this->resource['monthly_breakdown'])) {
            $data['monthly_breakdown'] = collect($this->resource['monthly_breakdown'])->map(function ($month) {
                return [
                    'month' => $month['month'],
                    'month_name' => $month['month_name'],
                    'sales' => number_format($month['sales'], 2, '.', ''),
                    'crusher_expense' => number_format($month['crusher_expense'], 2, '.', ''),
                    'quarry_expense' => number_format($month['quarry_expense'], 2, '.', ''),
                    'labour' => number_format($month['labour'], 2, '.', ''),
                    'diesel' => number_format($month['diesel'], 2, '.', ''),
                    'other_expense' => number_format($month['other_expense'], 2, '.', ''),
                    'net_profit' => number_format($month['net_profit'], 2, '.', ''),
                ];
            })->toArray();
        }

        return $data;
    }
}
