<?php


namespace Wsl\CourseManager;

use Wsl\CourseManager\Services\FileManager;
use Wsl\CourseManager\Models\Project;

use Exception;

/**
 * Classe en charge de gérer la configuration d'un projet
 */
class Config
{

    public const COURSE_MANAGER_VERSION = "1.0";

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
     * @param string[] $variables
     */
    public function __construct(public readonly array $variables)
    {
    }

    /**
     * Les clés (clés/valeurs) obligatoires dans le fichier de configuration
     * local à un projet.
     * @var string[]
     */
    public const MANDATORY_LOCAL_CONFIG_KEYS = array(
        'project' => array(
            'type', 'version', 'editor'
        )
    );

    /**
     * Retourne un tableau contenant les variables de configuration local d'un projet.
     * @throws Exception - Si le fichier de configuration local n'existe pas.
     * @throws Exception - Si le fichier de configuration local ne contient pas les clefs/valeurs obligatoires.
     * @return string[]
     */
    public static function readLocalConfigFile(): array
    {

        if (!FileManager::fileExists(Config::LOCAL_CONFIG_FILE)) {
            throw new \Exception(
                sprintf("Fichier de configuration %s manquant à la racine du projet.", Config::LOCAL_CONFIG_FILE)
            );
        }

        $variables = FileManager::parseIniFile(Config::LOCAL_CONFIG_FILE, processSections: true);

        if (false === $variables) {
            throw new \Exception("Impossible de lire le fichier de configuration. Fichier mal formatté.");
        }

        //Les cléfs/valeurs obligatoires du fichier de configuration

        $diff = array_diff(Config::MANDATORY_LOCAL_CONFIG_KEYS, array_keys($variables));

        if (!empty($diff)) {
            throw new \Exception(sprintf("Le projet est mal configuré: %s", implode(",", $diff)));
        }

        return $variables;
    }


    /**
     * Le contenu du fichier INI de configuration local du projet par défaut
     * @return string
     */
    public static function configIniContent(): string
    {

        $version = Config::COURSE_MANAGER_VERSION;

        $INI = <<< INI
        [project]
        type=course-manager
        version={$version}
        editor=code
        INI;

        return $INI;
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
            throw new \Exception(
                sprintf(
                    "Impossible de créer le fichier de configuration global %s.",
                    $absPathGlobalConfig
                )
            );

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
            static::createGlobalConfigFile();
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
        $values = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

        if (!is_array($values))
            return false;

        //Tous les projets enregistrés
        $projects = explode(',', $values['PROJECTS']);

        //Tous les autres projets
        $otherProjects = array_filter($projects, function ($project) use ($absPathToRootDir) {
            return $project !== $absPathToRootDir;
        });

        $main = $values['MAIN'];

        if ($values['MAIN'] === $absPathToRootDir) {

            //Changer le curseur au projet précédent
            if (empty($otherProjects)) {
                //Si pas d'autres projets, on supprime le fichier de configuration globale
                FileManager::removeFile(static::absPathOfGlobalConfigFile());
                return true;
            }

            //Change le curseur de position sur un autre projet existant.
            $main = reset($otherProjects);
        }

        return static::overWriteGlobalConfigFile($main,  implode(",", $otherProjects));
    }


