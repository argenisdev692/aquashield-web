<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Services\CrudGeneratorService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrudEntityCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:crud-entity 
                            {--json= : JSON string with entity configuration}
                            {--file= : Path to JSON file with entity configuration}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a complete CRUD entity with all necessary files based on JSON configuration';

    /**
     * CRUD Generator Service
     */
    protected CrudGeneratorService $generatorService;

    /**
     * Create a new command instance.
     */
    public function __construct(CrudGeneratorService $generatorService)
    {
        parent::__construct();
        $this->generatorService = $generatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $config = $this->getConfiguration();
            
            if (!$config) {
                $this->error('Invalid or missing JSON configuration.');
                return Command::FAILURE;
            }

            $this->info('🚀 Starting CRUD Entity Generation for: ' . $config['entity_name']);
            $this->newLine();

            // Validate configuration
            if (!$this->validateConfiguration($config)) {
                return Command::FAILURE;
            }

            // Generate all files
            $generatedFiles = $this->generatorService->generateEntity(
                $config, 
                $this->option('force') ?? false
            );

            // Display results
            $this->displayResults($generatedFiles, $config['entity_name']);

            $this->newLine();
            $this->info('✅ CRUD Entity generation completed successfully!');
            $this->info('📝 Don\'t forget to run: php artisan migrate');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error generating CRUD entity: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get configuration from JSON input or file
     */
    private function getConfiguration(): ?array
    {
        $jsonString = $this->option('json');
        $jsonFile = $this->option('file');

        if ($jsonString) {
            return json_decode($jsonString, true);
        }

        if ($jsonFile) {
            if (!File::exists($jsonFile)) {
                $this->error("JSON file not found: {$jsonFile}");
                return null;
            }
            return json_decode(File::get($jsonFile), true);
        }

        // Interactive mode
        return $this->interactiveConfiguration();
    }

    /**
     * Interactive configuration builder
     */
    private function interactiveConfiguration(): array
    {
        $this->info('🔧 Interactive CRUD Entity Configuration');
        $this->newLine();

        $entityName = $this->ask('Entity name (PascalCase, e.g., TypeDamage)');
        $tableName = $this->ask('Table name (snake_case)', Str::snake(Str::plural($entityName)));
        
        $fields = [];
        $this->info('📝 Define your fields (press enter with empty name to finish):');
        
        while (true) {
            $fieldName = $this->ask('Field name (snake_case)');
            if (empty($fieldName)) break;
            
            $fieldType = $this->choice('Field type', [
                'string', 'text', 'integer', 'boolean', 'decimal', 'date', 'datetime', 'enum'
            ], 'string');
            
            $required = $this->confirm('Is required?', true);
            
            $field = [
                'name' => $fieldName,
                'type' => $fieldType,
                'required' => $required
            ];
            
            if ($fieldType === 'enum') {
                $enumValues = $this->ask('Enum values (comma separated)');
                $field['enum_values'] = array_map('trim', explode(',', $enumValues));
            }
            
            if ($fieldType === 'string') {
                $maxLength = $this->ask('Max length', '255');
                $field['max_length'] = (int) $maxLength;
            }
            
            $fields[] = $field;
        }

        return [
            'entity_name' => $entityName,
            'table_name' => $tableName,
            'fields' => $fields
        ];
    }

    /**
     * Validate the configuration
     */
    private function validateConfiguration(array $config): bool
    {
        $required = ['entity_name', 'table_name', 'fields'];
        
        foreach ($required as $key) {
            if (!isset($config[$key])) {
                $this->error("Missing required configuration: {$key}");
                return false;
            }
        }

        if (!is_array($config['fields'])) {
            $this->error('Fields must be an array');
            return false;
        }

        foreach ($config['fields'] as $index => $field) {
            if (!isset($field['name']) || !isset($field['type'])) {
                $this->error("Field at index {$index} is missing name or type");
                return false;
            }
        }

        return true;
    }

    /**
     * Display generation results
     */
    private function displayResults(array $generatedFiles, string $entityName): void
    {
        $this->newLine();
        $this->info("📁 Generated files for {$entityName}:");
        $this->newLine();

        foreach ($generatedFiles as $type => $files) {
            $this->line("<fg=yellow>{$type}:</>");
            foreach ($files as $file) {
                $this->line("  ✓ {$file}");
            }
        }
    }
}