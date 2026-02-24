<?php
// app/Http/Requests/Review/UpdateReviewRequest.php
namespace App\Http\Requests\Review;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'title'  => ['nullable', 'string', 'max:191'],
            'body'   => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],
        ];
    }
}
