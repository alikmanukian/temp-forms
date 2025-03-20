<?php

namespace App\TableComponents\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateInertiaTableCommand extends Command
{
    protected $signature = 'make:inertia-table {name}';

    protected $description = 'Generate Inertia table component';

    public function handle(): int
    {
        $class = $this->argument('name');
        $resource = Str::singular($class);
        $classContent = file_get_contents(base_path('app/TableComponents/stubs/inertia-table.stub'));
        $classContent = str_replace(
            ['{{CLASS}}', '{{RESOURCE}}', '{{NAMESPACE}}'],
            [$class, $resource, 'App\\TableComponents'],
            $classContent
        );

        File::ensureDirectoryExists(base_path('app/Tables'));
        File::put( $filePath =base_path('app/Tables').'/'.$class.'.php', $classContent);

        $this->info('Inertia table created successfully in ' .$filePath);
        return 0;
    }
}
