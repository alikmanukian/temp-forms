<?php

use App\TableComponents\Enums\ColumnAlignment;

it('has all expected alignment cases', function () {
    $expectedCases = ['Left', 'Center', 'Right'];
    
    $actualCases = array_map(fn($case) => $case->name, ColumnAlignment::cases());
    
    foreach ($expectedCases as $expected) {
        expect($actualCases)->toContain($expected);
    }
});

it('has correct CSS class values', function () {
    expect(ColumnAlignment::Left->value)->toBe('justify-start');
    expect(ColumnAlignment::Center->value)->toBe('justify-center');
    expect(ColumnAlignment::Right->value)->toBe('justify-end');
});

it('can be used as string', function () {
    expect(ColumnAlignment::Left->value)->toBe('justify-start');
    expect(ColumnAlignment::Center->value)->toBe('justify-center');
    expect(ColumnAlignment::Right->value)->toBe('justify-end');
});

it('has unique values', function () {
    $values = array_map(fn($case) => $case->value, ColumnAlignment::cases());
    
    expect(count($values))->toBe(count(array_unique($values)));
});

it('can be created from valid string values', function () {
    expect(ColumnAlignment::from('justify-start'))->toBe(ColumnAlignment::Left);
    expect(ColumnAlignment::from('justify-center'))->toBe(ColumnAlignment::Center);
    expect(ColumnAlignment::from('justify-end'))->toBe(ColumnAlignment::Right);
});

it('can try from string values', function () {
    expect(ColumnAlignment::tryFrom('justify-start'))->toBe(ColumnAlignment::Left);
    expect(ColumnAlignment::tryFrom('invalid'))->toBeNull();
});

it('can be compared', function () {
    expect(ColumnAlignment::Left)->toBe(ColumnAlignment::Left);
    expect(ColumnAlignment::Left)->not->toBe(ColumnAlignment::Right);
});

it('can be used in array contexts', function () {
    $alignments = [
        ColumnAlignment::Left,
        ColumnAlignment::Center,
        ColumnAlignment::Right,
    ];
    
    expect($alignments)->toHaveCount(3);
    expect($alignments)->toContain(ColumnAlignment::Center);
});

it('supports serialization', function () {
    $serialized = serialize(ColumnAlignment::Center);
    $unserialized = unserialize($serialized);
    
    expect($unserialized)->toBe(ColumnAlignment::Center);
});

it('supports json encoding', function () {
    $json = json_encode(ColumnAlignment::Right);
    $decoded = json_decode($json, true);
    
    expect($decoded)->toBe('justify-end');
});