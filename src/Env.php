<?php

namespace Wsl\CourseManager;

use Exception;

/**
 * Remarque: Une classe a écarteler. Cette classe n'a que la responsabilité de lire le path de la racine
 * du projet et de retourner les chemins absolus ou relatifs. Elle devrait s'appeler path.
 * Elle lit également le fichier de config et test si le sapi de php est bien CLI.
 */

class Env
{
    private const ABS_PATH_KEY = 'path_courses';

    private function __construct(
        readonly public array $variables
    ) {
    }

    /**
     * Factory qui retourne un environnement avec validation préalable
     * @return Env
     */
    public static function create(): Env
    {

        try {
            if (php_sapi_name() !== 'cli') {
                throw new Exception("php doit être executé en mode CLI.");
            }

            $variables = static::readConfigFile();

            return new Env($variables);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit;
        }
    }

    /**
     * Retourne un tableau contenant les variables d'environnemnt
     * @throws Exception - Si le fichier conf.ini n'est pas trouvé
     * @throws Exception - Si le fichier conf.ini ne contient pas les variables obligatoires
     * @return array
     */
    public static function readConfigFile(): array
    {
        $conf_file = __DIR__ . '/../conf.ini';

        if (!file_exists($conf_file)) {
            throw new Exception("Fichier de configuration conf.ini manquant à la racine du projet.");
        }

        $env_variables = parse_ini_file($conf_file);

        if (false === $env_variables) {
            throw new Exception("Impossible de lire le fichier de configuration. Fichier mal formatté.");
        }

        $mandatory_options = array('path_courses');

        $diff = array_diff($mandatory_options, array_keys($env_variables));

        if (!empty($diff)) {
            throw new Exception(sprintf("Variables d'environnement non initialisées: %s", implode(",", $diff)));
        }

        return $env_variables;
    }

    /**
     * Retourne le path absolu de la racine du projet (avec trailing slash)
     * @throws Exception - Si la variable ABS_PATH_KEY n'existe pas dans les variables d'environnement
     * @return string
     */
    public function abspath(): string
    {
        if (!isset($this->envVariables[self::ABS_PATH_KEY])) {
            throw new Exception("La variable d'environnement " . self::ABS_PATH_KEY . "n'est pas définie.");
        }
        return $this->envVariables[self::ABS_PATH_KEY] . '/';
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
