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
     * @param string $label Un label qui décrit ce que l'on build
     * @return void
     * @throws Exception
     */
    private function build($label): void
    {

        //Création des dossiers par défaut
        foreach ($this->getDefaultDirectories() as $dir) {
            //Possibilité de recursion ici avec le Model Directory.
            $absPath = sprintf("%s/%s", $this->getAbsPathOfParentDirectory(), $dir->name);
            FileManager::createDirectory($absPath, $label);
        }

        //Création des fichiers par défaut
        foreach ($this->getDefaultFiles() as $file) {
            $absPath =  sprintf("%s/%s", $this->getAbsPathOfParentDirectory(), $file->name);
            FileManager::createFile($absPath, $file->content);
        }
    }


    /**
     * Retourne le repertoire par défaut par nom
     * @return Directory
     * @throws Exception Si le repertoire par défaut n'existe pas
     */
    public function getDefaultDirectory(string $name): Directory
    {
        $defaultDirectories = $this->getDefaultDirectories();

        foreach ($defaultDirectories as $dir) {
            if ($dir->name === $name)
                return $dir;
        }

        throw new \Exception("Le repertoire %s n'existe pas");
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
    public function create($label = 'directory')
    {
        $this->hookBeforeBuilding();
        $this->build($label);
        $this->hookAfterBuilding();
    }
}
