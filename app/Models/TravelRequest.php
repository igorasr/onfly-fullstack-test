<?php

namespace App\Models;

use App\Enums\TravelRequestStatus;
use App\Exceptions\InvalidStatusTransition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'status' => TravelRequestStatus::class,
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function setStatus(TravelRequestStatus $status): self
    {
        return $this->changeStatus($status);
    }

    private function changeStatus(TravelRequestStatus $newStatus): self
    {
        if (!$newStatus) {
            throw ValidationException::withMessages([
                'status' => "Status '{$newStatus->value}' inválido.",
            ]);
        }

        $current = $this->status;
        if (!$current->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransition($current->value, $newStatus->value);
        }

        $this->status = $newStatus;

        return $this;
    }
}
