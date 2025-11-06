<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 一般ユーザーのIDを取得（山田花子、佐藤三郎）
        $users = DB::table('users')->where('is_admin', false)->get();

        foreach ($users as $user) {
            // 各ユーザーに10件の勤怠情報を作成
            for ($i = 0; $i < 10; $i++) {
                // 日付を過去10日分作成（今日から遡る）
                $date = Carbon::today()->subDays($i);
                
                // 出勤時間（9:00 ± ランダムで0-30分）
                $workStart = Carbon::create($date->year, $date->month, $date->day, 9, 0, 0)
                    ->addMinutes(rand(0, 30));
                
                // 退勤時間（18:00 ± ランダムで0-60分）
                $workStop = Carbon::create($date->year, $date->month, $date->day, 18, 0, 0)
                    ->addMinutes(rand(0, 60));
                
                // ステータス（退勤済み）
                $status = 'done';

                // 勤怠レコードを作成
                $attendanceId = DB::table('attendances')->insertGetId([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'work_start' => $workStart,
                    'work_stop' => $workStop,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 休憩時間を1-2回作成
                $breakCount = rand(1, 2);
                
                for ($j = 0; $j < $breakCount; $j++) {
                    // 休憩開始時間（12:00 + j時間 ± ランダムで0-30分）
                    $breakStart = Carbon::create($date->year, $date->month, $date->day, 12 + $j, 0, 0)
                        ->addMinutes(rand(0, 30));
                    
                    // 休憩終了時間（休憩開始 + 45-75分）
                    $breakStop = $breakStart->copy()->addMinutes(rand(45, 75));

                    DB::table('break_times')->insert([
                        'attendance_id' => $attendanceId,
                        'break_start' => $breakStart,
                        'break_stop' => $breakStop,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
