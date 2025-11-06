<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin()
    {
        return User::factory()->create([
            'is_admin' => 1,
        ]);
    }

    protected function createUser()
    {
        return User::factory()->create([
            'name' => 'テストユーザー',
            'is_admin' => 0,
        ]);
    }

    /**
     * 勤怠詳細画面に表示されるデータが選択したものになっている
     */
    public function test_admin_can_view_selected_attendance_detail()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $workStart = Carbon::parse('2025-10-15 09:00:00');
        $workStop = Carbon::parse('2025-10-15 18:00:00');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => $workStart,
            'work_stop' => $workStop,
            'date' => $workStart->toDateString(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSeeText('テストユーザー');
        // input要素の値を確認
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }

    /**
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_admin_cannot_update_with_invalid_clock_in_time()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::parse('09:00:00'),
            'work_stop' => Carbon::parse('18:00:00'),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->post('/admin/attendance/' . $attendance->id . '/update', [
            'work_start' => '19:00',
            'work_stop' => '18:00',
            'remarks' => '管理者による修正',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_admin_cannot_update_with_invalid_break_start_time()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::parse('09:00:00'),
            'work_stop' => Carbon::parse('18:00:00'),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->post('/admin/attendance/' . $attendance->id . '/update', [
            'work_start' => '09:00',
            'work_stop' => '18:00',
            'break_start' => ['19:00'],
            'break_stop' => ['20:00'],
            'remarks' => '管理者による修正',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_admin_cannot_update_with_invalid_break_stop_time()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::parse('09:00:00'),
            'work_stop' => Carbon::parse('18:00:00'),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->post('/admin/attendance/' . $attendance->id . '/update', [
            'work_start' => '09:00',
            'work_stop' => '18:00',
            'break_start' => ['12:00'],
            'break_stop' => ['19:00'],
            'remarks' => '管理者による修正',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * 備考欄が未入力の場合のエラーメッセージが表示される
     */
    public function test_admin_remarks_field_is_required()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::parse('09:00:00'),
            'work_stop' => Carbon::parse('18:00:00'),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->post('/admin/attendance/' . $attendance->id . '/update', [
            'work_start' => '09:30',
            'work_stop' => '18:30',
            'remarks' => '',
        ]);

        $response->assertSessionHasErrors('remarks');
    }
}

