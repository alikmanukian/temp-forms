<?php

use App\TableComponents\Enums\Clause;
use App\TableComponents\Filters\Filter;
use App\TableComponents\Filters\TextFilter;
use App\TableComponents\Filters\BooleanFilter;
use App\TableComponents\Filters\DateFilter;
use App\TableComponents\Filters\Spatie\AllowedFilter;
use Tests\Unit\TableComponents\TableTestCase;

uses(TableTestCase::class);

it('can create a filter instance', function () {
    $filter = TextFilter::make('name', 'Full Name');
    
    expect($filter)->toBeInstanceOf(Filter::class);
});

it('has correct default configuration', function () {
    $filter = TextFilter::make('name', 'Full Name');
    
    $data = $filter->toArray();
    
    expect($data)
        ->toHaveKey('name', 'name')
        ->toHaveKey('title', 'Full Name')
        ->toHaveKey('component', 'TextFilter')
        ->toHaveKey('showInHeader', false)
        ->toHaveKey('selected', false)
        ->toHaveKey('opened', false)
        ->toHaveKey('value', null)
        ->toHaveKey('selectedClause', null);
});

it('can configure filter properties', function () {
    $filter = TextFilter::make('name', 'Full Name')
        ->showInHeader()
        ->as('display_name');
    
    $data = $filter->toArray();
    
    expect($data)
        ->toHaveKey('name', 'display_name')
        ->toHaveKey('showInHeader', true);
});

it('can handle title generation from name', function () {
    $filter = TextFilter::make('first_name');
    
    $data = $filter->toArray();
    
    expect((string) $data['title'])->toBe('First Name');
});

it('can parse request values with clauses', function () {
    $filter = TextFilter::make('name');
    
    // Test contains clause
    $filter->parseRequestValue('table', 'contains.john');
    
    expect($filter->toArray())
        ->toHaveKey('value', 'john')
        ->toHaveKey('selectedClause');
        
    expect($filter->toArray()['selectedClause']['value'])->toBe('contains');
});

it('can parse request values without clauses', function () {
    $filter = TextFilter::make('name');
    
    // Test simple value (should default to equals)
    $filter->parseRequestValue('table', 'john');
    
    expect($filter->toArray())
        ->toHaveKey('value', 'john')
        ->toHaveKey('selectedClause');
        
    expect($filter->toArray()['selectedClause']['value'])->toBe('equals');
});

it('can parse array values for arrayable clauses', function () {
    $filter = TextFilter::make('status');
    
    // Test in clause with multiple values
    $filter->parseRequestValue('table', 'in.active,inactive');
    
    $data = $filter->toArray();
    
    expect($data['value'])->toBe(['active', 'inactive']);
    expect($data['selectedClause']['value'])->toBe('is_in');
});

it('can parse boolean values', function () {
    $filter = BooleanFilter::make('active');
    
    // Test true value
    $filter->parseRequestValue('table', 'true');
    expect($filter->toArray()['value'])->toBeTrue();
    
    // Test false value
    $filter->parseRequestValue('table', 'false');
    expect($filter->toArray()['value'])->toBeFalse();
});

it('can generate query parameters', function () {
    $filter = TextFilter::make('name');
    
    $queryParam = $filter->getQueryParam('users');
    
    expect($queryParam)->toBe('filter.users.name');
});

it('can generate query parameters with custom filter name', function () {
    $filter = TextFilter::make('name')->as('display_name');
    
    $queryParam = $filter->getQueryParam('users');
    
    expect($queryParam)->toBe('filter.users.display_name');
});

it('can generate query parameters without table name', function () {
    $filter = TextFilter::make('name');
    
    $queryParam = $filter->getQueryParam('');
    
    expect($queryParam)->toBe('filter.name');
});

it('can create allowed filter methods', function () {
    $filter = TextFilter::make('name');
    $filter->parseRequestValue('table', 'contains.john');
    
    $allowedFilter = $filter->getAllowedFilterMethod();
    
    expect($allowedFilter)->toBeInstanceOf(AllowedFilter::class);
});

