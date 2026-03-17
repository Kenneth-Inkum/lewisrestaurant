<?php

namespace App\Models;

use App\Enums\TableStatus;
use Database\Factories\RestaurantTableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    /** @use HasFactory<RestaurantTableFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'capacity',
        'section',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => TableStatus::class,
            'capacity' => 'integer',
            'number' => 'integer',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrder(): mixed
    {
        return $this->hasOne(Order::class)->whereNotIn('status', ['delivered', 'cancelled'])->latest();
    }

    public function scopeAvailable($query): mixed
    {
        return $query->where('status', TableStatus::Available->value);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? "Table {$this->number}";
    }
}
