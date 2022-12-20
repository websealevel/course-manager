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
        return $this->course->absPath();
    }

}
