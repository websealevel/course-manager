<?php

namespace Wsl\CourseManager;

use Wsl\CourseManager\IFile;

/**
 * Un module est une partie d'un cours.
 */
class Module implements IFile
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
     * Retourne la path du module relatif à la racine du projet
     * @return string
     */
    public function path(): string
    {
        return $this->course->path() . '/' . $this->fullName();
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
