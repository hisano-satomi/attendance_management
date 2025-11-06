<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixesAttendanceRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fixes_attendance_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attendance_id',
        'work_start',
        'work_stop',
        'request_reason',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'work_start' => 'datetime',
        'work_stop' => 'datetime',
    ];

    /**
     * 勤怠記録とのリレーション
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * 休憩時間修正申請とのリレーション
     */
    public function fixesBreakRequests(): HasMany
    {
        return $this->hasMany(FixesBreakRequest::class);
    }
}
