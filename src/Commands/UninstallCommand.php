<?php

namespace Wsl\CourseManager\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe en charge de désinstaller le programme 
 * (suppression des fichiers de config, suppression du code source, 
 * retirer le code source du PATH, suppression des cours)
 */
class UninstallCommand extends Command
{
    protected static $defaultDescription = 'Uninstall course-manager from your machine (Irreversible !)';


    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Demander la confirmation à l'utilisateur
        //Ajouter une option pour forcer également la suppression du projet de cours principal
        return COMMAND::FAILURE;
    }


    public function configure(): void
    {
    }
}
