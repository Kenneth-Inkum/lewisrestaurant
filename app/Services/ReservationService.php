<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ReservationService
{
    /**
     * Get available reservation time slots
     */
    public static function getAvailableTimes(): array
    {
        return [
            '17:00' => '5:00 PM',
            '17:30' => '5:30 PM',
            '18:00' => '6:00 PM',
            '18:30' => '6:30 PM',
            '19:00' => '7:00 PM',
            '19:30' => '7:30 PM',
            '20:00' => '8:00 PM',
            '20:30' => '8:30 PM',
            '21:00' => '9:00 PM',
            '21:30' => '9:30 PM',
        ];
    }

    /**
     * Get maximum party size allowed
     */
    public static function getMaxPartySize(): int
    {
        return 20;
    }

    /**
     * Get phone number validation rule
     */
    public static function getPhoneValidationRule(): array
    {
        return ['required', 'string', 'regex:/^[\+]?[1-9][\d]{0,15}$/', 'min:7', 'max:20'];
    }

    /**
     * Get date validation rule for public reservations
     */
    public static function getPublicDateValidationRule(): array
    {
        return ['required', 'date', 'after:today'];
    }

    /**
     * Get date validation rule for admin reservations
     */
    public static function getAdminDateValidationRule(): array
    {
        return ['required', 'date'];
    }

    /**
     * Get party size validation rule
     */
    public static function getPartySizeValidationRule(): array
    {
        return ['required', 'integer', 'min:1', 'max:' . self::getMaxPartySize()];
    }

    /**
     * Check if a time slot is during operating hours
     */
    public static function isDuringOperatingHours(string $time): bool
    {
        $operatingHours = [
            'start' => '17:00', // 5:00 PM
            'end' => '21:30',   // 9:30 PM
        ];

        return $time >= $operatingHours['start'] && $time <= $operatingHours['end'];
    }

    /**
     * Format phone number for display
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Basic phone formatting - can be enhanced based on requirements
        return preg_replace('/^(\+?1)?(\d{3})(\d{3})(\d{4})$/', '$1 ($2) $3-$4', $phone);
    }
}
