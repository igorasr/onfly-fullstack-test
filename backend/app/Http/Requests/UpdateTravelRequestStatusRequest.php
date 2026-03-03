<?php

namespace App\Http\Requests;

use App\Enums\TravelRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTravelRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $travelRequest = $this->route('travel_request');

        return $this->user()->can(
            'updateStatus',
            $travelRequest
        );
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(TravelRequestStatus::class)->only([
                    TravelRequestStatus::Approved,
                    TravelRequestStatus::Cancelled,
                ]),
            ],
        ];
    }
}
