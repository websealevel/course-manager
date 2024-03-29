<?php

namespace Wsl\CourseManager\Models;

use Wsl\CourseManager\Models\Module;
use Wsl\CourseManager\Services\DefaultContent;

/**
 * Une classe qui décrit un cours. Un cours est éventuellement placé dans un vendor (établissement, organisme, etc.)
 * et est défini par un nom. Un cours possède des métadonnées (niveau des apprenant·es, mots-clefs, etc.). Un cours
 * est composé de modules.
 */
class Course extends AbstractNode
{
    /**
     * Liste des modules contenus dans le cours
     * @var Module[]
     */
    readonly public array $modules;

    public function __construct(
        readonly public string $name,
        public readonly string $absPathProjectDirectory,
        readonly public string $vendor,
        readonly public string $level,
        readonly public string $keyWords,
    ) {

        //Initialisation des modules par défaut
        $this->modules = array(
            new Module($this, 0, 'presentation')
        );
    }

    protected function hookAfterBuilding(): void
    {
        //Création des modules par défaut.
        foreach ($this->modules as $module) {
            $module->create();
        }
    }

    public function getAbsPathOfParentDirectory(): string
    {
        return sprintf("%s/%s", $this->absPathProjectDirectory, $this->path());
    }

    /**
     * Retourne les répertoires contenus par défaut dans un cours
     * @return array
     */
    public function getDefaultDirectories(): array
    {
        return array(
            new Directory('bibliographie'),
        );
    }

    /**
     * Retourne les fichiers contenus par défaut dans un cours
     * @return array
     */
    public function getDefaultFiles(): array
    {

        $metaDataContent = <<< INI
        name={$this->name}
        level={$this->level}
        keywords={$this->keyWords}
        INI;

        return array(
            new File('README.md', DefaultContent::readmeContent($this->name)),
            new File('index.html', DefaultContent::indexHtmlContent($this->name)),
            new File('.metadata', $metaDataContent)
        );
    }

    /**
     * Retourne le chemin relatif du cours par rapport au projet courant
     * @return string
     */
    public function path(): string
    {
        if (empty($this->vendor)) {
            return sprintf("%s", strtolower($this->name));
        }

        return sprintf("%s/%s", strtolower($this->vendor), strtolower($this->name));
    }

    /**
     * Retourne le chemin absolu du cours
     * @return string
     */
    public function absPath(): string
    {
        return sprintf("%s/%s", $this->absPathProjectDirectory, $this->path());
    }
}
