<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectRequest extends FormRequest
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
            'start_work' => ['required', 'before:end_work', 'regex:/\A\d{2}:\d{2}\z/'],
            'end_work' => ['required', 'after:start_work', 'regex:/\A\d{2}:\d{2}\z/'],
            'break_start.*' => ['nullable', 'required_with:break_end.*', 'after:start_work', 'before:end_work', function ($attribute, $value, $fail) {
                $index = str_replace('break_start.', '', $attribute);
                $breakStart = $value;
                $breakEnd = $this->input('break_end.' . $index);
                if ($breakStart && $breakEnd && $breakStart >= $breakEnd) {
                    $fail('休憩時間が不適切な値です');
                }
            }, 'regex:/\A\d{2}:\d{2}\z/'],
            'break_end.*' => ['nullable', 'required_with:break_start.*', 'before:end_work', 'after:start_work', function ($attribute, $value, $fail) {
                $index = str_replace('break_end.', '', $attribute);
                $breakStart = $this->input('break_start.' . $index);
                $breakEnd = $value;
                if ($breakStart && $breakEnd && $breakStart >= $breakEnd) {
                    $fail('休憩時間が不適切な値です');
                }
            }, 'regex:/\A\d{2}:\d{2}\z/'],
            'notes' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'start_work.regex' => '出勤時間が不適切な値です',
            'end_work.regex' => '退勤時間が不適切な値です',
            'start_work.required' => '出勤時間は必須です。',
            'end_work.required' => '退勤時間は必須です。',
            'start_work.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_work.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start.*.regex' => '休憩開始時間が不適切な値です',
            'break_start.*.required_with' => '休憩開始時間は必須です',
            'break_start.*.after' => '休憩時間が勤務時間外です',
            'break_start.*.before' => '休憩開始時間は退勤時間より前である必要があります',
            'break_end.*.regex' => '休憩終了時間が不適切な値です',
            'break_end.*.required_with' => '休憩終了時間は必須です',
            'break_end.*.after' => '休憩時間が勤務時間外です',
            'break_end.*.before' => '休憩時間が勤務時間外です',
            'notes.required' => '備考を記入してください',
        ];
    }
}
