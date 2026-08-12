<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommunityImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'image' => array_merge(
                $this->isMethod('post') ? ['required'] : ['nullable'],
                ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048']
            ),
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
