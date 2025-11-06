<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 month', 'now');
        $workStart = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 09:00:00', $date->format('Y-m-d') . ' 10:00:00');
        $workStop = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 17:00:00', $date->format('Y-m-d') . ' 19:00:00');

        return [
            'user_id' => User::factory(),
            'date' => $date,
            'work_start' => $workStart,
            'work_stop' => $workStop,
            'status' => 'done', // enum型なので 'working', 'breaking', 'done' のいずれかを指定
        ];
    }
}

