<?php

namespace App\Console\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Console\Generators\{
    ModelGenerator,
    MigrationGenerator,
    ControllerGenerator,
    RepositoryGenerator,
    ServiceGenerator,
    DtoGenerator,
    RequestGenerator,
    ResourceGenerator,
    ExportGenerator,
    TestGenerator,
    RouteGenerator,
    ServiceProviderUpdater
};

class CrudGeneratorService
{
    /**
     * Available generators
     */
    private array $generators = [];

    /**
     * Generated files tracking
     */
    private array $generatedFiles = [];

    public function __construct()
    {
        $this->initializeGenerators();
    }

    /**
     * Generate complete CRUD entity
     */
    public function generateEntity(array $config, bool $force = false): array
    {
        $this->generatedFiles = [];
        
        // Normalize configuration
        $config = $this->normalizeConfig($config);
        
        try {
            // Generate all components
            foreach ($this->generators as $type => $generator) {
                $files = $generator->generate($config, $force);
                $this->generatedFiles[$type] = $files;
            }
            
            return $this->generatedFiles;
            
        } catch (\Exception $e) {
            // Rollback on error
            $this->rollbackGeneration();
            throw $e;
        }
    }

    /**
     * Initialize all generators
     */
    private function initializeGenerators(): void
    {
        $this->generators = [
            'Migration' => new MigrationGenerator(),
            'Model' => new ModelGenerator(),
            'Repository Interface' => new RepositoryGenerator('interface'),
            'Repository' => new RepositoryGenerator('implementation'),
            'Service' => new ServiceGenerator(),
            'DTO' => new DtoGenerator(),
            'Request' => new RequestGenerator(),
            'Resource' => new ResourceGenerator(),
            'Controller' => new ControllerGenerator(),
            'Export' => new ExportGenerator(),
            'Tests' => new TestGenerator(),
            'Routes' => new RouteGenerator(),
            'Service Provider' => new ServiceProviderUpdater(),
        ];
    }

    /**
     * Normalize and enrich configuration
     */
    private function normalizeConfig(array $config): array
    {
        $entityName = $config['entity_name'];
        $tableName = $config['table_name'];
        
        return array_merge($config, [
            // Naming conventions
            'entity_name_pascal' => Str::studly($entityName),
            'entity_name_camel' => Str::camel($entityName),
            'entity_name_snake' => Str::snake($entityName),
            'entity_name_kebab' => Str::kebab($entityName),
            'entity_name_plural' => Str::plural($entityName),
            'entity_name_plural_snake' => Str::snake(Str::plural($entityName)),
            'entity_name_plural_camel' => Str::camel(Str::plural($entityName)),
            
            // Table naming
            'table_name' => $tableName,
            
            // Paths
            'model_path' => "app/Models/{$entityName}.php",
            'controller_path' => "app/Http/Controllers/{$entityName}Controller.php",
            'repository_path' => "app/Repositories/{$entityName}Repository.php",
            'repository_interface_path' => "app/Repositories/Interfaces/{$entityName}RepositoryInterface.php",
            'service_path' => "app/Services/{$entityName}Service.php",
            'dto_path' => "app/Http/DTOs/{$entityName}DTO.php",
            'request_path' => "app/Http/Requests/{$entityName}Request.php",
            'resource_path' => "app/Http/Resources/{$entityName}Resource.php",
            'export_path' => "app/Exports/{$entityName}Export.php",
            'export_service_path' => "app/Services/{$entityName}ExportService.php",
            
            // Migration
            'migration_name' => 'create_' . $tableName . '_table',
            'migration_class' => 'Create' . Str::studly($tableName) . 'Table',
            
            // Generic fields (always included)
            'generic_fields' => [
                ['name' => 'id', 'type' => 'id', 'primary' => true],
                ['name' => 'uuid', 'type' => 'uuid', 'unique' => true],
                ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users'],
                ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
            ],
            
            // Relationships
            'relationships' => [
                'belongsTo' => [
                    ['model' => 'User', 'foreign_key' => 'user_id']
                ],
                'hasMany' => [] // Will be added to User model
            ],
            
            // Timestamps
            'created_at' => now()->format('Y_m_d_His'),
        ]);
    }

    /**
     * Rollback generation in case of error
     */
    private function rollbackGeneration(): void
    {
        foreach ($this->generatedFiles as $type => $files) {
            foreach ($files as $file) {
                if (File::exists($file)) {
                    File::delete($file);
                }
            }
        }
    }

    /**
     * Get stub content and replace placeholders
     */
    public static function getStub(string $stubName): string
    {
        $stubPath = base_path("stubs/crud/{$stubName}.stub");
        
        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }
        
        return File::get($stubPath);
    }

    /**
     * Replace placeholders in content
     */
    public static function replacePlaceholders(string $content, array $config): string
    {
        $replacements = [
            '{{EntityName}}' => $config['entity_name_pascal'],
            '{{entityName}}' => $config['entity_name_camel'],
            '{{entity_name}}' => $config['entity_name_snake'],
            '{{entity-name}}' => $config['entity_name_kebab'],
            '{{EntityNames}}' => $config['entity_name_plural'],
            '{{entityNames}}' => $config['entity_name_plural_camel'],
            '{{entity_names}}' => $config['entity_name_plural_snake'],
            '{{table_name}}' => $config['table_name'],
            '{{migration_class}}' => $config['migration_class'],
            '{{created_at}}' => $config['created_at'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}