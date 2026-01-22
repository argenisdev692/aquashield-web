<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DTOGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $fileName = $this->config['entity_name'] . 'DTO.php';
        $filePath = app_path('Http/DTOs/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'DTO already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateDTOContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'DTO created successfully',
            'path' => $filePath
        ];
    }
    
    public function getExistingFiles(): array
    {
        $fileName = $this->config['entity_name'] . 'DTO.php';
        $filePath = app_path('Http/DTOs/' . $fileName);
        
        return File::exists($filePath) ? [$filePath] : [];
    }
    
    public function getType(): string
    {
        return 'dto';
    }
    
    private function generateDTOContent(): string
    {
        $stub = $this->getStub('dto');
        
        $entityName = $this->config['entity_name'];
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{properties}}' => $this->generateProperties(),
            '{{constructor}}' => $this->generateConstructor(),
            '{{methods}}' => $this->generateMethods(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateProperties(): string
    {
        $properties = [];
        
        foreach ($this->config['fields'] as $field) {
            $type = $this->mapFieldTypeToPhpType($field['type']);
            $nullable = ($field['nullable'] ?? false) ? '?' : '';
            $name = $field['name'];
            
            $properties[] = "    public readonly {$nullable}{$type} \${$name};";
        }
        
        return implode("\n", $properties);
    }
    
    private function generateConstructor(): string
    {
        $parameters = [];
        $assignments = [];
        
        foreach ($this->config['fields'] as $field) {
            $type = $this->mapFieldTypeToPhpType($field['type']);
            $nullable = ($field['nullable'] ?? false) ? '?' : '';
            $name = $field['name'];
            
            $parameters[] = "        {$nullable}{$type} \${$name}";
            $assignments[] = "        \$this->{$name} = \${$name};";
        }
        
        $parameterList = implode(",\n", $parameters);
        $assignmentList = implode("\n", $assignments);
        
        return "    public function __construct(\n{$parameterList}\n    ) {\n{$assignmentList}\n    }";
    }
    
    private function generateMethods(): string
    {
        $methods = [];
        
        // Generate toArray method
        $arrayFields = [];
        foreach ($this->config['fields'] as $field) {
            $name = $field['name'];
            $arrayFields[] = "            '{$name}' => \$this->{$name}";
        }
        
        $arrayFieldsList = implode(",\n", $arrayFields);
        
        $methods[] = "    /**\n     * Convert DTO to array\n     */\n    public function toArray(): array\n    {\n        return [\n{$arrayFieldsList}\n        ];\n    }";
        
        // Generate fromArray method
        $fromArrayParams = [];
        foreach ($this->config['fields'] as $field) {
            $name = $field['name'];
            $fromArrayParams[] = "            \$data['{$name}'] ?? null";
        }
        
        $fromArrayParamsList = implode(",\n", $fromArrayParams);
        
        $methods[] = "    /**\n     * Create DTO from array\n     */\n    public static function fromArray(array \$data): self\n    {\n        return new self(\n{$fromArrayParamsList}\n        );\n    }";
        
        return implode("\n\n", $methods);
    }
    
    private function mapFieldTypeToPhpType(string $fieldType): string
    {
        return match ($fieldType) {
            'string', 'text', 'longtext', 'enum' => 'string',
            'integer', 'biginteger' => 'int',
            'boolean' => 'bool',
            'decimal', 'float', 'double' => 'float',
            'date', 'datetime', 'timestamp' => '\\Carbon\\Carbon',
            'json' => 'array',
            default => 'string'
        };
    }
}