<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'work_start' => 'required|date_format:H:i',
            'work_stop' => 'required|date_format:H:i',
            'break_start' => 'nullable|array',
            'break_start.*' => 'nullable|date_format:H:i',
            'break_stop' => 'nullable|array',
            'break_stop.*' => 'nullable|date_format:H:i',
            'remarks' => 'required|string|max:255',
        ];
    }

    /**
     * カスタムバリデーション（時刻の整合性チェック）
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 基本バリデーションが通過している場合のみチェック
            if (!$validator->errors()->has('work_start') && !$validator->errors()->has('work_stop')) {
                $workStart = $this->work_start;
                $workStop = $this->work_stop;
                
                // 出勤時間が退勤時間より後の場合（または同じ時刻の場合）
                if ($workStart >= $workStop) {
                    $validator->errors()->add('work_stop', '出勤時間もしくは退勤時間が不適切な値です');
                }
            }

            // 休憩時間の配列を取得
            $breakStarts = $this->break_start ?? [];
            $breakStops = $this->break_stop ?? [];

            // 各休憩時間のチェック
            foreach ($breakStarts as $index => $breakStart) {
                if (isset($breakStops[$index])) {
                    $breakStop = $breakStops[$index];
                    
                    // 休憩開始時間と休憩終了時間のチェック
                    if (!empty($breakStart) && !empty($breakStop)) {
                        // 休憩開始時間が休憩終了時間より後の場合（または同じ時刻の場合）
                        if ($breakStart >= $breakStop) {
                            $validator->errors()->add("break_stop.{$index}", '休憩時間が不適切な値です');
                        }
                    }

                    // 休憩時間が勤務時間内にあるかチェック
                    if (!$validator->errors()->has('work_start') && 
                        !$validator->errors()->has('work_stop') && 
                        !empty($breakStart) && !empty($breakStop)) {
                        
                        $workStart = $this->work_start;
                        $workStop = $this->work_stop;
                        
                        // 休憩開始時間が出勤時間より前、または休憩開始時間が退勤時間より後の場合
                        if ($breakStart < $workStart || $breakStart > $workStop) {
                            $validator->errors()->add("break_start.{$index}", '休憩時間が不適切な値です');
                        }

                        // 休憩終了時間が退勤時間より後の場合
                        if ($breakStop > $workStop) {
                            $validator->errors()->add("break_stop.{$index}", '休憩時間もしくは退勤時間が不適切な値です');
                        }
                    }
                }
            }
        });
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages()
    {
        return [
            'work_start.required' => '出勤時間を入力してください',
            'work_start.date_format' => '出勤時間はHH:MM形式で入力してください',
            'work_stop.required' => '退勤時間を入力してください',
            'work_stop.date_format' => '退勤時間はHH:MM形式で入力してください',
            'break_start.array' => '休憩時間の形式が不正です',
            'break_start.*.date_format' => '休憩開始時間はHH:MM形式で入力してください',
            'break_stop.array' => '休憩時間の形式が不正です',
            'break_stop.*.date_format' => '休憩終了時間はHH:MM形式で入力してください',
            'remarks.required' => '備考を入力してください',
            'remarks.string' => '備考は文字列で入力してください',
            'remarks.max' => '備考は255文字以内で入力してください',
        ];
    }
}

