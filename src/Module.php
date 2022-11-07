<?php

namespace Wsl\CourseManager;

/**
 * Un module est une partie d'un cours.
 */
class Module
{
    public function __construct(
        readonly public int $id,
        readonly public string $name,
        readonly public array $directories = array(
            'cours',
            'exercices',
            'tp'
        )
    ) {
    }

    /**
     * Retourne le nom complet du dossier du module
     * @return string
     */
    public function fullName(): string
    {
        return sprintf("module-%02d-%s", $this->id, $this->name);
    }

    /**
     * Retourne le fichier markdown du cours
     * @return string
     */
    public function slidesDeckMarkdownFile(): string
    {
        return sprintf("%02d-%s.md", $this->id, $this->name);
    }
}
