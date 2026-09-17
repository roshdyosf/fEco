<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Category::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $familyId = $this->user()?->family_id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->where(
                    fn ($query) => $query->where('family_id', $familyId),
                ),
            ],
            'type' => ['required', 'string', Rule::in(['expense', 'income'])],
        ];
    }
}