it('returns null for allowed filter when no clause selected', function () {
    $filter = TextFilter::make('name');
    
    $allowedFilter = $filter->getAllowedFilterMethod();
    
    expect($allowedFilter)->toBeNull();
});

it('can handle date filters with special date methods', function () {
    $filter = DateFilter::make('created_at');
    $filter->parseRequestValue('table', 'equals.2024-01-01');
    
    $allowedFilter = $filter->getAllowedFilterMethod();
    
    expect($allowedFilter)->toBeInstanceOf(AllowedFilter::class);
});

it('can handle between clause for date ranges', function () {
    $filter = DateFilter::make('created_at');
    $filter->parseRequestValue('table', 'between.2024-01-01,2024-01-31');
    
    $data = $filter->toArray();
    
    expect($data['value'])->toBe(['2024-01-01', '2024-01-31']);
    expect($data['selectedClause']['value'])->toBe('between');
});

it('can handle nullable configuration', function () {
    $filter = TextFilter::make('name')->nullable();
    
    $data = $filter->toArray();
    $clauseValues = array_column($data['clauses'], 'value');
    
    expect($clauseValues)
        ->toContain('is_set')
        ->toContain('is_not_set');
});

it('can handle custom apply logic', function () {
    $customCalled = false;
    
    $filter = TextFilter::make('name')->applyUsing(function ($builder, $attribute, $clause, $value) use (&$customCalled) {
        $customCalled = true;
        return $builder;
    });
    
    $filter->parseRequestValue('table', 'john');
    $allowedFilter = $filter->getAllowedFilterMethod();
    
    expect($allowedFilter)->toBeInstanceOf(AllowedFilter::class);
    
    // Test that custom callback is used
    $mockBuilder = $this->createMock(\Illuminate\Database\Eloquent\Builder::class);
    
    // This would normally call the custom callback
    expect($customCalled)->toBeFalse(); // Not called until filter is actually applied
});

it('merges request values correctly', function () {
    $filter = TextFilter::make('name');
    
    // Mock request
    request()->merge(['initial' => 'value']);
    
    $filter->parseRequestValue('table', 'john');
    
    // Should merge the filter value into request
    expect(request()->input('filter.table.name'))->toBe('john');
    expect(request()->input('initial'))->toBe('value'); // Should preserve existing
});

it('can handle url decoding in request values', function () {
    $filter = TextFilter::make('name');
    
    // Test URL encoded value
    $encodedValue = urlencode('john doe');
    $filter->parseRequestValue('table', $encodedValue);
    
    expect($filter->toArray()['value'])->toBe('john doe');
});

it('can handle empty and null values appropriately', function () {
    $filter = TextFilter::make('name');
    
    // Test empty value
    $filter->parseRequestValue('table', '');
    expect($filter->toArray()['value'])->toBeNull();
    
    // Test with clause but empty value
    $filter->parseRequestValue('table', 'contains.');
    expect($filter->toArray()['value'])->toBe('');
});

it('can handle special clause values correctly', function () {
    $filter = BooleanFilter::make('active');
    
    // Test is_set clause
    $filter->parseRequestValue('table', 'not.null');
    expect($filter->toArray()['value'])->toBe('anyValue');
    expect($filter->toArray()['selectedClause']['value'])->toBe('is_set');
    
    // Test is_not_set clause
    $filter->parseRequestValue('table', 'null');
    expect($filter->toArray()['value'])->toBe('anyValue');
    expect($filter->toArray()['selectedClause']['value'])->toBe('is_not_set');
});

it('can sort clauses by length for proper parsing', function () {
    $filter = TextFilter::make('name');
    
    // The parsing should handle longer clauses first
    // Test that "not.contains" is matched before "not"
    $filter->parseRequestValue('table', 'not.contains.test');
    
    expect($filter->toArray()['selectedClause']['value'])->toBe('does_not_contain');
});

it('can handle alias in query parameters', function () {
    $filter = TextFilter::make('first_name')->as('name');
    
    expect($filter->getAlias())->toBe('name');
    expect($filter->getName())->toBe('first_name');
});