<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'username', 'string', 'max:255', Rule::unique('users')->ignore(Auth::user())],
            'password' => ['nullable', 'string', 'confirmed', 'min:6'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->password == null) {
            $this->request->remove('password');
        }
    }
}
