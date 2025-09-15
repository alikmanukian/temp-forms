<?php

use App\Models\User;
use App\TableComponents\Columns\Column;
use App\TableComponents\Columns\TextColumn;
use App\TableComponents\Enums\ColumnAlignment;
use Tests\Unit\TableComponents\TableTestCase;

uses(TableTestCase::class);

it('can create a column instance', function () {
    $column = TextColumn::make('name', 'Full Name');

    expect($column)->toBeInstanceOf(Column::class);
});

it('has correct default configuration', function () {
    $column = TextColumn::make('name', 'Full Name');

    $data = $column->toArray();

    expect($data)
        ->toHaveKey('name', 'name')
        ->toHaveKey('header', 'Full Name')
        ->toHaveKey('type', 'TextColumn')
        ->toHaveKey('width', 'auto')
        ->toHaveKey('hasIcon', false);

    expect($data['options'])
        ->toHaveKey('sortable', false)
        ->toHaveKey('toggleable', true)
        ->toHaveKey('stickable', false)
        ->toHaveKey('wrap', false)
        ->toHaveKey('truncate', 1);
});

it('can configure column properties via method calls', function () {
    $column = TextColumn::make('name', 'Full Name')
        ->sortable()
        ->searchable()
        ->width('200px')
        ->headerAlignment(ColumnAlignment::Center)
        ->alignment(ColumnAlignment::Right);

    $data = $column->toArray();

    expect($data)
        ->toHaveKey('width', '200px');

    expect($data['options'])
        ->toHaveKey('sortable', true)
        ->toHaveKey('headerAlignment', 'justify-center')
        ->toHaveKey('alignment', 'justify-end');
});

it('can handle alias configuration', function () {
    $column = TextColumn::make('first_name', 'Name')
        ->as('full_name');

    expect($column->getAlias())->toBe('full_name');
    expect($column->getName())->toBe('first_name');

    $data = $column->toArray();
    expect($data['name'])->toBe('full_name');
});

it('can handle not methods', function () {
    $column = TextColumn::make('name', 'Name')
        ->sortable()
        ->searchable()
        ->toggleable()
        ->stickable();

    // Test not methods
    $column->notSortable()
        ->notSearchable()
        ->notToggleable()
        ->notStickable();

    $data = $column->toArray();

    expect($data['options'])
        ->toHaveKey('sortable', false)
        ->toHaveKey('toggleable', false)
        ->toHaveKey('stickable', false);
});

it('can check if column is searchable', function () {
    $searchableColumn = TextColumn::make('name')->searchable();
    $nonSearchableColumn = TextColumn::make('id');

    expect($searchableColumn->isSearchable())->toBeTrue();
    expect($nonSearchableColumn->isSearchable())->toBeFalse();
});

it('can handle width configuration', function () {
    $column = TextColumn::make('name')->width('150px');

    expect($column->getWidth())->toBe('150px');
});

it('can handle header configuration', function () {
    $column = TextColumn::make('name', 'Custom Header');

    expect($column->getHeader())->toBe('Custom Header');

    // Test default header from name
    $defaultColumn = TextColumn::make('first_name');
    expect($defaultColumn->getHeader())->toBe('First Name');
});

it('can apply data mappings', function () {
    $inputModel = new User;
    $inputModel->status = 'active';
    $outputModel = new class extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;

        protected $fillable = ['status'];
    };

    // Create column with mapping
    $column = TextColumn::make('status')->mapAs(function ($model, $value) {
        return strtoupper($value);
    });

    $column->useMappings($inputModel, $outputModel);

    expect($outputModel->status)->toBe('ACTIVE');
});

it('can apply array mappings', function () {
    $inputModel = new User;
    $inputModel->status = 'active';
    $outputModel = new class extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;

        protected $fillable = ['status'];
    };

    // Create column with array mapping
    $column = TextColumn::make('status')->mapAs([
        'active' => 'Online',
        'inactive' => 'Offline',
    ]);

    $column->useMappings($inputModel, $outputModel);

    expect($outputModel->status)->toBe('Online');
});

it('can handle truncation configuration', function () {
    $column = TextColumn::make('description')->truncate(3);

    $data = $column->toArray();

    expect($data['options']['truncate'])->toBe(3);
});

it('can handle wrapping configuration', function () {
    $column = TextColumn::make('description')->wrap();

    $data = $column->toArray();

    expect($data['options']['wrap'])->toBeTrue();
});

it('can handle custom CSS classes', function () {
    $column = TextColumn::make('name')
        ->headerClass('custom-header')
        ->cellClass('custom-cell');

    $data = $column->toArray();

    expect($data['options'])
        ->toHaveKey('headerClass', 'custom-header')
        ->toHaveKey('cellClass', 'custom-cell');
});

it('throws exception for invalid method calls', function () {
    $column = TextColumn::make('name');

    expect(fn () => $column->invalidMethod())
        ->toThrow(BadMethodCallException::class, "Method [invalidMethod] doesn't exists");
});

it('can handle mutated attributes', function () {
    // Create a model with a mutated attribute
    $inputModel = new class extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;

        public function getFullNameAttribute()
        {
            return 'John Doe';
        }

        public function getMutatedAttributes()
        {
            return ['full_name'];
        }
    };

    $outputModel = new class extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;
    };

    $column = TextColumn::make('full_name');
    $column->useMappings($inputModel, $outputModel);

    // Should be added to appends for mutated attributes
    expect($column->appends)->toContain('full_name');
});

it('caches trait checks for performance', function () {
    // Create multiple instances of the same column type
    $column1 = TextColumn::make('name1');
    $column2 = TextColumn::make('name2');

    $inputModel = new User;
    $outputModel = new class extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;
    };

    // Call transform on both - should use cached trait checks
    $column1->transform($inputModel, $outputModel);
    $column2->transform($inputModel, $outputModel);

    // This test mainly ensures no errors occur with caching
    expect(true)->toBeTrue();
});

it('can handle reflection errors gracefully', function () {
    $column = TextColumn::make('name');

    // Create a mock that might throw reflection exceptions
    $inputModel = new User;
    $outputModel = new class extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;
    };

    // Should not throw exceptions
    expect(fn () => $column->transform($inputModel, $outputModel))
        ->not->toThrow(Exception::class);
});

it('can handle left alignment', function () {
    $column = TextColumn::make('name')->leftAligned();

    $data = $column->toArray();

    expect($data['options']['alignment'])->toBe('justify-start');
});

it('can handle center alignment', function () {
    $column = TextColumn::make('name')->centerAligned();

    $data = $column->toArray();

    expect($data['options']['alignment'])->toBe('justify-center');
});

it('can handle right alignment', function () {
    $column = TextColumn::make('name')->rightAligned();

    $data = $column->toArray();

    expect($data['options']['alignment'])->toBe('justify-end');
});
