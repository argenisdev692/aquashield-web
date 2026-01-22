<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ServiceGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $fileName = $this->config['entity_name'] . 'Service.php';
        $filePath = app_path('Services/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Service already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateServiceContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Service created successfully',
            'path' => $filePath
        ];
    }
    
    public function getExistingFiles(): array
    {
        $fileName = $this->config['entity_name'] . 'Service.php';
        $filePath = app_path('Services/' . $fileName);
        
        return File::exists($filePath) ? [$filePath] : [];
    }
    
    public function getType(): string
    {
        return 'service';
    }
    
    private function generateServiceContent(): string
    {
        $stub = $this->getStub('service');
        
        $entityName = $this->config['entity_name'];
        $entityVariable = Str::camel($entityName);
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{entityName}}' => $entityVariable,
            '{{businessLogic}}' => $this->generateBusinessLogic(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateBusinessLogic(): string
    {
        $businessLogic = [];
        
        // Add validation logic for specific field types
        foreach ($this->config['fields'] as $field) {
            if ($field['type'] === 'email') {
                $businessLogic[] = $this->generateEmailValidationLogic($field['name']);
            }
            
            if ($field['type'] === 'enum' && isset($field['enum_values'])) {
                $businessLogic[] = $this->generateEnumValidationLogic($field['name'], $field['enum_values']);
            }
        }
        
        return implode("\n\n    ", $businessLogic);
    }
    
    private function generateEmailValidationLogic(string $fieldName): string
    {
        $methodName = 'validate' . Str::studly($fieldName);
        
        return "/**
     * Validate {$fieldName} format
     */
    private function {$methodName}(string \${$fieldName}): bool
    {
        return filter_var(\${$fieldName}, FILTER_VALIDATE_EMAIL) !== false;
    }";
    }
    
    private function generateEnumValidationLogic(string $fieldName, array $enumValues): string
    {
        $methodName = 'validate' . Str::studly($fieldName);
        $allowedValues = "['" . implode("', '", $enumValues) . "']";
        
        return "/**
     * Validate {$fieldName} enum value
     */
    private function {$methodName}(string \${$fieldName}): bool
    {
        return in_array(\${$fieldName}, {$allowedValues});
    }";
    }
}