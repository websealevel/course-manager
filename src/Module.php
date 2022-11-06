<?php

namespace Wsl\CourseManager;

use Wsl\CourseManager\Course;

/**
 * Un module est une partie d'un cours. Un module appartient à un Course.
 */
class Module
{

    public function __construct(
        readonly public Course $course,
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
    public function path()
    {
        return $this->course->path() . '/' . $this->fullName();
    }

    /**
     * Retourne le fichier markdown du cours
     * @return string
     */
    public function courseMarkdownFile(): string
    {
        return sprintf("%02d-%s-%s.md", $this->id, $this->name, $this->course->fullName());
    }
}
