<?php


namespace Wsl\CourseManager\Services;


/**
 * Classe en charge de gérer le formattage des noms de fichiers (path relatifs à la racine
 * du projet courant) et de dossier dans les projets.
 * Ne travaille que sur des noms relatifs au repertoire du projet couant. 
 */
class Formatter
{
    /**
     * Retourne le path formatté (minuscules) d'un cours
     * @param string $courseName Le nom du cours
     * @param string $vendorName Optionnal. Le nom du vendor
     * @return string Le path formatté du cours
     */
    public static function pathOfCourse(string $courseName, string $vendorName = ''): string
    {
        return sprintf("%s/%s", strtolower($vendorName), strtolower($courseName));
    }
}
