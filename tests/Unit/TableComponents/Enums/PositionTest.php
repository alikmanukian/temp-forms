<?php

use App\TableComponents\Enums\Position;

it('has all expected position cases', function () {
    $expectedCases = ['Start', 'End'];
    
    $actualCases = array_map(fn($case) => $case->name, Position::cases());
    
    foreach ($expectedCases as $expected) {
        expect($actualCases)->toContain($expected);
    }
});

it('has correct position values', function () {
    expect(Position::Start->value)->toBe('start');
    expect(Position::End->value)->toBe('end');
});

it('can be used as string', function () {
    expect(Position::Start->value)->toBe('start');
    expect(Position::End->value)->toBe('end');
});

it('has unique values', function () {
    $values = array_map(fn($case) => $case->value, Position::cases());
    
    expect(count($values))->toBe(count(array_unique($values)));
});

it('can be created from valid string values', function () {
    expect(Position::from('start'))->toBe(Position::Start);
    expect(Position::from('end'))->toBe(Position::End);
});

it('can try from string values', function () {
    expect(Position::tryFrom('start'))->toBe(Position::Start);
    expect(Position::tryFrom('invalid'))->toBeNull();
});

it('can be compared', function () {
    expect(Position::Start)->toBe(Position::Start);
    expect(Position::Start)->not->toBe(Position::End);
});

it('has only two valid positions', function () {
    expect(Position::cases())->toHaveCount(2);
});

it('can be used in array contexts', function () {
    $positions = [Position::Start, Position::End];
    
    expect($positions)->toHaveCount(2);
    expect($positions)->toContain(Position::Start);
    expect($positions)->toContain(Position::End);
});

it('supports serialization', function () {
    $serialized = serialize(Position::End);
    $unserialized = unserialize($serialized);
    
    expect($unserialized)->toBe(Position::End);
});

it('supports json encoding', function () {
    $json = json_encode(Position::Start);
    $decoded = json_decode($json, true);
    
    expect($decoded)->toBe('start');
});

it('can be used for flex positioning', function () {
    // Test that positions make sense for flex layout
    expect(Position::Start->value)->toBe('start');
    expect(Position::End->value)->toBe('end');
    
    // These values should be valid CSS flex values
    $validFlexValues = ['start', 'end'];
    foreach (Position::cases() as $position) {
        expect($validFlexValues)->toContain($position->value);
    }
});

it('can be used for icon positioning', function () {
    // Test typical use case for icon positioning in components
    $iconPosition = Position::Start;
    expect($iconPosition->value)->toBe('start');
    
    $iconPosition = Position::End;
    expect($iconPosition->value)->toBe('end');
});