    /**
     * Action: ecrase le contenu du fichier de configuration global avec le nouveau contenu.
     * Retourne vrai si l'écriture à réussi, faux sinon
     * @param string $main Le nouveau projet courant
     * @param string $projects Les nouveaux projets enregistrés
     * @return bool 
     * @see FileManager::createFile
     */
    public static function overWriteGlobalConfigFile(string $main, string $projects): bool
    {
        $values = array(
            sprintf("MAIN=%s", $main),
            sprintf("PROJECTS=%s", $projects),
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

        if (!FileManager::fileExists(static::absPathOfGlobalConfigFile()))
            return false;

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
     * Retourne vrai si le dossier courant est un repertoire de gestion de cours, faux sinon
     * @return bool
     */
    public static function isThereALocalConfigurationFileInTheCurrentDirectory(): bool
    {
        $absPathToLocalConfigurationFile = sprintf("%s/%s", getcwd(), Config::LOCAL_CONFIG_FILE);

        $localConfigurationFileExists = FileManager::fileExists($absPathToLocalConfigurationFile);

        if (false === $localConfigurationFileExists)
            return false;

        //Rajouter une clef dans le fichier pour le distinguer d'autres fichiers config.ini qui pourraient s'y trouver.
        $content = FileManager::parseIniFile($absPathToLocalConfigurationFile, processSections: true);

        if (false === $content)
            return false;

        return isset($content['project']['type']) && 'course-manager' === $content['project']['type'];
    }


    /**
     * Retourne le projet courant défini dans la configuration globale (sous la clef MAIN)
     * @return string
     */
    public static function getCurrentProjectDefinedInGlobalConfiguration(): string
    {
        $values = static::getGlobalConfigurationValues();

        return $values['MAIN'];
    }


    /**
     * Retourne la liste des clefs/valeurs enregistrés dans le fichier de configuration
     * global au format INI
     * @return string[]
     * @throws Exception - Si le fichier de configuration n'existe pas, n'a pas une syntaxe correcte.
     */
    public static function getGlobalConfigurationValues(): array
    {
        if (!static::globalConfigFileIsInitialized()) {
            throw new \Exception("Le fichier de configuration global n'existe pas ou n'a pas une syntaxe correcte. Veuillez créer un nouveau projet pour initialiser le fichier de configuration global ou vérifier sa syntaxe.");
        }

        $values = FileManager::parseIniFile(static::absPathOfGlobalConfigFile());

        if (false === $values)
            throw new \Exception("Impossible de lire le fichier de configuration global. Veuillez vérifier sa syntaxe.");


        if (isset($values['MAIN']) && empty($values['MAIN']))
            throw new \Exception("Aucun projet courant défini dans le fichier de configuration global. Veuillez en définir un.");


        if (isset($values['PROJECTS']) && empty($values['PROJECTS'])) {
            //Si aucun projet n'est enregistré on supprime le fichier de configuration global.
            FileManager::removeFile(static::absPathOfGlobalConfigFile());
            throw new \Exception("Aucuns projets définis dans le fichier de configuration global. Veuillez en enregistrer.");
        }

        return $values;
    }


    /**
     * Retourne la liste de tous les projets enregistrés (sous la clef PROJECTS)
     * et qui existent sur le disque.
     * Effet de bord: nettoie les projets enregistrés qui n'existent plus sur le disque.
     * @return string[]
     */
    public static function getAllProjectsRegisteredInGlobalConfiguration(): array
    {
        $values = static::getGlobalConfigurationValues();

        $registeredProjects = explode(",", $values['PROJECTS']);

        $existingProjects = array_filter($registeredProjects, function ($project) {
            return is_dir($project);
        });

        //Nettoyage
        $projectsToUnregister = array_diff($registeredProjects, $existingProjects);

        foreach ($projectsToUnregister as $p) {
            Config::removeFromConfigFile($p);
        }

        return $existingProjects;
    }


    /**
     * Retourne le chemin absolu du repertoire du projet courant
     * @return string
     */
    public static function absPathToCurrentProject(): string
    {

        if (Config::isThereALocalConfigurationFileInTheCurrentDirectory())
            //On est dans un projet course-manager, donc le currentPath est le pathCourant
            return getcwd();

        //Renvoyer le dossier courant défini dans la configuration globale.
        return Config::getCurrentProjectDefinedInGlobalConfiguration();
    }

    /**
     * Retourne le projet courant
     * @return Project
     */
    public static function loadCurrentProject(): Project
    {
        $path = static::absPathToCurrentProject();
        return new Project($path);
    }
}
