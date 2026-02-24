<?php
// app/Http/Requests/Customer/UpdateCustomerRequest.php
namespace App\Http\Requests\Customer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:191'],
            'email'    => ['sometimes', 'email', 'unique:users,email,' . $this->route('id')],
            'password' => ['nullable', 'string', 'min:8'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'avatar'   => ['nullable', 'string'],
            'status'   => ['nullable', 'boolean'],
        ];
    }
}
