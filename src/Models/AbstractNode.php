<?php

namespace Wsl\CourseManager\Models;

use Wsl\CourseManager\Models\INode;
use Wsl\CourseManager\Services\FileManager;

/**
 * Classe abstraite représentant un noeud (projet, cours, module)
 * Utilise le DP Template Method pour creer les repertoires et les fichiers.
 */
abstract class AbstractNode implements INode
{
     /**
     * Action: Execution du build (création des fichiers et des dossiers) et des hooks
     * @param string $label Un label décrivant le noeud qui va être crée.
     * @throws \Exception
     */
    public function create(string $label = 'directory'): void
    {
        $this->hookBeforeBuilding();
        $this->build($label);
        $this->hookAfterBuilding();
    }

    /**
     * Action: crée les dossiers et les fichiers par défaut du Noeud.
     * @param string $label Un label décrivant le noeud
     * @return void
     * @throws \Exception
     */
    private function build($label): void
    {

        $path =  $this->getAbsPathOfParentDirectory();

        //Creation du dossier du noeud
        FileManager::createDirectory($path, 'course');

        //Création des fichiers par défaut a la racine du Noeud
        foreach ($this->getDefaultFiles() as $file) {
            FileManager::createFile(sprintf("%s/%s", $path, $file->name), $file->content);
        }

        //Création des sous-dossiers du noeud (recursif)
        foreach ($this->getDefaultDirectories() as $dir) {
            $this->createDirectoryAndItsContent(sprintf("%s/%s", $path, $dir->name), $dir);
        }
    }

    /**
     * Action: cree le repertoire et son contenu de manière récursive (repertoire et fichiers)
     * @param string $path Le chemin absolu du repertoire à créer
     * @param Directory $dir Le repertoire et son contenu à créer
     */
    public function createDirectoryAndItsContent(string $path, Directory $dir)
    {

        FileManager::createDirectory($path);

        if ($dir->hasFiles()) {
            foreach ($dir->files as $file) {
                FileManager::createFile(
                    sprintf("%s/%s", $path, $file->name),
                    $file->content
                );
            }
        }

        if ($dir->hasDirs()) {
            foreach ($dir->directories as $subDir) {
                $this->createDirectoryAndItsContent(
                    sprintf("%s/%s", $path, $subDir->name),
                    $subDir
                );
            }
        }
    }


    /**
     * Retourne le repertoire par défaut par nom
     * @return Directory
     * @throws \Exception Si le repertoire par défaut n'existe pas
     */
    public function getDefaultDirectory(string $name): Directory
    {
        $defaultDirectories = $this->getDefaultDirectories();

        foreach ($defaultDirectories as $dir) {
            if ($dir->name === $name) {
                return $dir;
            }
        }

        throw new \Exception("Le repertoire %s n'existe pas");
    }

    /**
     * Hook mis à disposition pour executer du code avant le build
     */
    protected function hookBeforeBuilding(): void
    {
    }

    /**
     * Hook mis à disposition pour executer du code après le build
     */
    protected function hookAfterBuilding(): void
    {
    }
}
