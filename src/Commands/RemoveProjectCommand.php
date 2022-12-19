<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Wsl\CourseManager\Config;
use Wsl\CourseManager\Services\FileManager;

class RemoveProjectCommand extends Command
{

    protected static $defaultDescription = 'Remove an existing courses managment system. Be careful.';


    public function execute(InputInterface $input, OutputInterface $output)
    {

        $projectToDelete = $input->getArgument('root_dir');
        $projects = Config::getAllProjectsRegisteredInGlobalConfiguration();

        //Offrir le choix parmi les projets enregistrés.
        if (!isset($projectToDelete)) {
            //Proposer les choix sous forme de liste
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion("Which project do you want to remove permanently ?", $projects, 0);

            $question->setErrorMessage("Choice %s is invalid");
            $choice = $helper->ask($input, $output, $question);
            $output->writeln("You have selected: " . $choice);

            $projectToDelete = $choice;
        }

        //Demander la confirmation.
        $helper = $this->getHelper('question');

        $questionText = sprintf(
            "You are about to remove the selected project %s. Are you sure you want to continue (y/n) ?",
            $projectToDelete
        );

        $question = new ConfirmationQuestion($questionText, false);

        $continue = $helper->ask($input, $output, $question);

        if (!$continue) {
            $output->writeln("Removing project has been aborted. Nothing happened.");
            return COMMAND::SUCCESS;
        }

        //Supprimer le dossier root du projet.

        $removeDir = FileManager::removeDir($projectToDelete);

        //Supprimer le projet du fichier de configuration global.

        $removeFromGlobalConfiguration = Config::removeFromConfigFile($projectToDelete);

        $output->writeln("The project has been removed with success !");

        return COMMAND::SUCCESS;
    }


    public function configure(): void
    {
        $this
            ->addArgument('root_dir', InputArgument::OPTIONAL, 'The name of the root course you want to remove.');

        //Ajouter une option --force qui ne demande pas la confirmation
    }
}
