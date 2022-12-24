<?php


namespace Wsl\CourseManager\Models;

use Wsl\CourseManager\Models\AbstractNode;
use Wsl\CourseManager\Services\DefaultContent;
use Wsl\CourseManager\Config;
use Wsl\CourseManager\Services\FileManager;

/**
 * Class représentant un projet de gestion de cours
 */
class Project extends AbstractNode
{

    public function __construct(
        public readonly string $absPath
    ) {
    }

    public function getAbsPathOfParentDirectory(): string
    {
        return $this->absPath;
    }

    /**
     * @return Directory[]
     */
    public function getDefaultDirectories(): array
    {
        return array(
            new Directory('sources'),
            new Directory('templates'),
            new Directory('public')
        );
    }

    public function getDefaultFiles(): array
    {
        return array(
            new File('index.html', ''),
            new File('config.ini', Config::configIniContent())
        );
    }

    public function hookAfterBuilding(): void
    {
        //Mettre le contenu par défaut du fichier index.html (listing des fichiers)
        $tree = FileManager::treeStructureUnderPath($this->absPath);
        var_dump($tree);
    }
}
