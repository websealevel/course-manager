<?php

namespace Wsl\CourseManager;


interface IFile
{
    public function path(): string;
    public function fullName(): string;
}
