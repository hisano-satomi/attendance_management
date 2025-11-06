<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\FixesAttendanceRequest;
use App\Models\FixesBreakRequest;
use Carbon\Carbon;

class AdminFixesRequestTest extends TestCase
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
            'is_admin' => 0,
        ]);
    }

    /**
     * 承認待ちの修正申請が全て表示されている
     */
    public function test_admin_can_view_all_pending_requests()
    {
        $admin = $this->createAdmin();
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        FixesAttendanceRequest::create([
            'attendance_id' => $attendance1->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => 'ユーザー1の申請',
            'status' => 'pending',
        ]);

        FixesAttendanceRequest::create([
            'attendance_id' => $attendance2->id,
            'work_start' => Carbon::parse('10:00:00'),
            'work_stop' => Carbon::parse('19:00:00'),
            'request_reason' => 'ユーザー2の申請',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSeeText('ユーザー1の申請');
        $response->assertSeeText('ユーザー2の申請');
    }

    /**
     * 承認済みの修正申請が全て表示されている
     */
    public function test_admin_can_view_all_approved_requests()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '承認済み申請',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSeeText('承認済み申請');
    }

    /**
     * 修正申請の詳細内容が正しく表示されている
     */
    public function test_admin_can_view_request_detail()
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

        $request = FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '打刻忘れのため修正',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/approve/' . $request->id);

        $response->assertStatus(200);
        $response->assertSeeText('09:30');
        $response->assertSeeText('18:30');
        $response->assertSeeText('打刻忘れのため修正');
    }

    /**
     * 修正申請の承認処理が正しく行われる
     */
    public function test_admin_can_approve_request()
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

        $request = FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '打刻忘れ',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post('/admin/stamp_correction_request/approve/' . $request->id);

        $response->assertRedirect();

        $this->assertDatabaseHas('fixes_attendance_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        // 勤怠データも更新されていることを確認
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
        ]);
    }

    /**
     * 一般ユーザーは修正申請の承認ができない
     */
    public function test_regular_user_cannot_approve_request()
    {
        $user = $this->createUser();
        $otherUser = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $otherUser->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $request = FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => Carbon::parse('09:30:00'),
            'work_stop' => Carbon::parse('18:30:00'),
            'request_reason' => '打刻忘れ',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post('/admin/stamp_correction_request/approve/' . $request->id);

        $response->assertStatus(403);
    }

    /**
     * 一般ユーザーは管理者用申請一覧にアクセスできない
     */
    public function test_regular_user_cannot_access_admin_request_list()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/admin/stamp_correction_request/list');

        $response->assertStatus(403);
    }
}

