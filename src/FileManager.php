<?php

/**
 * Classe qui gère la création de fichiers et de dossiers
 *
 * @package wsl
 */

namespace Wsl\CourseManager;

use Wsl\CourseManager\Console;

class FileManager
{

    /**
     * Crée le fichier $abspath et écrit le contenu dedans.
     * @param string $abspath Le path absolu du fichier à créée
     * @param string $content . Optionnel. Le contenu du fichier à écrire.
     * @return bool
     */
    public static function createFile(string $abspath, string $content = ''): bool
    {
        $file = fopen(
            $abspath,
            'w'
        );
        fwrite($file, $content);
        return fclose($file);
    }

    /**
     * Créer le dossier $path s'il n'existe pas déjà. Retourne vrai si la création du dossier
     * a réussi, faux sinon
     * @param string $path. Le path du dossier à écrire (relatif à abspath)
     * @throws Exception -- Si impossible de créer le dossier à cause des droits d'écriture.
     * @return bool
     */
    public static function createDirectory(string $abspath)
    {
        if (is_dir($abspath)) {
            Console::print(sprintf("Le répertoire %s existe déjà. Skip.", $abspath));
            return false;
        }

        $created = mkdir($abspath, recursive: true);

        if (!$created) {
            throw new \Exception(
                sprintf("Impossible de créer le dossier %s. Véfifier les droits en écriture sur le path. ", $path)
            );
        }

        return $created;
    }
}
