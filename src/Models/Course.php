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
        public readonly string $absPathRootDirectory,
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $keyWords,
    ) {

        $this->modules = array(new Module($this->absPathRootDirectory, 0, 'presentation'));
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
     * Retourne le chemin relatif du cours
     * @return string
     */
    public function path(): string
    {
        return sprintf("%s/%s", strtolower($this->vendor), strtolower($this->name));
    }

    /**
     * Retourne le chemin absolu du cours
     * @return string
     */
    public function absPath(): string
    {
        return sprintf("%s%s", $this->absPathRootDirectory, $this->path());
    }

    /**
     * Initialise le répertoire d'un cours nouvellement créee (création de dossiers et de fichiers par défaut)
     * @return bool
     */
    public function create(): bool
    {

        FileManager::createDirectory($this->absPath(), 'cours');

        $defaultDirs = array(
            'bibliographie',
        );

        $metaDataContent = <<< INI
        name={$this->name}
        level={$this->level}
        keywords={$this->keywords}
        INI;

        $defaultFiles = array(
            'README.md' => DefaultContent::readmeContent($this->name),
            'index.html' => DefaultContent::indexHtmlContent($this->name),
            '.metadata' => $metaDataContent
        );

        //Création des sous dossiers par défaut
        foreach ($defaultDirs as $dir) {
            $absPath = sprintf("%s/%s", $this->absPath(), $dir);
            FileManager::createDirectory($absPath);
        }

        //Création des fichiers par défaut
        foreach ($defaultFiles as $file => $content) {
            $absPath =  sprintf("%s/%s", $this->absPath(), $file);
            FileManager::createFile($absPath, $content);
        }

        //Préparation des repertoires des modules
        foreach ($this->modules as $module) {
            // $module->prepareModuleDirectory($this->path(), $this->level);
        }

        return true;
    }
}
