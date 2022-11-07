<?php

namespace Wsl\CourseManager;

use Wsl\CourseManager\IFile;
use Wsl\CourseManager\Module;
use Wsl\CourseManager\FileManager;
use Wsl\CourseManager\DefaultContent;

/**
 * Une classe qui décrit un cours. Un cours est placé dans un vendor (établissement, organisme, etc.)
 * et est définir par un niveau et un nom.
 */
class Course
{
    public function __construct(
        readonly public Env $env,
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $name,
        readonly public array $modules = array(
            new Module(0, 'presentation')
        )
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

    /**
     * Initialise le répertoire d'un cours nouvellement créee (création de dossiers et de fichiers par défaut)
     * @return void
     */
    public function createCourseDirectory()
    {
        //Création du dossier du cours
        FileManager::createDirectory($this->env->fullPath($this->path()));

        $dirsToCreate = array(
            'Bibliographie',
        );
        $filesToCreate = array(
            'README.md' => DefaultContent::readmeContent($this->name),
            'index.html' => DefaultContent::indexHtmlContent($this->name)
        );

        //Création des sous dossiers par défaut
        foreach ($dirsToCreate as $dir) {
            $relativePath = sprintf("%s/%s", $this->path(), $dir);
            $absPath = $this->env->fullPath($relativePath);
            FileManager::createDirectory($absPath);
        }

        //Création des fichiers par défaut
        foreach ($filesToCreate as $file => $content) {
            $relativePath =  sprintf("%s/%s", $this->path(), $file);
            $absPath = $this->env->fullPath($relativePath);
            FileManager::createFile($absPath);
        }

        //Creation des modules par défaut

        //Creation des sous directory du module
        foreach ($this->modules as $module) {
            foreach ($module->directories as $dir) {
                $relativePath = sprintf("%s/%s/%s", $this->path(), $module->fullName(), $dir);
                $absPath = $this->env->fullPath($relativePath);
                FileManager::createDirectory($absPath);
            }
        }

        // //Creation du fichier de cours
        // $file = fopen(
        //     sprintf(
        //         "%s/%s/cours/%s",
        //         $this->fullPath($module->course->path()),
        //         $module->fullName(),
        //         $module->slidesDeckMarkdownFile()
        //     ),
        //     'w'
        // );

        // fwrite($file, DefaultContent::marpFirstSlide($module->name, $module->course->level));
        // fclose($file);
    }
}
