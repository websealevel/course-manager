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

    public function fullName(): string
    {
        return sprintf("%s-%s", strtolower($this->level), strtolower($this->name));
    }

    public function path(): string
    {
        return $this->vendor . '/' . $this->fullName($this->level, $this->name);
    }
}
