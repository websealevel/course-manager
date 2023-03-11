<?php

namespace Wsl\CourseManager\Services;

use Wsl\CourseManager\Services\FileManager;

/**
 * Service en charge d'inspecter le projet et de remonter les données des fichiers
 */
class Loader
{
    /**
     * Retourne la liste des cours dans le projet sous le path
     * @param string $path Le path du projet à inspecter
     * @return Course[] La liste des cours
     */
    public static function coursesUnderProject(string $path): array
    {

        //Scanner tous les dossiers sous le path
        $tree  = FileManager::buildTreeFromPath($path);

        //Créer des instances de cours
        
        //Charger les modules
        
        //Lire les métadonnées: nombre de modules, liste des modules, mot-clefs, niveau

        var_dump($tree);

        return array();
    }
}
