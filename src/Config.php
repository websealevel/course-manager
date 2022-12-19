<?php


namespace Wsl\CourseManager;


class Config
{


    public const PATH_ENV_FILE = __DIR__ . '/../conf.ini';

    /**
     * Factory qui retourne un environnement avec validation préalable
     * @return Env
     */
    public static function create(): Config
    {

        try {
            if (php_sapi_name() !== 'cli') {
                throw new \Exception("php doit être executé en mode CLI.");
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

        if (!file_exists(Config::PATH_ENV_FILE)) {
            throw new \Exception("Fichier de configuration conf.ini manquant à la racine du projet.");
        }

        $variables = parse_ini_file(Config::PATH_ENV_FILE);

        if (false === $variables) {
            throw new \Exception("Impossible de lire le fichier de configuration. Fichier mal formatté.");
        }

        //Les cléfs/valeurs obligatoires du fichier de configuration
        $mandatory_options = array('path_courses');

        $diff = array_diff($mandatory_options, array_keys($variables));

        if (!empty($diff)) {
            throw new \Exception(sprintf("Variables d'environnement non initialisées: %s", implode(",", $diff)));
        }

        return $variables;
    }
}
