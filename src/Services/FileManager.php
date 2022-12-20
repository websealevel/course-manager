<?php

/**
 * Classe qui gère la création de fichiers et de dossiers
 *
 * @package wsl
 */

namespace Wsl\CourseManager\Services;

use Exception;

class FileManager
{

    private const CODE_DIR_ALREADY_EXISTS = 203;

    /**
     * Retourne le path absolu du dossier home/user
     * @return string
     */
    public static function getHomeDirOfUser(): string
    {
        $home = getenv("HOME");
        return $home;
    }

    /**
     * Retourne le contenu parsé d'un fichier de configuration au format INI
     * @param string $absPathIniFile Le chemin absolu vers le fichier de configuration du projet 
     * @return string[]|false
     */
    public static function parseIniFile(string $absPathIniFile): array|false
    {
        $size = filesize($absPathIniFile);

        $result = parse_ini_file($absPathIniFile);

        //Un fichier vide n'est pas un fichier INI invalide.
        if (false === $result && 0 !== $size)
            throw new \Exception("Impossible de parser le fichier de configuration INI. Veuillez vérifier sa syntaxe.");
        return $result;
    }


    /**
     * Retourne vrai si le dossier existe, faux sinon
     * @param string $absPathToDir Le chemin absolu du dossier
     * @return bool
     */
    public static function dirExists(string $absPathToDir): bool
    {
        return is_dir($absPathToDir);
    }

    /**
     * Retourne vrai si le fichier existe, faux sinon
     * @param string $absPathToFile Le path absolu du fichier
     * @return bool
     */
    public static function fileExists(string $absPathToFile): bool
    {
        return file_exists($absPathToFile);
    }

    /**
     * Action: Crée le fichier $abspath et écrit le contenu dedans. Retourne vrai en cas de réussite d'écriture, faux sinon.
     * @param string $abspath Le path absolu du fichier à créée
     * @param string|string[] $content Optionnel. Le contenu du fichier à écrire.
     * @return bool
     * @throws Exception Si impossible d'ouvrir le fichier en écriture, impossible d'écrire dans le fichier.
     */
    public static function createFile(string $abspath, string|array $content = ''): bool
    {
        $file = fopen(
            $abspath,
            'w'
        );

        if (!$file) {
            throw new \Exception(sprintf("Impossible d'ouvrir le fichier %s", $abspath));
        }

        if (false === fwrite($file, $content)) {
            throw new \Exception(sprintf("Impossible d'écrire dans le fichier %s", $abspath));
        }

        return fclose($file);
    }


    /**
     * Action: supprime le fichier sur le path $abspath. Retourne vrai si la suppression
     * a eu lieu, faux sinon.
     * @param $abspath Le path du fichier à supprimer
     * @return bool
     */
    public static function removeFile(string $abspath): bool
    {
        return unlink($abspath);
    }

    /**
     * Action: supprime le dossier sur le path $abspath. Retourne vrai si la suppression
     * a eu lieu, faux sinon.
     * @param $abspath Le path du fichier à supprimer
     * @return bool
     */
    public static function removeDir(string $abspath): bool
    {
        //Check que le PATH est sur le home (on ne veut pas toucher à des fichiers systeme)
        if (!str_starts_with($abspath, '/home/') || str_contains($abspath, '.'))
            throw new \Exception("Impossible de supprimer un fichier qui n'appartient pas à l'utilisateur courant.");

        return FileManager::recursiveRmDir($abspath);
    }

    /**
     * Action: supprime le dossier et tout son contenu de manière récursive
     * @param string $absPath Le path du dossier a supprimé (ainsi que son contenu)
     */
    public static function recursiveRmDir($absPath): bool
    {
        $files = glob($absPath . '/{,.metadata}*', GLOB_BRACE);

        foreach ($files as $file) {
            if (is_dir($file))
                FileManager::recursiveRmDir($file);
            else
                unlink($file);
        }

        if (is_dir($absPath)) rmdir($absPath);

        return true;
    }

    /**
     * Action: crée le repertoire racine du projet de gestion de cours s'il n'existe
     * pas déjà. Retourne la chemin absolu du repertoire du projet
     * @return string Le chemin absolu vers le dossier ROOT du projet
     * @throws Exception @see createDirectory()
     */
    public static function createRootDirectory(string $rootDir): string
    {

        //Récuperer le répertoire courant
        $absPathRootDir = sprintf("%s/%s", getcwd(), $rootDir);

        try {

            static::createDirectory($rootDir);
        } catch (\Exception $e) {
            //Si le dossier existe déjà, on continue.
            if (FileManager::CODE_DIR_ALREADY_EXISTS === $e->getCode())
                return $absPathRootDir;

            //Sinon on fait passer l'exception
            throw new \Exception($e->getMessage());
        }

        return $absPathRootDir;
    }

    /**
     * Action: crée le dossier $path s'il n'existe pas déjà. Retourne vrai si la création du dossier
     * a réussi, faux sinon.
     * @param string $path. Le path du dossier à écrire
     * @throws Exception Si impossible de créer le dossier à cause des droits d'écriture, si le dossier existe déjà.
     * @return bool
     */
    public static function createDirectory(string $path, string $label = 'projet'): bool
    {

        if (is_dir($path)) {
            throw new \Exception(
                sprintf(
                    "Le %s <%s> existe déjà sur la machine.",
                    $label,
                    $path,
                ),
                FileManager::CODE_DIR_ALREADY_EXISTS
            );
        }

        $created = mkdir($path, recursive: true);

        if (!$created) {
            throw new \Exception(
                sprintf(
                    "Impossible de créer le dossier <%s>. Véfifier les droits en écriture sur le path. ",
                    $path
                )
            );
        }

        return $created;
    }

    /**
     * Action: crée un ensemble de dossiers
     * @param string[] $dirs Des paths de dossiers à créer
     * @return bool Retourne vrai si tous les dossiers ont été crées avec succès
     * @throws Exception - @see createDirectory
     */
    public static function createDirectories(array $dirs): bool
    {
        foreach ($dirs as $dir) {
            try {
                static::createDirectory($dir);
            } catch (\Exception $e) {
                //Si le sous-dossier existe déjà, on ignore.
                if ($e->getCode() === FileManager::CODE_DIR_ALREADY_EXISTS) {
                    continue;
                }
            }
        }
        return true;
    }

    /**
     * Action: crée un ensemble de fichiers dans le dossier du projet.
     * @param string $absPathOfRootDir Le chemin absolu du projet
     * @param File[] $files Des fichiers à créer
     * @return bool Retourne vrai si tous les fichiers ont été crées avec succès
     * @throws Exception - @see createFile
     */
    public static function createFiles(string $absPathOfRootDir, array $files): bool
    {
        foreach ($files as $file) {
            $path = sprintf("%s/%s", $absPathOfRootDir, $file->name);
            static::createFile($path, $file->content);
        }

        return true;
    }
}
