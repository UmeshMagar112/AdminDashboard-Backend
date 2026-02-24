<?php
// app/Http/Requests/Customer/StoreCustomerRequest.php
namespace App\Http\Requests\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:191'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'avatar'   => ['nullable', 'string'],
            'status'   => ['nullable', 'boolean'],
        ];
    }
}
