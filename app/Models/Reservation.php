<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'party_size',
        'reservation_date',
        'reservation_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'reservation_date' => 'date',
            'party_size' => 'integer',
        ];
    }

    public function scopeUpcoming($query): mixed
    {
        return $query->where('reservation_date', '>=', today())
            ->whereIn('status', [ReservationStatus::Pending->value, ReservationStatus::Confirmed->value])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time');
    }

    public function scopeToday($query): mixed
    {
        return $query->where('reservation_date', today())
            ->orderBy('reservation_time');
    }
}
