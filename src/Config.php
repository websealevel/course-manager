<?php


namespace Wsl\CourseManager;


class Config
{


    public const PATH_ENV_FILE = __DIR__ . '/../conf.ini';

    /**
     * Les clés/valeurs obligatoires à déclarer dans le fichier de configuration conf.ini
     */
    public const MANDATORY_CONFIG_KEYS = array('path_courses');

    /**
     * Retourne les variables d'environnement avec validation préalable
     * @throws Exception - Si PHP n'est pas executé en mode (SAPI) 'cli'
     * @return Env
     */
    public static function create(): Config
    {

        //Validation préalable
        if (php_sapi_name() !== 'cli') {
            throw new \Exception("php doit être executé en mode CLI.");
        }

        $variables = static::readConfigFile();

        return new Config($variables);
    }

    /**
     * Retourne un tableau contenant les variables d'environnemnt
     * @throws Exception - Si le fichier conf.ini n'est pas trouvé
     * @throws Exception - Si le fichier conf.ini ne contient pas les variables obligatoires
     * @return array
     */
    public static function readConfigFile(): array
    {

        if (!file_exists(Config::PATH_ENV_FILE)) {
            throw new \Exception("Fichier de configuration conf.ini manquant à la racine du projet.");
        }

        $variables = parse_ini_file(Config::PATH_ENV_FILE);

        if (false === $variables) {
            throw new \Exception("Impossible de lire le fichier de configuration. Fichier mal formatté.");
        }

        //Les cléfs/valeurs obligatoires du fichier de configuration
        $mandatory_options = array('path_courses');

        $diff = array_diff(Config::MANDATORY_CONFIG_KEYS, array_keys($variables));

        if (!empty($diff)) {
            throw new \Exception(sprintf("Variables d'environnement non initialisées: %s", implode(",", $diff)));
        }

        return $variables;
    }
}
