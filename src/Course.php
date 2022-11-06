<?php

namespace Wsl\CourseManager;

use Wsl\CourseManager\IFile;

/**
* Une classe qui décrit un cours. Un cours est placé dans un vendor (établissement, organisme, etc.) et est définir par un niveau et un nom.
*/
class Course implements IFile
{
    public function __construct(
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $name
    ) {
    }

    /**
     * Retourne le nom complet du cours ($level-$name)
     * @return string
     */
    public function fullName(): string
    {
        return sprintf("%s-%s", strtolower($this->level), strtolower($this->name));
    }

    /**
     * Retourne le chemin du cours
     * @return string
     */
    public function path(): string
    {
        return sprintf("%s/%s", $this->vendor, $this->fullName());
    }
}
