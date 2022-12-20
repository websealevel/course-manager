<?php

namespace Wsl\CourseManager\Models;


use Wsl\CourseManager\Services\DefaultContent;
use Wsl\CourseManager\Models\AbstractNode;
use Wsl\CourseManager\Services\FileManager;

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

    public function getDefaultDirectories(): array
    {
        return array(
            new Directory(name: 'cours', files: array(
                new File(
                    'slides.md',
                    DefaultContent::marpFirstSlide(
                        $this->name,
                        $this->course->level
                    ),
                    'Les diapos du cours'
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

    public function path(): string{
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
