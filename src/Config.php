<?php


namespace Wsl\CourseManager;

use Wsl\CourseManager\Services\FileManager;

/**
 * Classe en charge de gérer la configuration d'un projet
 */
class Config
{

    /**
     * Le nom du fichier de configuration local à un projet
     * @var string
     */
    public const CONFIG_FILE = 'conf.ini';

    /**
     * Les clés (clés/valeurs) obligatoires dans le fichier de configuration
     * local à un projet.
     * @var string[]
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
            throw new \Exception("PHP must be executed in the SAPI mode 'cli'.");
        }

        $variables = static::readLocalConfigFile();

        return new Config($variables);
    }

    /**
     * Retourne un tableau contenant les variables d'environnemnt
     * @throws Exception - Si le fichier de configuration local n'existe pas.
     * @throws Exception - Si le fichier de configuration local ne contient pas les clefs/valeurs obligatoires.
     * @return array
     */
    public static function readLocalConfigFile(): array
    {

        if (!FileManager::fileExists(Config::CONFIG_FILE)) {
            throw new \Exception(
                sprintf("Fichier de configuration %s manquant à la racine du projet.", Config::CONFIG_FILE)
            );
        }

        $variables = FileManager::parseIniFile(Config::CONFIG_FILE);

        if (false === $variables) {
            throw new \Exception("Impossible de lire le fichier de configuration. Fichier mal formatté.");
        }

        //Les cléfs/valeurs obligatoires du fichier de configuration

        $diff = array_diff(Config::MANDATORY_CONFIG_KEYS, array_keys($variables));

        if (!empty($diff)) {
            throw new \Exception(sprintf("Variables d'environnement non initialisées: %s", implode(",", $diff)));
        }

        return $variables;
    }

    /**
     * Retourne la liste des noms de dossier crées par défaut 
     * à la racine du projet à l'initalisation d'un nouveau projet 
     * @return array
     */
    public static function projectDefaultDirectories(): array
    {
        return array(
            'sources', 'templates', 'public'
        );
    }

    /**
     * Retourne la liste des fichiers crées par défaut 
     * à la racine du projet à l'initalisation d'un nouveau projet 
     * @return array
     */
    public static function projectDefaultFiles(): array
    {
        return array(
            'index.html', 'config.ini'
        );
    }
}
