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
     * Action: crée les dossiers et les fichiers par défaut du Noeud.
     * @return void
     * @throws Exception
     */
    private function build(): void
    {

        //Création des dossiers par défaut
        foreach ($this->getDefaultDirectories() as $dir) {
            //Possibilité de recursion ici avec le Model Directory.
            $absPath = sprintf("%s/%s", $this->getAbsPathOfRootDirectory(), $dir->name);
            FileManager::createDirectory($absPath);
        }

        //Création des fichiers par défaut
        foreach ($this->getDefaultFiles() as $file) {
            $absPath =  sprintf("%s/%s", $this->getAbsPathOfRootDirectory(), $file->name);
            FileManager::createFile($absPath, $file->content);
        }
    }

    /**
     * Hook mis à disposition pour executer du code avant le build
     */
    protected function hookBeforeBuilding()
    {
    }

    /**
     * Hook mis à disposition pour executer du code après le build
     */
    protected function hookAfterBuilding()
    {
    }

    /**
     * Action: Execution du build (création des fichiers et des dossiers) et des hooks
     * @throws Exception
     */
    public function create()
    {
        $this->hookBeforeBuilding();
        $this->build();
        $this->hookAfterBuilding();
    }
}
