<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveProjectCommand extends Command
{

    protected static $defaultDescription = 'Remove an existing courses managment system. Be careful.';


    public function execute(InputInterface $input, OutputInterface $output)
    {

        $rootDir = $input->getArgument('root_dir');

        //Demander la confirmation.
        $helper = $this->getHelper('question');

        $questionText = sprintf("You are about to remove the project (including all its courses) %s. 
        Are you sure you want to continue ?", $rootDir);

        $question = new ConfirmationQuestion($questionText, false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        //Supprimer le dossier root du projet.

        //Supprimer le projet du fichier de configuration global.

        return COMMAND::SUCCESS;
    }


    public function configure(): void
    {
        $this
            ->addArgument('root_dir', InputArgument::REQUIRED, 'The name of the root course you want to remove.');

        //Ajouter une option --force qui ne demande pas la confirmation
    }
}
