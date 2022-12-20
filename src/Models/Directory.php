<?php

namespace Wsl\CourseManager\Models;

/**
 * Une classe représentant un répertoire
 */
class Directory
{
    public function __construct(
        public readonly string $name,
        public readonly array $directories = array(),
        public readonly array $files = array()
    ) {
    }

    /**
     * Retourne vrai si le repertoire est supposé contenir
     * des fichiers, faux sinon.
     */
    public function hasFiles(): bool
    {
        return 0 !== count($this->files);
    }
}
