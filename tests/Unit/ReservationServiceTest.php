<?php

use App\Services\ReservationService;

test('get available times returns correct time slots', function () {
    $times = ReservationService::getAvailableTimes();
    
    expect($times)->toBeArray()
        ->and($times)->toHaveCount(10)
        ->and($times)->toHaveKey('17:00')
        ->and($times['17:00'])->toBe('5:00 PM')
        ->and($times)->toHaveKey('21:30')
        ->and($times['21:30'])->toBe('9:30 PM');
});

test('get max party size returns correct value', function () {
    $maxSize = ReservationService::getMaxPartySize();
    
    expect($maxSize)->toBeInt()->toBe(20);
});

test('get phone validation rule returns correct format', function () {
    $rule = ReservationService::getPhoneValidationRule();
    
    expect($rule)->toBeArray()
        ->and($rule)->toContain('required')
        ->and($rule)->toContain('string')
        ->and($rule)->toContain('min:7')
        ->and($rule)->toContain('max:20');
});

test('get public date validation rule includes after today', function () {
    $rule = ReservationService::getPublicDateValidationRule();
    
    expect($rule)->toBeArray()
        ->and($rule)->toContain('required')
        ->and($rule)->toContain('date')
        ->and($rule)->toContain('after:today');
});

test('get admin date validation rule does not include after today', function () {
    $rule = ReservationService::getAdminDateValidationRule();
    
    expect($rule)->toBeArray()
        ->and($rule)->toContain('required')
        ->and($rule)->toContain('date')
        ->and($rule)->not->toContain('after:today');
});

test('get party size validation rule uses correct max', function () {
    $rule = ReservationService::getPartySizeValidationRule();
    
    expect($rule)->toBeArray()
        ->and($rule)->toContain('required')
        ->and($rule)->toContain('integer')
        ->and($rule)->toContain('min:1')
        ->and($rule)->toContain('max:20');
});

test('is during operating hours returns correct results', function () {
    expect(ReservationService::isDuringOperatingHours('17:00'))->toBeTrue();
    expect(ReservationService::isDuringOperatingHours('21:30'))->toBeTrue();
    expect(ReservationService::isDuringOperatingHours('16:59'))->toBeFalse();
    expect(ReservationService::isDuringOperatingHours('21:31'))->toBeFalse();
});

test('format phone number formats correctly', function () {
    expect(ReservationService::formatPhoneNumber('2025550100'))->toBe(' (202) 555-0100');
    expect(ReservationService::formatPhoneNumber('+12025550100'))->toBe('+1 (202) 555-0100');
    expect(ReservationService::formatPhoneNumber('5550100'))->toBe('5550100'); // No match, returns original
});
