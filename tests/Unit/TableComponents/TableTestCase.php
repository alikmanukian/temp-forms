<?php

namespace Tests\Unit\TableComponents;

use App\Models\User;
use App\TableComponents\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class TableTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users for table testing
        User::factory(50)->create();
    }

    /**
     * Create a mock table for testing purposes
     */
    protected function createMockTable(): MockTable
    {
        return MockTable::make();
    }

    /**
     * Assert that a table has the expected structure
     */
    protected function assertTableStructure(array $tableData, array $expectedKeys = []): void
    {
        $defaultKeys = [
            'name', 'pageName', 'stickyHeader', 'stickyPagination',
            'searchable', 'resizable', 'headers', 'filters', 'hash',
            'data', 'meta'
        ];

        $keysToCheck = !empty($expectedKeys) ? $expectedKeys : $defaultKeys;

        foreach ($keysToCheck as $key) {
            $this->assertArrayHasKey($key, $tableData, "Table data missing key: {$key}");
        }
    }

    /**
     * Assert that table meta has pagination structure
     */
    protected function assertPaginationMeta(array $meta): void
    {
        $paginationKeys = ['currentPage', 'perPage', 'total', 'lastPage', 'perPageOptions'];
        
        foreach ($paginationKeys as $key) {
            $this->assertArrayHasKey($key, $meta, "Pagination meta missing key: {$key}");
        }
    }

    /**
     * Assert that a column has the expected structure
     */
    protected function assertColumnStructure(array $column): void
    {
        $requiredKeys = ['name', 'header', 'width', 'type', 'hasIcon', 'options'];
        
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $column, "Column missing key: {$key}");
        }

        // Check options structure
        $optionKeys = ['sortable', 'toggleable', 'headerAlignment', 'alignment', 'wrap', 'truncate', 'stickable'];
        foreach ($optionKeys as $key) {
            $this->assertArrayHasKey($key, $column['options'], "Column options missing key: {$key}");
        }
    }

    /**
     * Assert that a filter has the expected structure
     */
    protected function assertFilterStructure(array $filter): void
    {
        $requiredKeys = ['name', 'title', 'clauses', 'defaultClause', 'showInHeader', 'component', 'selected', 'opened'];
        
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $filter, "Filter missing key: {$key}");
        }
    }

    /**
     * Create test request data for table filtering
     */
    protected function createFilterRequest(array $filters = []): void
    {
        request()->merge($filters);
    }

    /**
     * Get a test user model
     */
    protected function getTestUser(): User
    {
        return User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'active',
            'bio' => 'Test bio content',
        ]);
    }
}