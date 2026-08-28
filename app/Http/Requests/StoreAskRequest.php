<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Any logged-in user can create their own ask
        return true;
    }

    public function rules(): array
    {
        return [
            'for_name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:date,money,custom'],

            // Step 1
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'question' => ['required', 'string', 'max:255'],
            'accept_label' => ['required', 'string', 'max:40'],
            'decline_label' => ['required', 'string', 'max:40'],

            // Step 2 (optional)
            'has_scheduling_step' => ['required', 'boolean'],
            'scheduling_heading' => ['nullable', 'string', 'max:255'],
            'min_date' => ['nullable', 'date', 'required_if:has_scheduling_step,true'],
            'max_date' => ['nullable', 'date', 'after_or_equal:min_date', 'required_if:has_scheduling_step,true'],

            'venues' => ['nullable', 'array'],
            'venues.*.name' => ['required_with:venues', 'string', 'max:150'],
            'venues.*.address' => ['nullable', 'string', 'max:255'],

            // Step 3
            'final_heading' => ['nullable', 'string', 'max:120'],
            'final_message' => ['nullable', 'string', 'max:500'],

            'is_published' => ['sometimes', 'boolean'],

            // Share link, created alongside the ask
            'share_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'That link is already taken, please choose another one.',
            'max_date.after_or_equal' => 'The last possible date must be on or after the first possible date.',
        ];
    }
}
