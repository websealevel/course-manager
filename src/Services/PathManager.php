<?php

namespace Wsl\CourseManager\Services;


use Exception;

/**
 * Cette classe n'a que la responsabilité de lire le path de la racine
 * du projet et de retourner les chemins absolus ou relatifs. Elle devrait s'appeler path.
 * Elle lit également le fichier de config et test si le sapi de php est bien CLI.
 */

class PathManager
{
    private const ABS_PATH_KEY = 'path_courses';

    private function __construct(
        readonly public array $variables
    ) {
    }





    /**
     * Retourne le path absolu de la racine du projet (avec trailing slash)
     * @throws Exception - Si la variable ABS_PATH_KEY n'existe pas dans les variables d'environnement
     * @return string
     */
    public function abspath(): string
    {
        if (!isset($this->variables[self::ABS_PATH_KEY])) {
            throw new Exception("La variable d'environnement " . self::ABS_PATH_KEY . " n'est pas définie.");
        }
        return $this->variables[self::ABS_PATH_KEY] . '/';
    }

    /**
     * Retourne le path absolu de $relativePath
     * @param string $relativePath Le chemin relatif à la racine du projet
     * @param string $trailing Caractère à ajouter à la fin (trailing slash)
     * @return string
     */
    public function fullPath(string $relativePath, string $trailing = ''): string
    {
        return $this->abspath() . $relativePath . $trailing;
    }
}
