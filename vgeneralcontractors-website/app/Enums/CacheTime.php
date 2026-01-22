<?php

namespace App\Enums;

enum CacheTime: int
{
    case SHORT = 60;        // 1 minute
    case MEDIUM = 300;      // 5 minutes
    case LONG = 1800;       // 30 minutes
    case EXTENDED = 3600;   // 1 hour
    case DAILY = 86400;     // 24 hours
    case WEEKLY = 604800;   // 7 days

    public function label(): string
    {
        return match($this) {
            self::SHORT => '1 minute',
            self::MEDIUM => '5 minutes',
            self::LONG => '30 minutes',
            self::EXTENDED => '1 hour',
            self::DAILY => '24 hours',
            self::WEEKLY => '7 days'
        };
    }

    public function getSeconds(): int
    {
        return $this->value;
    }

    public static function forEntity(string $entityType): self
    {
        return match($entityType) {
            'insurance_companies' => self::MEDIUM,
            'users' => self::LONG,
            'settings' => self::EXTENDED,
            'static_data' => self::DAILY,
            default => self::SHORT
        };
    }
}