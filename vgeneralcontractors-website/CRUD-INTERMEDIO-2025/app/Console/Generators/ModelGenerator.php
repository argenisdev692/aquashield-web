<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;

class ModelGenerator extends BaseGenerator
{
    protected string $type = 'Model';
    protected string $targetDirectory = 'app/Models';

    /**
     * Get files to generate
     */
    protected function getFilesToGenerate(array $config): array
    {
        return [
            [
                'stub' => 'model',
                'filename' => $config['entity_name_pascal']
            ]
        ];
    }

    /**
     * Replace dynamic content specific to models
     */
    protected function replaceDynamicContent(string $content, array $config, array $fileConfig): string
    {
        // Generate fillable fields
        $fillableFields = $this->generateFillableFields($config['fields']);
        $content = str_replace('{{fillable_fields}}', $fillableFields, $content);

        // Generate casts
        $casts = $this->generateCasts($config['fields']);
        $content = str_replace('{{casts}}', $casts, $content);

        // Generate relationships
        $relationships = $this->generateRelationships($config);
        $content = str_replace('{{relationships}}', $relationships, $content);

        // Generate accessors and mutators
        $accessors = $this->generateAccessors($config['fields']);
        $content = str_replace('{{accessors}}', $accessors, $content);

        // Generate scopes
        $scopes = $this->generateScopes($config['fields']);
        $content = str_replace('{{scopes}}', $scopes, $content);

        return $content;
    }

    /**
     * Generate fillable fields array
     */
    private function generateFillableFields(array $fields): string
    {
        $fillableFields = ['user_id']; // Always include user_id
        
        foreach ($fields as $field) {
            $fillableFields[] = $field['name'];
        }
        
        $formattedFields = array_map(fn($field) => "'{$field}'", $fillableFields);
        
        return implode(",\n        ", $formattedFields);
    }

    /**
     * Generate casts array
     */
    private function generateCasts(array $fields): string
    {
        $casts = [
            "'uuid' => 'string'",
            "'created_at' => 'datetime'",
            "'updated_at' => 'datetime'",
            "'deleted_at' => 'datetime'"
        ];
        
        foreach ($fields as $field) {
            $cast = match ($field['type']) {
                'boolean' => "'{$field['name']}' => 'boolean'",
                'integer' => "'{$field['name']}' => 'integer'",
                'decimal' => "'{$field['name']}' => 'decimal:2'",
                'date' => "'{$field['name']}' => 'date'",
                'datetime' => "'{$field['name']}' => 'datetime'",
                'enum' => "'{$field['name']}' => " . Str::studly($field['name']) . "::class",
                default => null
            };
            
            if ($cast) {
                $casts[] = $cast;
            }
        }
        
        return implode(",\n        ", $casts);
    }

    /**
     * Generate relationships
     */
    private function generateRelationships(array $config): string
    {
        $relationships = [];
        
        // BelongsTo User relationship
        $relationships[] = "
    /**
     * Get the user that owns the {$config['entity_name_snake']}
     */
    public function user(): BelongsTo
    {
        return \$this->belongsTo(User::class);
    }";
        
        return implode("\n", $relationships);
    }

    /**
     * Generate accessors for common fields
     */
    private function generateAccessors(array $fields): string
    {
        $accessors = [];
        
        // Generate status accessor if there's a status-like field
        foreach ($fields as $field) {
            if (str_contains($field['name'], 'status') || str_contains($field['name'], 'active')) {
                $methodName = Str::camel('get_' . $field['name'] . '_label');
                $accessors[] = "
    /**
     * Get the {$field['name']} label
     */
    public function {$methodName}(): string
    {
        return match (\$this->{$field['name']}) {
            true => 'Active',
            false => 'Inactive',
            default => 'Unknown'
        };
    }";
            }
        }
        
        return implode("\n", $accessors);
    }

    /**
     * Generate query scopes
     */
    private function generateScopes(array $fields): string
    {
        $scopes = [];
        
        // Active scope if there's an active field
        foreach ($fields as $field) {
            if (str_contains($field['name'], 'active') && $field['type'] === 'boolean') {
                $scopes[] = "
    /**
     * Scope a query to only include active records
     */
    public function scopeActive(Builder \$query): void
    {
        \$query->where('{$field['name']}', true);
    }
    
    /**
     * Scope a query to only include inactive records
     */
    public function scopeInactive(Builder \$query): void
    {
        \$query->where('{$field['name']}', false);
    }";
                break;
            }
        }
        
        // User scope
        $scopes[] = "
    /**
     * Scope a query to only include records for a specific user
     */
    public function scopeForUser(Builder \$query, int \$userId): void
    {
        \$query->where('user_id', \$userId);
    }";
        
        return implode("\n", $scopes);
    }
}