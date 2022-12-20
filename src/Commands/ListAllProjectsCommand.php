<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Wsl\CourseManager\Config;

class ListAllProjectsCommand extends Command
{

    protected static $defaultDescription = 'Show all your your courses managment systems registered on your machine.';

    public function execute(InputInterface $input, OutputInterface $output)
    {

        try {

            $main = Config::getCurrentProjectDefinedInGlobalConfiguration();
            $projects = Config::getAllProjectsRegisteredInGlobalConfiguration();
        } catch (\Exception $e) {
            $output->writeln([
                "You do not have any projects registered on your machine."
            ]);
            return COMMAND::FAILURE;
        }

        $output->writeln([
            "There is the list of all your courses managment systems registered on your machine:"
        ]);

        $projectsToString = array_map(function ($project) use ($main) {
            return $project === $main ? sprintf("%s[MAIN]", $project) : $project;
        }, $projects);

        foreach ($projectsToString as $project) {
            $output->writeln([
                sprintf(
                    "%s",
                    $project
                ),
            ]);
        }
        return COMMAND::SUCCESS;
    }


    public function configure(): void
    {
    }
}
