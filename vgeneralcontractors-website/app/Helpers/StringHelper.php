<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StringHelper
{
    /**
     * Estimate reading time for content
     *
     * @param string $content
     * @param int $wordsPerMinute
     * @return int
     */
    public static function readDuration($content, $wordsPerMinute = 200)
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / $wordsPerMinute);
        
        return max(1, $minutes); // Asegurar que siempre devuelva al menos 1 minuto
    }
} 