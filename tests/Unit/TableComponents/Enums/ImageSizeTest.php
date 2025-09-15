<?php

use App\TableComponents\Enums\ImageSize;

it('has all expected image size cases', function () {
    $expectedCases = ['Small', 'Medium', 'Large', 'ExtraLarge'];
    
    $actualCases = array_map(fn($case) => $case->name, ImageSize::cases());
    
    foreach ($expectedCases as $expected) {
        expect($actualCases)->toContain($expected);
    }
});

it('has correct Tailwind CSS size classes', function () {
    expect(ImageSize::Small->value)->toBe('size-6');
    expect(ImageSize::Medium->value)->toBe('size-8');
    expect(ImageSize::Large->value)->toBe('size-10');
    expect(ImageSize::ExtraLarge->value)->toBe('size-12');
});

it('can be used as string', function () {
    expect(ImageSize::Small->value)->toBe('size-6');
    expect(ImageSize::Medium->value)->toBe('size-8');
    expect(ImageSize::Large->value)->toBe('size-10');
    expect(ImageSize::ExtraLarge->value)->toBe('size-12');
});

it('has unique values', function () {
    $values = array_map(fn($case) => $case->value, ImageSize::cases());
    
    expect(count($values))->toBe(count(array_unique($values)));
});

it('can be created from valid string values', function () {
    expect(ImageSize::from('size-6'))->toBe(ImageSize::Small);
    expect(ImageSize::from('size-8'))->toBe(ImageSize::Medium);
    expect(ImageSize::from('size-10'))->toBe(ImageSize::Large);
    expect(ImageSize::from('size-12'))->toBe(ImageSize::ExtraLarge);
});

it('can try from string values', function () {
    expect(ImageSize::tryFrom('size-8'))->toBe(ImageSize::Medium);
    expect(ImageSize::tryFrom('invalid'))->toBeNull();
});

it('can be compared', function () {
    expect(ImageSize::Small)->toBe(ImageSize::Small);
    expect(ImageSize::Small)->not->toBe(ImageSize::Large);
});

it('sizes are in ascending order', function () {
    $sizes = [
        ImageSize::Small->value => 6,
        ImageSize::Medium->value => 8,
        ImageSize::Large->value => 10,
        ImageSize::ExtraLarge->value => 12,
    ];
    
    $previousSize = 0;
    foreach ($sizes as $class => $size) {
        expect($size)->toBeGreaterThan($previousSize);
        expect($class)->toStartWith('size-');
        $previousSize = $size;
    }
});

it('can be used in array contexts', function () {
    $sizes = [
        ImageSize::Small,
        ImageSize::Medium,
        ImageSize::Large,
        ImageSize::ExtraLarge,
    ];
    
    expect($sizes)->toHaveCount(4);
    expect($sizes)->toContain(ImageSize::Medium);
});

it('supports serialization', function () {
    $serialized = serialize(ImageSize::Large);
    $unserialized = unserialize($serialized);
    
    expect($unserialized)->toBe(ImageSize::Large);
});

it('supports json encoding', function () {
    $json = json_encode(ImageSize::Medium);
    $decoded = json_decode($json, true);
    
    expect($decoded)->toBe('size-8');
});

it('has valid Tailwind CSS classes', function () {
    // Test that all values are valid Tailwind size classes
    foreach (ImageSize::cases() as $size) {
        expect($size->value)->toMatch('/^size-\d+$/');
    }
});

it('provides reasonable size progression', function () {
    // Test that sizes make sense for table images
    $sizeMap = [
        'Small' => 'size-6',     // 24px
        'Medium' => 'size-8',    // 32px
        'Large' => 'size-10',    // 40px
        'ExtraLarge' => 'size-12' // 48px
    ];
    
    foreach (ImageSize::cases() as $size) {
        expect($sizeMap[$size->name])->toBe($size->value);
    }
});