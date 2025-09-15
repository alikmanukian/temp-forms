<?php

use App\Models\User;
use App\TableComponents\Columns\TextColumn;
use App\TableComponents\Filters\TextFilter;
use Tests\Unit\TableComponents\TableTestCase;
use Tests\Unit\TableComponents\MockTable;

uses(TableTestCase::class);

it('can create a table instance', function () {
    $table = MockTable::make();
    
    expect($table)->toBeInstanceOf(MockTable::class);
});

it('can set table name', function () {
    $table = MockTable::make()->as('test-table');
    
    $data = $table->resolve();
    
    expect($data['name'])->toBe('test-table');
});

it('has proper default configuration', function () {
    $table = MockTable::make();
    
    $data = $table->resolve();
    
    expect($data)
        ->toHaveKey('resizable', true)
        ->toHaveKey('stickyHeader', false)
        ->toHaveKey('stickyPagination', false);
});

it('can register columns', function () {
    $table = MockTable::make();
    
    $data = $table->resolve();
    
    expect($data['headers'])->toHaveCount(5);
    
    // Check first column structure
    $this->assertColumnStructure($data['headers'][0]);
    expect($data['headers'][0]['name'])->toBe('id');
});

it('can register filters', function () {
    $table = MockTable::make();
    
    $data = $table->resolve();
    
    expect($data['filters'])->toHaveCount(3);
    
    // Check first filter structure
    $this->assertFilterStructure($data['filters'][0]);
    expect($data['filters'][0]['name'])->toBe('name');
});

it('sets up searchable fields correctly', function () {
    $table = MockTable::make();
    
    $data = $table->resolve();
    
    expect($data['searchable'])
        ->toContain('name')
        ->toContain('email')
        ->not->toContain('id');
});

it('can paginate data', function () {
    $table = MockTable::make();
    
    $data = $table->resolve();
    
    $this->assertPaginationMeta($data['meta']);
    expect($data['meta']['perPage'])->toBe(10);
    expect($data['meta']['total'])->toBe(50); // Created in setUp
});

it('can transform data through columns', function () {
    $table = MockTable::make();
    
    $data = $table->resolve();
    
    expect($data['data'])->toHaveCount(10); // First page
    
    // Check that each row has the expected structure
    $firstRow = $data['data'][0];
    expect($firstRow)
        ->toHaveKey('id')
        ->toHaveKey('name')
        ->toHaveKey('email')
        ->toHaveKey('status')
        ->toHaveKey('created_at');
});

it('generates consistent hash for same configuration', function () {
    $table1 = MockTable::make();
    $table2 = MockTable::make();
    
    $data1 = $table1->resolve();
    $data2 = $table2->resolve();
    
    // Hash should be the same for identical configurations
    expect($data1['hash'])->toBe($data2['hash']);
});

it('generates different hash for different configurations', function () {
    $table1 = MockTable::make();
    $table2 = MockTable::make()->as('different-name');
    
    $data1 = $table1->resolve();
    $data2 = $table2->resolve();
    
    // Hash should be different for different configurations
    expect($data1['hash'])->not->toBe($data2['hash']);
});

it('can serialize to JSON', function () {
    $table = MockTable::make();
    
    $json = json_encode($table);
    $decoded = json_decode($json, true);
    
    expect($decoded)->toBeArray();
    $this->assertTableStructure($decoded);
});

it('excludes correct cookies from encryption', function () {
    $cookies = MockTable::dontEncryptCookies();
    
    expect($cookies)
        ->toBeArray()
        ->toContain('perPage_users')
        ->toContain('perPage_employees');
});

it('handles empty dataset gracefully', function () {
    // Clear all users
    User::query()->delete();
    
    $table = MockTable::make();
    $data = $table->resolve();
    
    expect($data['data'])->toBeEmpty();
    expect($data['meta']['total'])->toBe(0);
});

it('can handle custom per page options', function () {
    $table = MockTable::make();
    
    // Debug: Check what the table name actually is
    $expectedCookieName = 'perPage_' . $table->getPublicName();
    
    // Set the cookie in the request
    request()->cookies->set($expectedCookieName, '25');
    
    $data = $table->resolve();
    
    expect($data['meta']['perPage'])->toBe(25);
});

it('validates resource configuration', function () {
    expect(function () {
        $tableClass = new class extends \App\TableComponents\Table {
            protected ?string $resource = null; // Invalid - no resource
            
            public function columns(): array
            {
                return [];
            }
            
            public function filters(): array
            {
                return [];
            }
        };
        $tableClass::make(); // This will call the constructor
    })->toThrow(\Error::class);
});

it('can handle global search', function () {
    // Create a user with specific content
    User::factory()->create(['name' => 'Searchable User', 'email' => 'search@test.com']);
    
    // Set up search request
    $this->createFilterRequest(['filter.search' => 'Searchable']);
    
    $table = MockTable::make();
    $data = $table->resolve();
    
    // Should find at least one result
    expect($data['data'])->not->toBeEmpty();
    
    // Check that found user contains search term
    $foundUser = collect($data['data'])->first(fn($user) => str_contains($user['name'], 'Searchable'));
    expect($foundUser)->not->toBeNull();
});

it('can handle per page changes', function () {
    $table = MockTable::make();
    
    // Debug: Check what the table name actually is
    $expectedCookieName = 'perPage_' . $table->getPublicName();
    
    // Test different per page values
    foreach ([10, 25, 50] as $perPage) {
        request()->cookies->set($expectedCookieName, (string)$perPage);
        
        $data = $table->resolve();
        expect($data['meta']['perPage'])->toBe($perPage);
    }
});

it('maintains column order', function () {
    $table = MockTable::make();
    $data = $table->resolve();
    
    $columnNames = array_column($data['headers'], 'name');
    
    expect($columnNames)->toBe(['id', 'name', 'email', 'status', 'created_at']);
});

it('can handle page name with table name', function () {
    $table = MockTable::make()->as('custom-table');
    $data = $table->resolve();
    
    expect($data['pageName'])->toBe('page.custom-table');
});