<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class RepositoryGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $results = [];
        
        // Generate Repository Interface
        $interfaceResult = $this->generateInterface();
        $results[] = $interfaceResult;
        
        // Generate Repository Implementation
        $repositoryResult = $this->generateRepository();
        $results[] = $repositoryResult;
        
        return $results;
    }
    
    public function getExistingFiles(): array
    {
        $entityName = $this->config['entity_name'];
        $interfacePath = app_path('Repositories/Interfaces/' . $entityName . 'RepositoryInterface.php');
        $repositoryPath = app_path('Repositories/' . $entityName . 'Repository.php');
        
        $files = [];
        if (File::exists($interfacePath)) {
            $files[] = $interfacePath;
        }
        if (File::exists($repositoryPath)) {
            $files[] = $repositoryPath;
        }
        
        return $files;
    }
    
    public function getType(): string
    {
        return 'repository';
    }
    
    private function generateInterface(): array
    {
        $fileName = $this->config['entity_name'] . 'RepositoryInterface.php';
        $filePath = app_path('Repositories/Interfaces/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Repository Interface already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateInterfaceContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Repository Interface created successfully',
            'path' => $filePath
        ];
    }
    
    private function generateRepository(): array
    {
        $fileName = $this->config['entity_name'] . 'Repository.php';
        $filePath = app_path('Repositories/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Repository already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateRepositoryContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Repository created successfully',
            'path' => $filePath
        ];
    }
    
    private function generateInterfaceContent(): string
    {
        $stub = $this->getStub('repository-interface');
        
        $entityName = $this->config['entity_name'];
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{entityName}}' => Str::camel($entityName),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateRepositoryContent(): string
    {
        $stub = $this->getStub('repository');
        
        $entityName = $this->config['entity_name'];
        $entityVariable = Str::camel($entityName);
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{entityName}}' => $entityVariable,
            '{{searchableFields}}' => $this->generateSearchableFields(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateSearchableFields(): string
    {
        $searchableFields = [];
        
        foreach ($this->config['fields'] as $field) {
            if (in_array($field['type'], ['string', 'text'])) {
                $searchableFields[] = "'{$field['name']}'";
            }
        }
        
        return '[' . implode(', ', $searchableFields) . ']';
    }
}