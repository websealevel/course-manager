<?php

namespace Wsl\CourseManager\Commands;


use Symfony\Component\Console\Command\Command;

/**
 * Classe en charge de désinstaller le programme 
 * (suppression des fichiers de config, suppression du code source, 
 * retirer le code source du PATH, suppression des cours)
 */
class UninstallCommand extends Command
{
    protected static $defaultDescription = 'Uninstall course-manager from your machine (Irreversible !)';

    //Demander la confirmation à l'utilisateur

    //Ajouter une option pour forcer également la suppression du projet de cours principal
}
