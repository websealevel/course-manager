<?php

namespace Wsl\CourseManager\Models;


use Wsl\CourseManager\Models\Module;
use Wsl\CourseManager\Services\FileManager;
use Wsl\CourseManager\Services\DefaultContent;

/**
 * Une classe qui décrit un cours. Un cours est éventuellement placé dans un vendor (établissement, organisme, etc.)
 * et est défini par un nom. Un cours possède des métadonnées (niveau des apprenant·es, mots-clefs, etc.)
 */
class Course
{
    readonly public array $modules;

    public function __construct(
        readonly public string $name,
        public readonly string $absPath,
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $keyWords,
    ) {

        $this->modules = array(new Module($this->absPath, 0, 'presentation'));
    }

    /**
     * Retourne le nom complet (path relatif au projet courant) du cours
     * @return string
     */
    public function fullName(): string
    {
        return sprintf("%s/%s", strtolower($this->vendor), strtolower($this->name));
    }

    /**
     * Retourne le chemin du cours
     * @return string
     */
    public function path(): string
    {
        return sprintf("%s/%s", strtolower($this->vendor), strtolower($this->name));
    }

    /**
     * Initialise le répertoire d'un cours nouvellement créee (création de dossiers et de fichiers par défaut)
     * @return void
     */
    public function createCourseDirectory()
    {

        //Création du dossier du cours. On vérifie que le cours n'existe pas déjà.
        FileManager::createDirectory($this->env->fullPath($this->path()));

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
