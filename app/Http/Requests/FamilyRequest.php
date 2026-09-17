<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FamilyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->family_id === null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->routeIs('family.join')) {
            return [
                'invite_code' => ['required', 'string', 'max:32'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
