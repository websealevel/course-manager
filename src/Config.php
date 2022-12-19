<?php


namespace Wsl\CourseManager;

use Wsl\CourseManager\Services\FileManager;

/**
 * Classe en charge de gérer la configuration d'un projet
 */
class Config
{

    /**
     * Nom du fichier de configuration global du programme. Maintient la liste des tous les projets de gestion de cours,
     * et un pointeur MAIN sur le projet courant.
     * @var string
     */
    public const GLOBAL_CONFIG_FILE = '.course-manager';

    /**
     * Le nom du fichier de configuration local à un projet
     * @var string
     */
    public const LOCAL_CONFIG_FILE = 'config.ini';

    public const DEFAULT_ROOT_DIR = 'courses';

    /**
     * Les clés (clés/valeurs) obligatoires dans le fichier de configuration
     * local à un projet.
     * @var string[]
     */
    public const MANDATORY_CONFIG_KEYS = array('path_courses');

    /**
     * Retourne les variables d'environnement avec validation préalable
     * @throws Exception - Si PHP n'est pas executé en mode (SAPI) 'cli'
     * @return Config
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

        if (!FileManager::fileExists(Config::LOCAL_CONFIG_FILE)) {
            throw new \Exception(
                sprintf("Fichier de configuration %s manquant à la racine du projet.", Config::LOCAL_CONFIG_FILE)
            );
        }

        $variables = FileManager::parseIniFile(Config::LOCAL_CONFIG_FILE);

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

    /**
     * Action: Crée un fichier de configuration global dans le répertoire home de l'utilisateur courant
     * s'il n'existe pas déjà. Retourne vrai si le fichier a été crée, faux s'il existe déjà.
     * @throws Exception - Si impossible de créer le fichier de configuration alors qu'il n'existe pas.
     */
    public static function createGlobalConfigFile(): bool
    {
        if (static::globalConfigExists()) {
            return false;
        }

        $pathHome = FileManager::getHomeDirOfUser();

        $absPathGlobalConfig = sprintf("%s/%s", $pathHome, static::GLOBAL_CONFIG_FILE);

        //Sinon, on crée le fichier de config global, et on met le pointeur MAIN sur le projet courant
        $success = FileManager::createFile(
            $absPathGlobalConfig
        );

        if (!$success)
            throw new \Exception("Impossible de créer le fichier de configuration global %s.", $absPathGlobalConfig);

        return true;
    }


    /**
     * Retourne vrai si le fichier de configuration global existe, faux sinon.
     * @return bool
     */
    public static function globalConfigExists(): bool
    {
        return FileManager::fileExists(static::absPathOfGlobalConfigFile());
    }

    /**
     * Retourne le chemin absolu du fichier de configuration global
     * @return string
     */
    public static function absPathOfGlobalConfigFile(): string
    {
        $pathHome = FileManager::getHomeDirOfUser();
        return sprintf("%s/%s", $pathHome, static::GLOBAL_CONFIG_FILE);
    }


    /**
     * Enregistre un nouveau projet dans le fichier de configuration global
     * @param string $absPathToRootDir Le path absolu du nouveau projet (unique)
     */
    public static function registerProject(string $absPathToRootDir): void
    {

        if (!static::globalConfigExists()) {
            static::createGlobalConfigFile($absPathToRootDir);
        }

        $values = array();

        if (!static::globalConfigFileIsInitialized()) {

            $values = array(
                sprintf("MAIN=%s", $absPathToRootDir),
                sprintf("PROJECTS=%s", $absPathToRootDir),
            );
        } else {

            $parsed = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

            //Projet non encore enregistré
            if (isset($parsed['PROJECTS']) && !str_contains($parsed['PROJECTS'], $absPathToRootDir)) {
                $parsed['PROJECTS'] = $parsed['PROJECTS'] . "," . $absPathToRootDir;
            }

            //Transform
            $values = array(
                sprintf("MAIN=%s", $parsed['MAIN']),
                sprintf("PROJECTS=%s", $parsed['PROJECTS']),
            );
        }

        if (false !== $values)
            FileManager::createFile(static::absPathOfGlobalConfigFile(), implode("\n", $values));

        return;
    }

