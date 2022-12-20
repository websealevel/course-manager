<?php

namespace Wsl\CourseManager\Models;

/**
 * Un simple modèle de fichier.
 */
class File
{
    public function __construct(
        public readonly string $name,
        public readonly string $content,
        public readonly string $description =''
    ) {
    }
}
