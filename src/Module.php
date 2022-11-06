<?php

namespace Wsl\CourseManager;

use Wsl\CourseManager\Course;

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
        return sprintf("module-%d-%s", $this->id, $this->name);
    }

    /**
     * Retourne la path complet du module relatif à la racine du projet
     * @return string
     */
    public function path()
    {
        return $this->course->path() . '/' . $this->fullName();
    }
}
