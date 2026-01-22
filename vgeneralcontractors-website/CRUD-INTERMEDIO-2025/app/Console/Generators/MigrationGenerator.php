<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MigrationGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $migrationName = 'create_' . $this->config['table_name'] . '_table';
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . '_' . $migrationName . '.php';
        $filePath = database_path('migrations/' . $fileName);
        
        // Check if migration already exists
        $existingMigrations = glob(database_path('migrations/*_' . $migrationName . '.php'));
        if (!empty($existingMigrations)) {
            return [
                'status' => 'skipped',
                'message' => 'Migration already exists: ' . basename($existingMigrations[0]),
                'path' => $existingMigrations[0]
            ];
        }
        
        $content = $this->generateMigrationContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Migration created successfully',
            'path' => $filePath
        ];
    }
    
    public function getExistingFiles(): array
    {
        $migrationName = 'create_' . $this->config['table_name'] . '_table';
        return glob(database_path('migrations/*_' . $migrationName . '.php'));
    }
    
    public function getType(): string
    {
        return 'migration';
    }
    
    private function generateMigrationContent(): string
    {
        $stub = $this->getStub('migration');
        
        $replacements = [
            '{{ClassName}}' => 'Create' . Str::studly($this->config['table_name']) . 'Table',
            '{{tableName}}' => $this->config['table_name'],
            '{{fields}}' => $this->generateMigrationFields(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateMigrationFields(): string
    {
        $fields = [];
        
        // Add custom fields
        foreach ($this->config['fields'] as $field) {
            $fieldDefinition = $this->generateMigrationFieldDefinition($field);
            if ($fieldDefinition) {
                $fields[] = $fieldDefinition;
            }
        }
        
        return implode("\n            ", $fields);
    }
    
    private function generateMigrationFieldDefinition(array $field): string
    {
        $name = $field['name'];
        $type = $field['type'];
        $nullable = $field['nullable'] ?? false;
        $default = $field['default'] ?? null;
        
        $definition = match ($type) {
            'string' => "\$table->string('{$name}')",
            'text' => "\$table->text('{$name}')",
            'longtext' => "\$table->longText('{$name}')",
            'integer' => "\$table->integer('{$name}')",
            'biginteger' => "\$table->bigInteger('{$name}')",
            'boolean' => "\$table->boolean('{$name}')",
            'decimal' => "\$table->decimal('{$name}', 8, 2)",
            'float' => "\$table->float('{$name}')",
            'double' => "\$table->double('{$name}')",
            'date' => "\$table->date('{$name}')",
            'datetime' => "\$table->dateTime('{$name}')",
            'timestamp' => "\$table->timestamp('{$name}')",
            'time' => "\$table->time('{$name}')",
            'json' => "\$table->json('{$name}')",
            'enum' => $this->generateEnumField($field),
            default => "\$table->string('{$name}')"
        };
        
        if ($nullable) {
            $definition .= '->nullable()';
        }
        
        if ($default !== null) {
            if (is_string($default)) {
                $definition .= "->default('{$default}')";
            } elseif (is_bool($default)) {
                $definition .= '->default(' . ($default ? 'true' : 'false') . ')';
            } else {
                $definition .= "->default({$default})";
            }
        }
        
        $definition .= ';';
        
        return $definition;
    }
    
    private function generateEnumField(array $field): string
    {
        $name = $field['name'];
        $values = $field['enum_values'] ?? ['active', 'inactive'];
        $enumValues = "['" . implode("', '", $values) . "']";
        
        return "\$table->enum('{$name}', {$enumValues})";
    }
}