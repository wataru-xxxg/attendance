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
            'startWork' => ['required', 'before:endWork', 'regex:/\A\d{2}:\d{2}\z/'],
            'endWork' => ['required', 'after:startWork', 'regex:/\A\d{2}:\d{2}\z/'],
            'breakStart.*' => ['nullable', 'required_with:breakEnd.*', 'after:startWork', 'before:endWork', function ($attribute, $value, $fail) {
                $index = str_replace('breakStart.', '', $attribute);
                $breakStart = $value;
                $breakEnd = $this->input('breakEnd.' . $index);
                if ($breakStart && $breakEnd && $breakStart >= $breakEnd) {
                    $fail('休憩時間が不適切な値です');
                }
            }, 'regex:/\A\d{2}:\d{2}\z/'],
            'breakEnd.*' => ['nullable', 'required_with:breakStart.*', 'before:endWork', 'after:startWork', function ($attribute, $value, $fail) {
                $index = str_replace('breakEnd.', '', $attribute);
                $breakStart = $this->input('breakStart.' . $index);
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
            'startWork.regex' => '出勤時間が不適切な値です',
            'endWork.regex' => '退勤時間が不適切な値です',
            'startWork.required' => '出勤時間は必須です。',
            'endWork.required' => '退勤時間は必須です。',
            'startWork.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'endWork.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breakStart.*.regex' => '休憩開始時間が不適切な値です',
            'breakStart.*.required_with' => '休憩開始時間は必須です',
            'breakStart.*.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breakStart.*.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'breakEnd.*.regex' => '休憩終了時間が不適切な値です',
            'breakEnd.*.required_with' => '休憩終了時間は必須です',
            'breakEnd.*.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breakEnd.*.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'notes.required' => '備考を記入してください',
        ];
    }
}
