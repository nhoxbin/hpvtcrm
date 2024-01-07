<?php

namespace App\Http\Requests\Admin\Customer;

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
            'phoneNumber' => 'required|numeric',
            'productId' => 'required|numeric',
            'regisMethod' => 'required|in:otp,sms',
        ];
    }
}
