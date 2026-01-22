<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ExportGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $results = [];
        
        // Generate Export class
        $exportResult = $this->generateExport();
        $results[] = $exportResult;
        
        // Generate Export Service
        $serviceResult = $this->generateExportService();
        $results[] = $serviceResult;
        
        return $results;
    }
    
    public function getExistingFiles(): array
    {
        $entityName = $this->config['entity_name'];
        $exportPath = app_path('Exports/' . $entityName . 'Export.php');
        $servicePath = app_path('Services/' . $entityName . 'ExportService.php');
        
        $files = [];
        if (File::exists($exportPath)) {
            $files[] = $exportPath;
        }
        if (File::exists($servicePath)) {
            $files[] = $servicePath;
        }
        
        return $files;
    }
    
    public function getType(): string
    {
        return 'export';
    }
    
    private function generateExport(): array
    {
        $fileName = $this->config['entity_name'] . 'Export.php';
        $filePath = app_path('Exports/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Export class already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateExportContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Export class created successfully',
            'path' => $filePath
        ];
    }
    
    private function generateExportService(): array
    {
        $fileName = $this->config['entity_name'] . 'ExportService.php';
        $filePath = app_path('Services/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Export Service already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateExportServiceContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Export Service created successfully',
            'path' => $filePath
        ];
    }
    
    private function generateExportContent(): string
    {
        $stub = $this->getStub('export');
        
        $entityName = $this->config['entity_name'];
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{headings}}' => $this->generateHeadings(),
            '{{mapMethod}}' => $this->generateMapMethod(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateExportServiceContent(): string
    {
        $stub = $this->getStub('export-service');
        
        $entityName = $this->config['entity_name'];
        $entityVariable = Str::camel($entityName);
        $entityVariablePlural = Str::plural($entityVariable);
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{entityName}}' => $entityVariable,
            '{{entityNamePlural}}' => $entityVariablePlural,
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateHeadings(): string
    {
        $headings = [];
        
        // Add generic headings
        $headings[] = "'ID'";
        $headings[] = "'UUID'";
        
        // Add custom field headings
        foreach ($this->config['fields'] as $field) {
            $heading = $this->generateFieldHeading($field['name']);
            $headings[] = "'{$heading}'";
        }
        
        // Add user and timestamp headings
        $headings[] = "'Usuario'";
        $headings[] = "'Fecha de Creación'";
        $headings[] = "'Fecha de Actualización'";
        
        return '[' . implode(', ', $headings) . ']';
    }
    
    private function generateMapMethod(): string
    {
        $mappings = [];
        
        // Add generic mappings
        $mappings[] = "            \$row->id";
        $mappings[] = "            \$row->uuid";
        
        // Add custom field mappings
        foreach ($this->config['fields'] as $field) {
            $fieldName = $field['name'];
            $mapping = $this->generateFieldMapping($field);
            $mappings[] = "            {$mapping}";
        }
        
        // Add user and timestamp mappings
        $mappings[] = "            \$row->user?->name ?? 'N/A'";
        $mappings[] = "            \$row->created_at?->format('Y-m-d H:i:s') ?? 'N/A'";
        $mappings[] = "            \$row->updated_at?->format('Y-m-d H:i:s') ?? 'N/A'";
        
        return '[\n' . implode(",\n", $mappings) . '\n        ]';
    }
    
    private function generateFieldHeading(string $fieldName): string
    {
        // Convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $fieldName));
    }
    
    private function generateFieldMapping(array $field): string
    {
        $fieldName = $field['name'];
        $fieldType = $field['type'];
        
        return match ($fieldType) {
            'date' => "\$row->{$fieldName}?->format('Y-m-d') ?? 'N/A'",
            'datetime', 'timestamp' => "\$row->{$fieldName}?->format('Y-m-d H:i:s') ?? 'N/A'",
            'boolean' => "\$row->{$fieldName} ? 'Sí' : 'No'",
            'json' => "\$row->{$fieldName} ? json_encode(\$row->{$fieldName}) : 'N/A'",
            'enum' => "\$row->{$fieldName} ?? 'N/A'",
            default => "\$row->{$fieldName} ?? 'N/A'"
        };
    }
}