<?php

use App\TableComponents\Enums\Clause;

it('has all expected clause types', function () {
    $expectedClauses = [
        'Equals', 'DoesNotEqual', 'Contains', 'DoesNotContain',
        'StartsWith', 'DoesNotStartWith', 'EndsWith', 'DoesNotEndWith',
        'IsIn', 'IsNotIn', 'IsSet', 'IsNotSet', 'IsTrue', 'IsFalse',
        'After', 'Before', 'Between'
    ];
    
    $actualClauses = array_map(fn($clause) => $clause->name, Clause::cases());
    
    foreach ($expectedClauses as $expected) {
        expect($actualClauses)->toContain($expected);
    }
});

it('can convert to array with correct structure', function () {
    $clause = Clause::Contains;
    $array = $clause->toArray();
    
    expect($array)
        ->toHaveKey('name', 'Contains')
        ->toHaveKey('searchSymbol', 'contains')
        ->toHaveKey('value', 'contains')
        ->toHaveKey('prefix', '');
});

it('can find clause by search symbol', function () {
    $clause = Clause::findBySearchSymbol('contains');
    
    expect($clause)->toBe(Clause::Contains);
});

it('returns null for invalid search symbol', function () {
    $clause = Clause::findBySearchSymbol('invalid');
    
    expect($clause)->toBeNull();
});

it('can get correct search symbols', function () {
    expect(Clause::Contains->toArray()['searchSymbol'])->toBe('contains');
    expect(Clause::DoesNotContain->toArray()['searchSymbol'])->toBe('not.contains');
    expect(Clause::StartsWith->toArray()['searchSymbol'])->toBe('starts');
    expect(Clause::DoesNotStartWith->toArray()['searchSymbol'])->toBe('not.starts');
    expect(Clause::EndsWith->toArray()['searchSymbol'])->toBe('ends');
    expect(Clause::DoesNotEndWith->toArray()['searchSymbol'])->toBe('not.ends');
    expect(Clause::Equals->toArray()['searchSymbol'])->toBe('');
    expect(Clause::DoesNotEqual->toArray()['searchSymbol'])->toBe('not');
    expect(Clause::IsIn->toArray()['searchSymbol'])->toBe('in');
    expect(Clause::IsNotIn->toArray()['searchSymbol'])->toBe('not.in');
    expect(Clause::IsSet->toArray()['searchSymbol'])->toBe('not.null');
    expect(Clause::IsNotSet->toArray()['searchSymbol'])->toBe('null');
    expect(Clause::IsTrue->toArray()['searchSymbol'])->toBe('true');
    expect(Clause::IsFalse->toArray()['searchSymbol'])->toBe('false');
    expect(Clause::After->toArray()['searchSymbol'])->toBe('after');
    expect(Clause::Before->toArray()['searchSymbol'])->toBe('before');
    expect(Clause::Between->toArray()['searchSymbol'])->toBe('between');
});

it('can get correct display names', function () {
    expect(Clause::Contains->toArray()['name'])->toBe('Contains');
    expect(Clause::DoesNotContain->toArray()['name'])->toBe('Does Not Contain');
    expect(Clause::StartsWith->toArray()['name'])->toBe('Starts With');
    expect(Clause::DoesNotStartWith->toArray()['name'])->toBe('Does Not Start With');
    expect(Clause::EndsWith->toArray()['name'])->toBe('Ends With');
    expect(Clause::DoesNotEndWith->toArray()['name'])->toBe('Does Not End With');
    expect(Clause::Equals->toArray()['name'])->toBe('Equals');
    expect(Clause::DoesNotEqual->toArray()['name'])->toBe('Does Not Equal');
    expect(Clause::IsIn->toArray()['name'])->toBe('Is In');
    expect(Clause::IsNotIn->toArray()['name'])->toBe('Is Not In');
    expect(Clause::IsSet->toArray()['name'])->toBe('Is Set');
    expect(Clause::IsNotSet->toArray()['name'])->toBe('Is Not Set');
    expect(Clause::IsTrue->toArray()['name'])->toBe('Is True');
    expect(Clause::IsFalse->toArray()['name'])->toBe('Is False');
    expect(Clause::After->toArray()['name'])->toBe('After');
    expect(Clause::Before->toArray()['name'])->toBe('Before');
    expect(Clause::Between->toArray()['name'])->toBe('Between');
});

