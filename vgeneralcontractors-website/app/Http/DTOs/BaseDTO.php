<?php

namespace App\Http\DTOs;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;
use ReflectionNamedType;

abstract class BaseDTO implements JsonSerializable, Arrayable
{
    /**
     * Constructor to initialize DTO properties from array data
     */
    public function __construct(array $data = [])
    {
        $this->fillFromArray($data);
        $this->validateData();
    }

    /**
     * Fill DTO properties from array data using PHP 8.4 property hooks
     */
    protected function fillFromArray(array $data): void
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_READONLY);
        
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            
            if (array_key_exists($propertyName, $data)) {
                $value = $this->castValue($data[$propertyName], $property);
                $property->setValue($this, $value);
            } elseif ($property->hasType() && !$property->getType()?->allowsNull()) {
                // Set default values for non-nullable properties
                $type = $property->getType();
                if ($type instanceof ReflectionNamedType) {
                    $defaultValue = match($type->getName()) {
                        'string' => '',
                        'int' => 0,
                        'float' => 0.0,
                        'bool' => false,
                        'array' => [],
                        default => null
                    };
                    $property->setValue($this, $defaultValue);
                }
            }
        }
    }

    /**
     * Cast value based on property type with strict typing
     */
    protected function castValue(mixed $value, ReflectionProperty $property): mixed
    {
        if (!$property->hasType()) {
            return $value;
        }

        $type = $property->getType();
        if (!$type instanceof ReflectionNamedType) {
            return $value;
        }

        return match($type->getName()) {
            'string' => $value !== null ? (string) $value : null,
            'int' => $value !== null ? (int) $value : null,
            'float' => $value !== null ? (float) $value : null,
            'bool' => $value !== null ? (bool) $value : null,
            'array' => is_array($value) ? $value : ($value !== null ? [$value] : []),
            default => $value
        };
    }

    /**
     * Validate data - to be overridden by child classes
     */
    protected function validateData(): void
    {
        // Default implementation does nothing
        // Child classes can override to add validation
    }

    /**
     * Create a new DTO instance from array data
     */
    public static function from(array $data): static
    {
        return new static($data);
    }

    /**
     * Create DTO instance from model
     */
    public static function fromModel(object $model): static
    {
        return new static($model->toArray());
    }

    /**
     * Create collection of DTOs from array of data
     */
    public static function collection(iterable $collection): Collection
    {
        return collect($collection)->map(fn($item) => 
            static::from(is_array($item) ? $item : $item->toArray())
        );
    }

    /**
     * Create a new DTO instance from request data
     */
    public static function fromRequest(object $request): static
    {
        $data = is_array($request) ? $request : $request->validated();
        return static::from($data);
    }

    /**
     * Convert DTO to array using PHP 8.4 features
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_READONLY);
        
        $array = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $array[$propertyName] = $property->getValue($this);
        }
        
        return $array;
    }

    /**
     * Convert DTO to JSON
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get only non-null values as array
     */
    public function toArrayFiltered(): array
    {
        return array_filter($this->toArray(), fn($value) => $value !== null);
    }

    /**
     * Check if DTO has any data
     */
    public function isEmpty(): bool
    {
        return empty(array_filter($this->toArray()));
    }

    /**
     * Get specific fields as array
     */
    public function only(array $fields): array
    {
        return array_intersect_key($this->toArray(), array_flip($fields));
    }

    /**
     * Exclude specific fields from array
     */
    public function except(array $fields): array
    {
        return array_diff_key($this->toArray(), array_flip($fields));
    }

    /**
     * Merge with another array or DTO
     */
    public function merge(array|self $data): array
    {
        $mergeData = $data instanceof self ? $data->toArray() : $data;
        return [...$this->toArray(), ...$mergeData];
    }

    /**
     * Convert to JSON string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}