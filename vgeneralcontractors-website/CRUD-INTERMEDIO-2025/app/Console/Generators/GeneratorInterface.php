<?php

namespace App\Console\Generators;

interface GeneratorInterface
{
    /**
     * Generate files based on configuration
     *
     * @param array $config Entity configuration
     * @param bool $force Overwrite existing files
     * @return array List of generated file paths
     */
    public function generate(array $config, bool $force = false): array;

    /**
     * Check if files already exist
     *
     * @param array $config Entity configuration
     * @return array List of existing file paths
     */
    public function getExistingFiles(array $config): array;

    /**
     * Get the type name of this generator
     *
     * @return string
     */
    public function getType(): string;
}