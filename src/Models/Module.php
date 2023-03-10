<?php

namespace Wsl\CourseManager\Models;


use Wsl\CourseManager\Services\DefaultContent;
use Wsl\CourseManager\Models\AbstractNode;

/**
 * Un module est une partie d'un cours.
 */
class Module extends AbstractNode
{
    public function __construct(
        readonly public Course $course,
        readonly public int $id,
        readonly public string $name,
    ) {
    }

    /**
     * Retourne les repertoires par défaut d'un module
     * @return array
     */
    public function getDefaultDirectories(): array
    {
        //Doit être configurable par l'utilisateur·ice
        return array(
            new Directory(name: 'cours', files: array(
                new File(
                    'cours.md',
                    DefaultContent::marpFirstSlide(
                        $this->name,
                        $this->course->level
                    ),
                    'Le contenu du cours'
                )
            )),
            new Directory('exercices')
        );
    }

    public function getDefaultFiles(): array
    {
        return array();
    }

    public function getAbsPathOfParentDirectory(): string
    {
        return sprintf("%s/%s", $this->course->absPath(), $this->path());
    }

    public function path(): string
    {
        return $this->name();
    }

    /**
     * Retourne le nom formatté du module
     * @return string
     */
    public function name(): string
    {
        return sprintf("module%02d-%s", $this->id, $this->name);
    }
}
