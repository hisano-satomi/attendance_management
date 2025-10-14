<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixesBreakRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fixes_attendance_request_id',
        'break_start',
        'break_stop',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'break_start' => 'datetime',
        'break_stop' => 'datetime',
    ];

    /**
     * 修正申請とのリレーション
     */
    public function fixesAttendanceRequest(): BelongsTo
    {
        return $this->belongsTo(FixesAttendanceRequest::class);
    }
}
