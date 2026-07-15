<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ask = $this->route('ask');

        return (int) $this->user()->id === (int) $ask->user_id;
    }

    public function rules(): array
    {
        $ask = $this->route('ask');

        return [
            'for_name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:date,money,custom'],

            'cover_image' => ['nullable', 'image', 'max:4096'],
            'question' => ['required', 'string', 'max:255'],
            'accept_label' => ['required', 'string', 'max:40'],
            'decline_label' => ['required', 'string', 'max:40'],

            'has_scheduling_step' => ['required', 'boolean'],
            'scheduling_heading' => ['nullable', 'string', 'max:255'],
            'min_date' => ['nullable', 'date', 'required_if:has_scheduling_step,true'],
            'max_date' => ['nullable', 'date', 'after_or_equal:min_date', 'required_if:has_scheduling_step,true'],

            'venues' => ['nullable', 'array'],
            'venues.*.id' => ['nullable', 'integer', 'exists:ask_venues,id'],
            'venues.*.name' => ['required_with:venues', 'string', 'max:150'],
            'venues.*.address' => ['nullable', 'string', 'max:255'],

            'final_heading' => ['nullable', 'string', 'max:120'],
            'final_message' => ['nullable', 'string', 'max:500'],

            'is_published' => ['sometimes', 'boolean'],
            'share_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
