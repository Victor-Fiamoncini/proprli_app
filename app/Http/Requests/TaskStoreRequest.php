<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'status' => 'required|in:OPEN,IN_PROGRESS,COMPLETED,REJECTED',
            'assigned_user_id' => 'required|integer|exists:users,id',
            'creator_user_id' => 'required|integer|exists:users,id',
        ];
    }
}
