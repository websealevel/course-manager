<?php

/**
 * Classe qui gère la création de fichiers et de dossiers
 *
 * @package wsl
 */

namespace Wsl\CourseManager;

class FileManager
{
    public static function createFile(string $abspath)
    {
    }

    public static function createDirectory(string $abspath)
    {
    }

     /**
     * Créer le dossier $path s'il n'existe pas déjà. Retourne vrai si la création du dossier
     * a réussi, faux sinon
     * @param string $path. Le path du dossier à écrire (relatif à abspath)
     * @throws Exception -- Si impossible de créer le dossier à cause des droits d'écriture.
     * @return bool
     */
    public function mkdirp(string $path): bool
    {

        $full_path = $this->fullPath($path);

        if (is_dir($full_path)) {
            $this->print(sprintf("Le répertoire %s existe déjà. Skip.", $path));
            return false;
        }

        $created = mkdir($full_path, recursive: true);

        if (!$created) {
            throw new Exception(
                sprintf("Impossible de créer le dossier %s. Véfifier les droits en écriture sur le path. ", $path)
            );
        }

        return $created;
    }
}
