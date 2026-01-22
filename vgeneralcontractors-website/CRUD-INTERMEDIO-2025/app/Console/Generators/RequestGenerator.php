<?php

namespace App\Console\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class RequestGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $fileName = $this->config['entity_name'] . 'Request.php';
        $filePath = app_path('Http/Requests/' . $fileName);
        
        if (File::exists($filePath)) {
            return [
                'status' => 'skipped',
                'message' => 'Request already exists',
                'path' => $filePath
            ];
        }
        
        $this->ensureDirectoryExists(dirname($filePath));
        
        $content = $this->generateRequestContent();
        
        File::put($filePath, $content);
        
        return [
            'status' => 'created',
            'message' => 'Request created successfully',
            'path' => $filePath
        ];
    }
    
    public function getExistingFiles(): array
    {
        $fileName = $this->config['entity_name'] . 'Request.php';
        $filePath = app_path('Http/Requests/' . $fileName);
        
        return File::exists($filePath) ? [$filePath] : [];
    }
    
    public function getType(): string
    {
        return 'request';
    }
    
    private function generateRequestContent(): string
    {
        $stub = $this->getStub('request');
        
        $entityName = $this->config['entity_name'];
        
        $replacements = [
            '{{EntityName}}' => $entityName,
            '{{rules}}' => $this->generateRules(),
            '{{messages}}' => $this->generateMessages(),
            '{{attributes}}' => $this->generateAttributes(),
        ];
        
        return $this->processContent($stub, $replacements);
    }
    
    private function generateRules(): string
    {
        $rules = [];
        
        foreach ($this->config['fields'] as $field) {
            $fieldRules = $this->generateFieldRules($field);
            if (!empty($fieldRules)) {
                $rules[] = "            '{$field['name']}' => '{$fieldRules}'";
            }
        }
        
        return implode(",\n", $rules);
    }
    
    private function generateFieldRules(array $field): string
    {
        $rules = [];
        
        // Required/nullable
        if (!($field['nullable'] ?? false)) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        // Type-specific rules
        switch ($field['type']) {
            case 'string':
                $rules[] = 'string';
                if (isset($field['max_length'])) {
                    $rules[] = 'max:' . $field['max_length'];
                } else {
                    $rules[] = 'max:255';
                }
                break;
                
            case 'text':
            case 'longtext':
                $rules[] = 'string';
                break;
                
            case 'integer':
            case 'biginteger':
                $rules[] = 'integer';
                if (isset($field['min'])) {
                    $rules[] = 'min:' . $field['min'];
                }
                if (isset($field['max'])) {
                    $rules[] = 'max:' . $field['max'];
                }
                break;
                
            case 'boolean':
                $rules[] = 'boolean';
                break;
                
            case 'decimal':
            case 'float':
            case 'double':
                $rules[] = 'numeric';
                if (isset($field['min'])) {
                    $rules[] = 'min:' . $field['min'];
                }
                if (isset($field['max'])) {
                    $rules[] = 'max:' . $field['max'];
                }
                break;
                
            case 'date':
                $rules[] = 'date';
                break;
                
            case 'datetime':
            case 'timestamp':
                $rules[] = 'date_format:Y-m-d H:i:s';
                break;
                
            case 'email':
                $rules[] = 'email';
                break;
                
            case 'enum':
                if (isset($field['enum_values'])) {
                    $enumValues = implode(',', $field['enum_values']);
                    $rules[] = "in:{$enumValues}";
                }
                break;
                
            case 'json':
                $rules[] = 'array';
                break;
        }
        
        // Custom rules from field configuration
        if (isset($field['rules'])) {
            if (is_array($field['rules'])) {
                $rules = array_merge($rules, $field['rules']);
            } else {
                $rules[] = $field['rules'];
            }
        }
        
        return implode('|', $rules);
    }
    
    private function generateMessages(): string
    {
        $messages = [];
        
        foreach ($this->config['fields'] as $field) {
            $fieldName = $field['name'];
            $fieldLabel = $this->generateFieldLabel($fieldName);
            
            // Required message
            if (!($field['nullable'] ?? false)) {
                $messages[] = "            '{$fieldName}.required' => 'El campo {$fieldLabel} es obligatorio.'";
            }
            
            // Type-specific messages
            switch ($field['type']) {
                case 'string':
                case 'text':
                case 'longtext':
                    $messages[] = "            '{$fieldName}.string' => 'El campo {$fieldLabel} debe ser una cadena de texto.'";
                    $messages[] = "            '{$fieldName}.max' => 'El campo {$fieldLabel} no debe exceder :max caracteres.'";
                    break;
                    
                case 'integer':
                case 'biginteger':
                    $messages[] = "            '{$fieldName}.integer' => 'El campo {$fieldLabel} debe ser un número entero.'";
                    break;
                    
                case 'boolean':
                    $messages[] = "            '{$fieldName}.boolean' => 'El campo {$fieldLabel} debe ser verdadero o falso.'";
                    break;
                    
                case 'email':
                    $messages[] = "            '{$fieldName}.email' => 'El campo {$fieldLabel} debe ser una dirección de correo válida.'";
                    break;
                    
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $messages[] = "            '{$fieldName}.date' => 'El campo {$fieldLabel} debe ser una fecha válida.'";
                    break;
                    
                case 'enum':
                    if (isset($field['enum_values'])) {
                        $enumValues = implode(', ', $field['enum_values']);
                        $messages[] = "            '{$fieldName}.in' => 'El campo {$fieldLabel} debe ser uno de: {$enumValues}.'";
                    }
                    break;
            }
        }
        
        return implode(",\n", $messages);
    }
    
    private function generateAttributes(): string
    {
        $attributes = [];
        
        foreach ($this->config['fields'] as $field) {
            $fieldName = $field['name'];
            $fieldLabel = $this->generateFieldLabel($fieldName);
            $attributes[] = "            '{$fieldName}' => '{$fieldLabel}'";
        }
        
        return implode(",\n", $attributes);
    }
    
    private function generateFieldLabel(string $fieldName): string
    {
        // Convert snake_case to human readable
        return ucfirst(str_replace('_', ' ', $fieldName));
    }
}