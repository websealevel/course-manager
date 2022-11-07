<?php

namespace Wsl\CourseManager;

use Exception;
use Wsl\CourseManager\Module;
use Wsl\CourseManager\FileManager;
use Wsl\CourseManager\DefaultContent;

/**
 * Une classe qui décrit un cours. Un cours est placé dans un vendor (établissement, organisme, etc.)
 * et est définir par un niveau et un nom.
 * Un cours est identifié par un vendor, level et name.
 */
class Course
{
    readonly public array $modules;

    public function __construct(
        public Env $env,
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $name,
    ) {

        $this->modules = array( new Module($this->env, 0, 'presentation'));
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

        //Création du dossier du cours. On vérifie que le cours n'existe pas déjà.
        try {
            FileManager::createDirectory($this->env->fullPath($this->path()));
        } catch (Exception $e) {
            Console::print($e->getMessage());
            return;
        }

        $defaultDirs = array(
            'bibliographie',
        );
        $defaultFiles = array(
            'README.md' => DefaultContent::readmeContent($this->name),
            'index.html' => DefaultContent::indexHtmlContent($this->name)
        );

        //Création des sous dossiers par défaut
        foreach ($defaultDirs as $dir) {
            $relativePath = sprintf("%s/%s", $this->path(), $dir);
            $absPath = $this->env->fullPath($relativePath);
            FileManager::createDirectory($absPath);
        }

        //Création des fichiers par défaut
        foreach ($defaultFiles as $file => $content) {
            $relativePath =  sprintf("%s/%s", $this->path(), $file);
            $absPath = $this->env->fullPath($relativePath);
            FileManager::createFile($absPath, $content);
        }

        //Préparation des repertoires des modules
        foreach ($this->modules as $module) {
            $module->prepareModuleDirectory($this->path(), $this->level);
        }
    }
}
