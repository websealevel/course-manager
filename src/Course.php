<?php

namespace Wsl\CourseManager;

class Course
{
    public function __construct(
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $name
    ) {
    }

    /**
     * Retourne le nom complet du cours $level-$name dans $vendor
     * @return string
     */
    public function fullName(): string
    {
        return sprintf("%s-%s", strtolower($this->level), strtolower($this->name));
    }

    /**
     * Retourne le chemin relatif du cours par rappor à la racine
     * @return string
     */
    public function path(): string
    {
        return $this->vendor . '/' . $this->fullName($this->level, $this->name);
    }
}
