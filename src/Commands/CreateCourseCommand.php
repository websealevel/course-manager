<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateCourseCommand extends Command
{

    protected static $defaultDescription = 'Create a new course in the current project.';

    protected static $defaultName = 'add:course';

    public function execute(InputInterface $input, OutputInterface $output)
    {

        return COMMAND::SUCCESS;
    }

    public function configure(): void
    {
        $this
            ->addArgument('course_name', InputArgument::REQUIRED, 'The name of the course you want to add to the current project.');

        $this
            ->addArgument('vendor_name', InputArgument::OPTIONAL, 'The name of the vendor for which the course is attached.');

        $this
            ->addArgument('key_words', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Key-words describing the course content.');
    }
}
