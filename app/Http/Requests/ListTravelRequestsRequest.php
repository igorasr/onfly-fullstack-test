<?php

namespace App\Http\Requests;

use App\Enums\TravelRequestStatus;
use App\Models\TravelRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTravelRequestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::enum(TravelRequestStatus::class)],
            'destination' => ['nullable', 'string', 'max:255'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
            'travel_from' => ['nullable', 'date'],
            'travel_to' => ['nullable', 'date', 'after_or_equal:travel_from'],
        ];
    }
}
