<?php

namespace Wsl\CourseManager;

use Wsl\CourseManager\Module;
use Wsl\CourseManager\IFile;

/**
 * Une classe qui décrit un cours. Un cours est placé dans un vendor (établissement, organisme, etc.)
 * et est définir par un niveau et un nom.
 */
class Course implements IFile
{
    public function __construct(
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
    public function createDirectory()
    {

        //Création du dossier du cours
        FileManager::mkdirp($this->path());

        $dirsToCreate = array(
            'Bibliographie',
        );
        $filesToCreate = array(
            'README.md' => $this->readmeContent($course->name),
            'index.html' => $this->indexHtmlContent($course->name)
        );

        foreach ($dirsToCreate as $dir) {
            $this->mkdirp(
                sprintf("%s/%s", $course->path(), $dir)
            );
        }

        foreach ($filesToCreate as $file => $content) {
            $file = fopen(
                sprintf("%s/%s", $this->fullPath($course->path()), $file),
                'w'
            );
            fwrite($file, $content);
            fclose($file);
        }

        //Creation du module de presentation
        $module = new Module(
            $course,
            0,
            'presentation'
        );

        //Creation des sous directory du module
        foreach ($module->directories as $dir) {
            $this->mkdirp(
                sprintf("%s/%s", $module->path(), $dir)
            );
        }

        //Creation du fichier de cours
        $file = fopen(
            sprintf(
                "%s/%s/cours/%s",
                $this->fullPath($module->course->path()),
                $module->fullName(),
                $module->slidesDeckMarkdownFile()
            ),
            'w'
        );

        fwrite($file, DefaultContent::marpFirstSlide($module->name, $module->course->level));
        fclose($file);
    }
}
