<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreFolderRequest extends ParentIdBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                // dd($this),
                'name' => [
                    'required',
                    Rule::unique(File::class, 'name') // name이 UNIQUE한지 확인
                        ->where('created_by', Auth::id()) // 현재 사용자가 인증된 사용자인지 확인
                        ->where('parent_id', $this->parent_id) // WARN:오류: parent_id가 동일한지 확인
                        ->whereNull('deleted_at') // deleted_at이 null인지 확인
                ]
            ]
        );
    }

    public function messages()
    {
        return [
            'name.unique' => 'Folder ":input" already exists',
        ];
    }
}
