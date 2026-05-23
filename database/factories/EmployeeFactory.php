<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'role' => 'operational',
            'base_salary' => 20000,
            'daily_rate' => 750,
            'is_active' => true,
        ];
    }
}