it('can get correct prefixes', function () {
    expect(Clause::Contains->toArray()['prefix'])->toBe('');
    expect(Clause::DoesNotContain->toArray()['prefix'])->toBe('Not Contains');
    expect(Clause::StartsWith->toArray()['prefix'])->toBe('Starts with');
    expect(Clause::DoesNotStartWith->toArray()['prefix'])->toBe('Not Starts with');
    expect(Clause::EndsWith->toArray()['prefix'])->toBe('Ends with');
    expect(Clause::DoesNotEndWith->toArray()['prefix'])->toBe('Not ends with');
    expect(Clause::Equals->toArray()['prefix'])->toBe('');
    expect(Clause::DoesNotEqual->toArray()['prefix'])->toBe('Not');
    expect(Clause::IsIn->toArray()['prefix'])->toBe('');
    expect(Clause::IsNotIn->toArray()['prefix'])->toBe('Not');
    expect(Clause::IsSet->toArray()['prefix'])->toBe('Is Set');
    expect(Clause::IsNotSet->toArray()['prefix'])->toBe('Is Not Set');
    expect(Clause::IsTrue->toArray()['prefix'])->toBe('Is True');
    expect(Clause::IsFalse->toArray()['prefix'])->toBe('Is False');
    expect(Clause::After->toArray()['prefix'])->toBe('After');
    expect(Clause::Before->toArray()['prefix'])->toBe('Before');
    expect(Clause::Between->toArray()['prefix'])->toBe('Between');
});

it('can sort clauses by symbol length', function () {
    $clauses = [Clause::Contains, Clause::DoesNotContain, Clause::Equals];
    $sorted = Clause::sortByLength($clauses);
    
    // Should be sorted by length descending
    expect($sorted)->toBeArray();
    expect(count($sorted))->toBeGreaterThan(0);
    
    // Verify that longer symbols come first
    $lengths = array_map('strlen', $sorted);
    $isSorted = true;
    for ($i = 1; $i < count($lengths); $i++) {
        if ($lengths[$i-1] < $lengths[$i]) {
            $isSorted = false;
            break;
        }
    }
    
    expect($isSorted)->toBeTrue();
});

it('caches sorted symbols for performance', function () {
    // Call multiple times to test caching
    $result1 = Clause::sortByLength([]);
    $result2 = Clause::sortByLength([]);
    
    // Should return the same cached result
    expect($result1)->toBe($result2);
});

it('handles edge cases in search symbol finding', function () {
    // Test with empty string
    $clause = Clause::findBySearchSymbol('');
    expect($clause)->toBe(Clause::Equals);
    
    // Test with invalid symbol
    $clause = Clause::findBySearchSymbol('invalid_symbol');
    expect($clause)->toBeNull();
});

it('ensures all clauses have unique search symbols', function () {
    $symbols = [];
    foreach (Clause::cases() as $clause) {
        $symbol = $clause->toArray()['searchSymbol'];
        expect($symbols)->not->toContain($symbol, "Duplicate search symbol: {$symbol}");
        $symbols[] = $symbol;
    }
});

it('ensures all clauses have unique values', function () {
    $values = [];
    foreach (Clause::cases() as $clause) {
        $value = $clause->value;
        expect($values)->not->toContain($value, "Duplicate clause value: {$value}");
        $values[] = $value;
    }
});

it('can handle boolean clauses correctly', function () {
    // Test boolean-specific clauses
    expect(Clause::IsTrue->toArray()['searchSymbol'])->toBe('true');
    expect(Clause::IsFalse->toArray()['searchSymbol'])->toBe('false');
    expect(Clause::IsSet->toArray()['searchSymbol'])->toBe('not.null');
    expect(Clause::IsNotSet->toArray()['searchSymbol'])->toBe('null');
});

it('can handle date-specific clauses correctly', function () {
    // Test date-specific clauses
    expect(Clause::After->toArray()['searchSymbol'])->toBe('after');
    expect(Clause::Before->toArray()['searchSymbol'])->toBe('before');
    expect(Clause::Between->toArray()['searchSymbol'])->toBe('between');
});

it('can handle array-compatible clauses correctly', function () {
    // Test clauses that work with arrays
    expect(Clause::IsIn->toArray()['searchSymbol'])->toBe('in');
    expect(Clause::IsNotIn->toArray()['searchSymbol'])->toBe('not.in');
    expect(Clause::Between->toArray()['searchSymbol'])->toBe('between');
});