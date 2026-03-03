<?php

namespace App\Http\Requests;

use App\Models\TravelRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $travelRequest = $this->route('travel_request');

        if (! $travelRequest instanceof TravelRequest) {
            return false;
        }

        return $this->user()?->can('update', $travelRequest) ?? false;
    }

    public function rules(): array
    {
        return [
            'destination' => ['sometimes', 'required', 'string', 'max:255'],
            'departure_date' => ['sometimes', 'required', 'date'],
            'return_date' => ['sometimes', 'required', 'date', 'after_or_equal:departure_date'],
            'status' => ['prohibited'],
            'requester_name' => ['prohibited'],
            'requester_id' => ['prohibited'],
        ];
    }
}
