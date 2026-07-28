<?php

namespace App\Http\Requests\LegoSet;

use Illuminate\Foundation\Http\FormRequest;

class StoreLegoSetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\LegoSet::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'series_id' => ['required', 'exists:series,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'original_price' => ['required', 'numeric', 'min:0'],
            'release_date' => ['required', 'date'],
            'article_number' => ['required', 'string', 'max:64', 'unique:lego_sets,article_number'],
            'image_path' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
