<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ControllerGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $fileName = $this->config['entity_name'] . 'Controller.php';
        $filePath = app_path('Http/Controllers/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Controller already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateControllerContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Controller created successfully',
            'path' => $filePath
        ];
    }
    
    public function getExistingFiles(): array
    {
        $fileName = $this->config['entity_name'] . 'Controller.php';
        $filePath = app_path('Http/Controllers/' . $fileName);
        
        return File::exists($filePath) ? [$filePath] : [];
    }
    
    public function getType(): string
    {
        return 'controller';
    }
    
    private function generateControllerContent(): string
    {
        $stub = $this->getStub('controller');
        
        $entityName = $this->config['entity_name'];
        $entityVariable = Str::camel($entityName);
        $entityVariablePlural = Str::plural($entityVariable);
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{entityName}}' => $entityVariable,
            '{{entityNamePlural}}' => $entityVariablePlural,
            '{{EntityNamePlural}}' => Str::plural($entityName),
            '{{tableName}}' => $this->config['table_name'],
        ];
        
        return $this->processContent($stub, $replacements);
    }
}