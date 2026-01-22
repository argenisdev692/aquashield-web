<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ResourceGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $fileName = $this->config['entity_name'] . 'Resource.php';
        $filePath = app_path('Http/Resources/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Resource already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateResourceContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Resource created successfully',
            'path' => $filePath
        ];
    }
    
    public function getExistingFiles(): array
    {
        $fileName = $this->config['entity_name'] . 'Resource.php';
        $filePath = app_path('Http/Resources/' . $fileName);
        
        return File::exists($filePath) ? [$filePath] : [];
    }
    
    public function getType(): string
    {
        return 'resource';
    }
    
    private function generateResourceContent(): string
    {
        $stub = $this->getStub('resource');
        
        $entityName = $this->config['entity_name'];
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{resourceFields}}' => $this->generateResourceFields(),
            '{{relationships}}' => $this->generateRelationships(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateResourceFields(): string
    {
        $fields = [];
        
        // Add generic fields
        $fields[] = "            'id' => \$this->id";
        $fields[] = "            'uuid' => \$this->uuid";
        
        // Add custom fields
        foreach ($this->config['fields'] as $field) {
            $fieldName = $field['name'];
            $fieldValue = $this->generateFieldValue($field);
            $fields[] = "            '{$fieldName}' => {$fieldValue}";
        }
        
        // Add timestamps
        $fields[] = "            'created_at' => \$this->created_at?->format('Y-m-d H:i:s')";
        $fields[] = "            'updated_at' => \$this->updated_at?->format('Y-m-d H:i:s')";
        
        return implode(",\n", $fields);
    }
    
    private function generateFieldValue(array $field): string
    {
        $fieldName = $field['name'];
        $fieldType = $field['type'];
        
        return match ($fieldType) {
            'date' => "\$this->{$fieldName}?->format('Y-m-d')",
            'datetime', 'timestamp' => "\$this->{$fieldName}?->format('Y-m-d H:i:s')",
            'boolean' => "(bool) \$this->{$fieldName}",
            'integer', 'biginteger' => "(int) \$this->{$fieldName}",
            'decimal', 'float', 'double' => "(float) \$this->{$fieldName}",
            'json' => "\$this->{$fieldName} ? json_decode(\$this->{$fieldName}, true) : null",
            default => "\$this->{$fieldName}"
        };
    }
    
    private function generateRelationships(): string
    {
        $relationships = [];
        
        // Add user relationship
        $relationships[] = "            'user' => new UserResource(\$this->whenLoaded('user'))";
        
        // Add any additional relationships based on field types
        foreach ($this->config['fields'] as $field) {
            if (isset($field['relationship'])) {
                $relationshipName = $field['relationship']['name'];
                $relationshipResource = $field['relationship']['resource'] ?? 'BaseResource';
                $relationships[] = "            '{$relationshipName}' => new {$relationshipResource}(\$this->whenLoaded('{$relationshipName}'))";
            }
        }
        
        return implode(",\n", $relationships);
    }
}