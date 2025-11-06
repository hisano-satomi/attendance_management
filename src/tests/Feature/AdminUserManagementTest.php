<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin()
    {
        return User::factory()->create([
            'is_admin' => 1,
        ]);
    }

    protected function createUser($name = 'テストユーザー', $email = 'test@example.com')
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'is_admin' => 0,
        ]);
    }

    /**
     * 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
     */
    public function test_admin_can_view_all_users_information()
    {
        $admin = $this->createAdmin();
        $user1 = $this->createUser('山田太郎', 'yamada@example.com');
        $user2 = $this->createUser('佐藤花子', 'sato@example.com');

        $response = $this->actingAs($admin)->get('/admin/staff/list');

        $response->assertStatus(200);
        $response->assertSeeText('山田太郎');
        $response->assertSeeText('yamada@example.com');
        $response->assertSeeText('佐藤花子');
        $response->assertSeeText('sato@example.com');
    }

    /**
     * ユーザーの勤怠情報が正しく表示される
     */
    public function test_admin_can_view_user_attendance_information()
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

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $user->id);

        $response->assertStatus(200);
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    /**
     * 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_admin_can_view_previous_month_user_attendance()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $lastMonth = Carbon::now()->subMonth();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $lastMonth,
            'date' => $lastMonth,
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $user->id . '?year=' . $lastMonth->year . '&month=' . $lastMonth->month);

        $response->assertStatus(200);
        $response->assertSeeText($lastMonth->format('Y/m'));
    }

    /**
     * 「翌月」を押下した時に表示月の翌月の情報が表示される
     */
    public function test_admin_can_view_next_month_user_attendance()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $nextMonth = Carbon::now()->addMonth();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $nextMonth,
            'date' => $nextMonth,
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $user->id . '?year=' . $nextMonth->year . '&month=' . $nextMonth->month);

        $response->assertStatus(200);
        $response->assertSeeText($nextMonth->format('Y/m'));
    }

    /**
     * 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     */
    public function test_admin_can_access_detail_from_user_attendance_list()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/' . $attendance->id);

        $response->assertStatus(200);
    }

    /**
     * 一般ユーザーはユーザー管理画面にアクセスできない
     */
    public function test_regular_user_cannot_access_user_management()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/admin/staff/list');

        $response->assertStatus(403);
    }
}

