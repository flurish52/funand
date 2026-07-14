<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAskResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public form, anyone with the link can respond
        return true;
    }

    public function rules(): array
    {
        $ask = $this->route('ask'); // resolved Ask model, see routes/web.php

        return [
            'ask_venue_id' => ['nullable', 'integer', 'exists:ask_venues,id'],
            'responded_date' => ['nullable', 'date'],
            'responded_time' => ['nullable', 'date_format:H:i'],
            'decline_taps' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
