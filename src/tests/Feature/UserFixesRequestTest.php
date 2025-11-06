<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\FixesAttendanceRequest;
use Carbon\Carbon;

class UserFixesRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'is_admin' => 0,
        ]);
    }

    protected function createAttendance($user)
    {
        return Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::parse('09:00:00'),
            'work_stop' => Carbon::parse('18:00:00'),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);
    }

    /**
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_clock_in_time_cannot_be_after_clock_out_time()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/fixes_request', [
            'attendance_id' => $attendance->id,
            'work_start' => '19:00',
            'work_stop' => '18:00',
            'remarks' => '打刻忘れ',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_break_start_time_cannot_be_after_clock_out_time()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/fixes_request', [
            'attendance_id' => $attendance->id,
            'work_start' => '09:00',
            'work_stop' => '18:00',
            'break_start' => ['19:00'],
            'break_stop' => ['20:00'],
            'remarks' => '打刻忘れ',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_break_stop_time_cannot_be_after_clock_out_time()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/fixes_request', [
            'attendance_id' => $attendance->id,
            'work_start' => '09:00',
            'work_stop' => '18:00',
            'break_start' => ['12:00'],
            'break_stop' => ['19:00'],
            'remarks' => '打刻忘れ',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * 備考欄が未入力の場合のエラーメッセージが表示される
     */
    public function test_remarks_field_is_required()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/fixes_request', [
            'attendance_id' => $attendance->id,
            'work_start' => '09:00',
            'work_stop' => '18:00',
            'remarks' => '',
        ]);

        $response->assertSessionHasErrors('remarks');
    }

    /**
     * 修正申請処理が実行される
     */
    public function test_fixes_request_can_be_submitted()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/fixes_request', [
            'attendance_id' => $attendance->id,
            'work_start' => '09:30',
            'work_stop' => '18:30',
            'remarks' => '打刻忘れのため修正申請',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('fixes_attendance_requests', [
            'attendance_id' => $attendance->id,
            'request_reason' => '打刻忘れのため修正申請',
            'status' => 'pending',
        ]);
    }

    /**
     * 「承認待ち」にログインユーザーが行った申請が全て表示されていること
     */
    public function test_pending_requests_are_displayed()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '打刻忘れ',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSeeText('承認待ち');
        $response->assertSeeText('打刻忘れ');
    }

    /**
     * 「承認済み」に管理者が承認した修正申請が全て表示されている
     */
    public function test_approved_requests_are_displayed()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '打刻忘れ',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSeeText('承認済み');
        $response->assertSeeText('打刻忘れ');
    }

    /**
     * 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
     */
    public function test_detail_button_redirects_to_attendance_detail_from_request_list()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '打刻忘れ',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
    }
}

