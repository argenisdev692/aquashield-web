<?php

namespace App\Enums;

enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';

    public function isReadOnly(): bool
    {
        return match($this) {
            self::GET, self::HEAD, self::OPTIONS => true,
            default => false
        };
    }

    public function requiresValidation(): bool
    {
        return match($this) {
            self::POST, self::PUT, self::PATCH => true,
            default => false
        };
    }

    public function allowsBody(): bool
    {
        return match($this) {
            self::POST, self::PUT, self::PATCH => true,
            default => false
        };
    }
}