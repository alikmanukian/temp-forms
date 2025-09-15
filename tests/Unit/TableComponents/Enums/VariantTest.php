<?php

use App\TableComponents\Enums\Variant;

it('has all expected variant cases', function () {
    $expectedCases = [
        'Default', 'Red', 'Yellow', 'Green', 
        'Blue', 'Indigo', 'Purple', 'Pink'
    ];
    
    $actualCases = array_map(fn($case) => $case->name, Variant::cases());
    
    foreach ($expectedCases as $expected) {
        expect($actualCases)->toContain($expected);
    }
});

it('has correct color values', function () {
    expect(Variant::Default->value)->toBe('default');
    expect(Variant::Red->value)->toBe('red');
    expect(Variant::Yellow->value)->toBe('yellow');
    expect(Variant::Green->value)->toBe('green');
    expect(Variant::Blue->value)->toBe('blue');
    expect(Variant::Indigo->value)->toBe('indigo');
    expect(Variant::Purple->value)->toBe('purple');
    expect(Variant::Pink->value)->toBe('pink');
});

it('can be used as string', function () {
    expect(Variant::Red->value)->toBe('red');
    expect(Variant::Default->value)->toBe('default');
    expect(Variant::Purple->value)->toBe('purple');
});

it('has unique values', function () {
    $values = array_map(fn($case) => $case->value, Variant::cases());
    
    expect(count($values))->toBe(count(array_unique($values)));
});

it('can be created from valid string values', function () {
    expect(Variant::from('red'))->toBe(Variant::Red);
    expect(Variant::from('default'))->toBe(Variant::Default);
    expect(Variant::from('purple'))->toBe(Variant::Purple);
});

it('can try from string values', function () {
    expect(Variant::tryFrom('blue'))->toBe(Variant::Blue);
    expect(Variant::tryFrom('invalid'))->toBeNull();
});

it('can be compared', function () {
    expect(Variant::Red)->toBe(Variant::Red);
    expect(Variant::Red)->not->toBe(Variant::Blue);
});

it('supports all color variants', function () {
    $colorVariants = [
        Variant::Default,
        Variant::Red,
        Variant::Yellow,
        Variant::Green,
        Variant::Blue,
        Variant::Indigo,
        Variant::Purple,
        Variant::Pink,
    ];
    
    expect($colorVariants)->toHaveCount(8);
    expect($colorVariants)->toContain(Variant::Green);
});

it('supports serialization', function () {
    $serialized = serialize(Variant::Blue);
    $unserialized = unserialize($serialized);
    
    expect($unserialized)->toBe(Variant::Blue);
});

it('supports json encoding', function () {
    $json = json_encode(Variant::Yellow);
    $decoded = json_decode($json, true);
    
    expect($decoded)->toBe('yellow');
});

it('can be used in badge contexts', function () {
    // Test that variants make sense for badge styling
    $badgeVariants = [Variant::Red, Variant::Green, Variant::Yellow];
    
    foreach ($badgeVariants as $variant) {
        expect($variant->value)->toBeString();
        expect(strlen($variant->value))->toBeGreaterThan(2);
    }
});