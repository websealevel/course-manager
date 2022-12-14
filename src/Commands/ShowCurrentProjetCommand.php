<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Wsl\CourseManager\Config;

class ShowCurrentProjetCommand extends Command
{

    protected static $defaultDescription = 'Show the current projet you are working on.';


    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Test si le repertoire courant est un projet de course-manager
        if (Config::isThereALocalConfigurationFileInTheCurrentDirectory()) {
            //Recuperer et afficher le nom du repertoire
            $dir = getcwd();
            $output->writeln([
                "*The current directory is a course-manager project",
                sprintf(
                    "You are working on the follwing project: %s",
                    $dir
                ),
            ]);
            return COMMAND::SUCCESS;
        }

        //Sinon c'est celui défini dans le fichier de configuration global.
        try {
            $dir = Config::getCurrentProjectDefinedInGlobalConfiguration();
        } catch (\Exception $e) {
            $output->writeln([
                "No projects are currently available on your machine.",
                "You can create a project by using the command " . CreateProjectCommand::getDefaultName(),
            ]);
            return COMMAND::SUCCESS;
        }
        $output->writeln([
            sprintf("You are working on the follwing project: %s", $dir),
        ]);
        return COMMAND::SUCCESS;
    }


    public function configure(): void
    {
    }
}
