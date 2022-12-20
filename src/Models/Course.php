<?php

namespace Wsl\CourseManager\Models;


use Wsl\CourseManager\Models\Module;
use Wsl\CourseManager\Services\DefaultContent;

/**
 * Une classe qui décrit un cours. Un cours est éventuellement placé dans un vendor (établissement, organisme, etc.)
 * et est défini par un nom. Un cours possède des métadonnées (niveau des apprenant·es, mots-clefs, etc.)
 */
class Course extends AbstractNode
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

    public function getAbsPathOfParentDirectory(): string
    {
        return $this->absPathRootDirectory;
    }

    public function getDefaultDirectories(): array
    {
        return array(
            new Directory('bibliographie'),
        );
    }

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
}