    /**
     * Action: supprime toutes les entrées du projet du fichier de configuration global
     * s'il existe. Si le projet a retiré est le projet MAIN, le projet précédent est déclaré comme MAIN
     * à la place. Si aucun autre projet enregistré n'existe, le fichier de configuration global est supprimé.
     */
    public static function removeFromConfigFile(string $absPathToRootDir): bool
    {
        //Lire le fichier de config
        $parsed = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

        if (!is_array($parsed))
            return false;

        //Tous les projets enregistrés
        $projects = explode(',', $parsed['PROJECTS']);

        //Tous les autres projets
        $otherProjects = array_filter($projects, function ($project) use ($absPathToRootDir) {
            return $project !== $absPathToRootDir;
        });

        //On retire le projet à supprimer des projets enregistrés.
        $parsed['PROJECTS'] = implode(",", $otherProjects);

        if ($parsed['MAIN'] === $absPathToRootDir) {

            //Changer le curseur au projet précédent
            if (empty($otherProjects)) {
                //Si pas d'autres projets, on supprime le fichier de configuration globale
                FileManager::removeFile(static::absPathOfGlobalConfigFile());
                return true;
            }

            //Change le curseur de position sur un autre projet existant.
            $parsed['MAIN'] = $otherProjects[0];
        }

        //Transform
        $values = array(
            sprintf("MAIN=%s", $parsed['MAIN']),
            sprintf("PROJECTS=%s", $parsed['PROJECTS']),
        );

        return FileManager::createFile(static::absPathOfGlobalConfigFile(), implode("\n", $values));
    }

    /**
     * Retourne vrai si le fichier de configuration Global est initialisé (présence
     * des clefs MAIN et PROJECTS), faux sinon
     * @return bool
     */
    public static function globalConfigFileIsInitialized(): bool
    {
        $values = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

        if (false === $values)
            return false;

        return
            in_array(
                'MAIN',
                array_keys($values)
            ) &&
            in_array(
                'PROJECTS',
                array_keys($values)
            );
    }


    /**
     * Retourne vrai si le dossier courant est un repertoire de gestion de cours
     * manipuable par le programme, faux sinon
     * @return bool
     */
    public static function isThereALocalConfigurationFileInTheCurrentDirectory(): bool
    {
        $absPathToLocalConfigurationFile = sprintf("%s/%s", getcwd(), Config::LOCAL_CONFIG_FILE);

        $localConfigurationFile = FileManager::fileExists($absPathToLocalConfigurationFile);

        //Rajouter une clef dans le fichier pour le distinguer d'autres fichiers config.ini qui pourraient s'y trouver.

        return $localConfigurationFile;
    }


    /**
     * Retourne le projet courant défini dans la configuration globale (sous la clef MAIN)
     * @return string
     */
    public static function getCurrentProjectDefinedInGlobalConfiguration(): string
    {

        if (!static::globalConfigFileIsInitialized()) {
            throw new \Exception("Le fichier de configuration global n'a pas une syntaxe correcte. 
            Impossible de l'analyser. Veuillez vérifier sa syntaxe.");
        }

        $values = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

        return $values['MAIN'];
    }


    /**
     * Retourne la liste de tous les projets enregistrés (sous la clef PROJECTS)
     * @return string[]
     */
    public static function getAllProjectsRegisteredInGlobalConfiguration(): array
    {
        if (!static::globalConfigFileIsInitialized()) {
            throw new \Exception("Le fichier de configuration global n'a pas une syntaxe correcte. 
            Impossible de l'analyser. Veuillez vérifier sa syntaxe.");
        }

        $values = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

        //Il faudrait checker que les dossiers existent toujours, sinon supprimer(clean) les projets enregistrés.

        return explode(",", $values['PROJECTS']);
    }
}
