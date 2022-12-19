<?php

namespace Wsl\CourseManager\Models;


use Wsl\CourseManager\Env;
use Wsl\CourseManager\FileManager;

/**
 * Un module est une partie d'un cours.
 */
class Module
{
    public function __construct(
        readonly public Env $env,
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
     * Retourne le fichier markdown du cours
     * @return string
     */
    public function slidesDeckMarkdownFile(): string
    {
        return sprintf("%02d-%s.md", $this->id, $this->name);
    }

    public function prepareModuleDirectory(string $coursePath, string $courseLevel = '')
    {
        //Creation des sous directory du module
        foreach ($this->directories as $dir) {
            $relativePath = sprintf("%s/%s/%s", $coursePath, self::fullName(), $dir);
            $absPath = $this->env->fullPath($relativePath);
            FileManager::createDirectory($absPath);
        }

        //Création de la présentation dans le subdir 'cours'
        $slidesDeckRelativePath =  sprintf(
            "%s/%s/cours/%s",
            $coursePath,
            self::fullName(),
            self::slidesDeckMarkdownFile()
        );

        $absPath = $this->env->fullPath($slidesDeckRelativePath);
        FileManager::createFile($absPath, DefaultContent::marpFirstSlide($this->name, $courseLevel));
    }
}
