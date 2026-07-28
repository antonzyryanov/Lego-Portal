<?php

namespace App\Http\Requests\LegoSet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLegoSetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('set')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $set = $this->route('set');

        return [
            'series_id' => ['sometimes', 'required', 'exists:series,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'original_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'release_date' => ['sometimes', 'required', 'date'],
            'article_number' => [
                'sometimes',
                'required',
                'string',
                'max:64',
                Rule::unique('lego_sets', 'article_number')->ignore($set?->id),
            ],
            'image_path' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
