<?php
// app/Http/Requests/Order/UpdateOrderRequest.php
namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'status'             => ['sometimes', 'in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'],
            'payment_status'     => ['sometimes', 'in:unpaid,paid,partial,refunded'],
            'payment_method'     => ['nullable', 'in:cash_on_delivery,stripe,paypal,bank_transfer'],
            'payment_reference'  => ['nullable', 'string'],
            'shipping_name'      => ['sometimes', 'string'],
            'shipping_email'     => ['sometimes', 'email'],
            'shipping_phone'     => ['nullable', 'string'],
            'shipping_address'   => ['sometimes', 'string'],
            'shipping_city'      => ['sometimes', 'string'],
            'shipping_state'     => ['nullable', 'string'],
            'shipping_zip'       => ['nullable', 'string'],
            'shipping_country'   => ['sometimes', 'string'],
            'notes'              => ['nullable', 'string'],
            'status_comment'     => ['nullable', 'string'],
        ];
    }
}
