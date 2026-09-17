<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        return [
            'type' => ['required', 'string', Rule::in(['expense', 'income'])],

            // Ensure category exists and belongs strictly to the user's family
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('family_id', $user->family_id);
                }),
            ],

            'amount' => ['required', 'numeric', 'min:0.01'],

            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
