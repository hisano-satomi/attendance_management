<?php

namespace Database\Factories;

use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BreakTime::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 month', 'now');
        $breakStart = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 12:00:00', $date->format('Y-m-d') . ' 13:00:00');
        $breakStop = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 13:00:00', $date->format('Y-m-d') . ' 14:00:00');

        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $breakStart,
            'break_stop' => $breakStop,
        ];
    }
}

