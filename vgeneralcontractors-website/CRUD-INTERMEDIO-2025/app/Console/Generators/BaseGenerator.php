<?php

namespace App\Console\Generators;

use Illuminate\Support\Facades\File;
use App\Console\Services\CrudGeneratorService;

abstract class BaseGenerator implements GeneratorInterface
{
    /**
     * Generator type
     */
    protected string $type;

    /**
     * Stub file name
     */
    protected string $stubName;

    /**
     * Target directory
     */
    protected string $targetDirectory;

    /**
     * File extension
     */
    protected string $fileExtension = '.php';

    /**
     * Generate files based on configuration
     */
    public function generate(array $config, bool $force = false): array
    {
        $generatedFiles = [];
        
        // Check if files exist and force is not enabled
        if (!$force) {
            $existingFiles = $this->getExistingFiles($config);
            if (!empty($existingFiles)) {
                throw new \Exception(
                    "Files already exist for {$this->type}: " . implode(', ', $existingFiles) . 
                    ". Use --force to overwrite."
                );
            }
        }

        // Ensure target directory exists
        $this->ensureDirectoryExists($config);

        // Generate files
        $files = $this->getFilesToGenerate($config);
        
        foreach ($files as $fileConfig) {
            $filePath = $this->generateFile($fileConfig, $config);
            $generatedFiles[] = $filePath;
        }

        return $generatedFiles;
    }

    /**
     * Generate a single file
     */
    protected function generateFile(array $fileConfig, array $config): string
    {
        $stubContent = CrudGeneratorService::getStub($fileConfig['stub']);
        $content = $this->processContent($stubContent, $config, $fileConfig);
        
        $filePath = $this->getFilePath($fileConfig, $config);
        
        File::put($filePath, $content);
        
        return $filePath;
    }

    /**
     * Process stub content with replacements
     */
    protected function processContent(string $content, array $config, array $fileConfig): string
    {
        // Replace basic placeholders
        $content = CrudGeneratorService::replacePlaceholders($content, $config);
        
        // Replace dynamic content
        $content = $this->replaceDynamicContent($content, $config, $fileConfig);
        
        return $content;
    }

    /**
     * Replace dynamic content specific to each generator
     */
    protected function replaceDynamicContent(string $content, array $config, array $fileConfig): string
    {
        // Override in child classes for specific replacements
        return $content;
    }

    /**
     * Get file path for generated file
     */
    protected function getFilePath(array $fileConfig, array $config): string
    {
        $directory = base_path($this->getTargetDirectory($config));
        $filename = $this->getFileName($fileConfig, $config);
        
        return $directory . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Get target directory
     */
    protected function getTargetDirectory(array $config): string
    {
        return $this->targetDirectory;
    }

    /**
     * Get file name
     */
    protected function getFileName(array $fileConfig, array $config): string
    {
        return $fileConfig['filename'] . $this->fileExtension;
    }

    /**
     * Ensure target directory exists
     */
    protected function ensureDirectoryExists(array $config): void
    {
        $directory = base_path($this->getTargetDirectory($config));
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Check if files already exist
     */
    public function getExistingFiles(array $config): array
    {
        $existingFiles = [];
        $files = $this->getFilesToGenerate($config);
        
        foreach ($files as $fileConfig) {
            $filePath = $this->getFilePath($fileConfig, $config);
            if (File::exists($filePath)) {
                $existingFiles[] = $filePath;
            }
        }
        
        return $existingFiles;
    }

    /**
     * Get generator type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get files to generate - must be implemented by child classes
     */
    abstract protected function getFilesToGenerate(array $config): array;

    /**
     * Generate field definitions for migrations
     */
    protected function generateFieldDefinitions(array $fields): string
    {
        $definitions = [];
        
        foreach ($fields as $field) {
            $definition = $this->generateSingleFieldDefinition($field);
            if ($definition) {
                $definitions[] = $definition;
            }
        }
        
        return implode("\n            ", $definitions);
    }

    /**
     * Generate single field definition
     */
    protected function generateSingleFieldDefinition(array $field): string
    {
        $name = $field['name'];
        $type = $field['type'];
        
        $definition = match ($type) {
            'string' => "\$table->string('{$name}', {$field['max_length'] ?? 255})",
            'text' => "\$table->text('{$name}')",
            'integer' => "\$table->integer('{$name}')",
            'boolean' => "\$table->boolean('{$name}')",
            'decimal' => "\$table->decimal('{$name}', 8, 2)",
            'date' => "\$table->date('{$name}')",
            'datetime' => "\$table->dateTime('{$name}')",
            'enum' => "\$table->enum('{$name}', ['" . implode("', '", $field['enum_values'] ?? []) . "'])",
            default => null
        };
        
        if (!$definition) {
            return '';
        }
        
        // Add nullable if not required
        if (!($field['required'] ?? true)) {
            $definition .= '->nullable()';
        }
        
        return $definition . ';';
    }
}