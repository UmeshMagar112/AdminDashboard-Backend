<?php
// app/Http/Requests/Review/StoreReviewRequest.php
namespace App\Http\Requests\Review;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'user_id'    => ['required', 'exists:users,id'],
            'order_id'   => ['nullable', 'exists:orders,id'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'title'      => ['nullable', 'string', 'max:191'],
            'body'       => ['nullable', 'string'],
            'status'     => ['nullable', 'integer', 'in:0,1,2'],
        ];
    }
}
