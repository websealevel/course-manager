<?php

namespace Wsl\CourseManager\Services;

use Wsl\CourseManager\Services\FileManager;
use Wsl\CourseManager\Models\Course;

/**
 * Service en charge d'inspecter le projet et de remonter les données des fichiers
 * Interface au système de fichier (a FileManager)
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
        $coursesPath  = FileManager::listDirsUnderPath($path . '/sources');

        // //Issue: comment detecter le vendor s'il y'en a et le differencier d'un cours ?

        // //Créer des instances de cours (et chargement des modules, métadonnées: nombre de modules, liste des modules, mot-clefs, niveau)
        // $courses = array_map(function ($path) {

        //     return new Course(
        //         basename($path),
        //         $path,
        //         static::vendor($path),
        //         '',
        //         ''
        //     );
        // }, $coursesPath);

        // var_dump($courses);

        return array();
    }


    /**
     * Retourne vrai si un vendor est détécté pour le cours, faux sinon
     * @param $pathCourse Le chemin absolu du cours
     * @return bool
     */
    public static function courseHasVendor(string $pathCourse): bool
    {
        return basename(dirname($pathCourse, 1)) !== 'sources';
    }

    /**
     * Retourne le vendor du cours s'il existe, une chaine vide sinon
     * @param $pathCourse Le chemin absolu du cours
     * @return string
     */
    public static function vendor(string $pathCourse): string
    {
        if (static::courseHasVendor($pathCourse)) {
            return basename(dirname($pathCourse, 1));
        } else {
            return '';
        }
    }
}
