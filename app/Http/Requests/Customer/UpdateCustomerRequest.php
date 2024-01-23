<?php

namespace App\Http\Requests\Customer;

use App\Enums\SalesStateEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UpdateCustomerRequest extends FormRequest
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
            'sales_state' => ['nullable', 'string', 'in:' . implode(',', SalesStateEnum::names())],
            'sales_note' => 'nullable|string',
        ];
    }
}
