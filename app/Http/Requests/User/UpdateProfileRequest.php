<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', Rule::unique('users', 'phone')->ignore($userId)],
        ];
    }
}
