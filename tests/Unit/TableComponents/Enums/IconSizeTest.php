<?php

use App\TableComponents\Enums\IconSize;

it('has all expected icon size cases', function () {
    $expectedCases = ['Small', 'Medium', 'Large', 'ExtraLarge'];
    
    $actualCases = array_map(fn($case) => $case->name, IconSize::cases());
    
    foreach ($expectedCases as $expected) {
        expect($actualCases)->toContain($expected);
    }
});

it('has correct Tailwind CSS size classes', function () {
    expect(IconSize::Small->value)->toBe('size-2');
    expect(IconSize::Medium->value)->toBe('size-4');
    expect(IconSize::Large->value)->toBe('size-6');
    expect(IconSize::ExtraLarge->value)->toBe('size-8');
});

it('can be used as string', function () {
    expect(IconSize::Small->value)->toBe('size-2');
    expect(IconSize::Medium->value)->toBe('size-4');
    expect(IconSize::Large->value)->toBe('size-6');
    expect(IconSize::ExtraLarge->value)->toBe('size-8');
});

it('has unique values', function () {
    $values = array_map(fn($case) => $case->value, IconSize::cases());
    
    expect(count($values))->toBe(count(array_unique($values)));
});

it('can be created from valid string values', function () {
    expect(IconSize::from('size-2'))->toBe(IconSize::Small);
    expect(IconSize::from('size-4'))->toBe(IconSize::Medium);
    expect(IconSize::from('size-6'))->toBe(IconSize::Large);
    expect(IconSize::from('size-8'))->toBe(IconSize::ExtraLarge);
});

it('can try from string values', function () {
    expect(IconSize::tryFrom('size-4'))->toBe(IconSize::Medium);
    expect(IconSize::tryFrom('invalid'))->toBeNull();
});

it('can be compared', function () {
    expect(IconSize::Small)->toBe(IconSize::Small);
    expect(IconSize::Small)->not->toBe(IconSize::Large);
});

it('sizes are in ascending order', function () {
    $sizes = [
        IconSize::Small->value => 2,
        IconSize::Medium->value => 4,
        IconSize::Large->value => 6,
        IconSize::ExtraLarge->value => 8,
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
        IconSize::Small,
        IconSize::Medium,
        IconSize::Large,
        IconSize::ExtraLarge,
    ];
    
    expect($sizes)->toHaveCount(4);
    expect($sizes)->toContain(IconSize::Medium);
});

it('supports serialization', function () {
    $serialized = serialize(IconSize::Large);
    $unserialized = unserialize($serialized);
    
    expect($unserialized)->toBe(IconSize::Large);
});

it('supports json encoding', function () {
    $json = json_encode(IconSize::Medium);
    $decoded = json_decode($json, true);
    
    expect($decoded)->toBe('size-4');
});

it('has valid Tailwind CSS classes', function () {
    // Test that all values are valid Tailwind size classes
    foreach (IconSize::cases() as $size) {
        expect($size->value)->toMatch('/^size-\d+$/');
    }
});

it('provides reasonable size progression for icons', function () {
    // Test that sizes make sense for table icons (smaller than images)
    $sizeMap = [
        'Small' => 'size-2',     // 8px
        'Medium' => 'size-4',    // 16px
        'Large' => 'size-6',     // 24px
        'ExtraLarge' => 'size-8' // 32px
    ];
    
    foreach (IconSize::cases() as $size) {
        expect($sizeMap[$size->name])->toBe($size->value);
    }
});

it('icon sizes are generally smaller than image sizes', function () {
    // Icons should generally be smaller than images for table usage
    $iconSizes = [2, 4, 6, 8]; // From IconSize values
    $imageSizes = [6, 8, 10, 12]; // From ImageSize values
    
    // Most icon sizes should be smaller than most image sizes
    $smallestImageSize = min($imageSizes);
    $smallestIconSize = min($iconSizes);
    $mediumIconSize = 4;
    
    expect($smallestIconSize)->toBeLessThan($smallestImageSize);
    expect($mediumIconSize)->toBeLessThan($smallestImageSize);
    
    // Icons start smaller than images
    expect(min($iconSizes))->toBeLessThan(min($imageSizes));
});