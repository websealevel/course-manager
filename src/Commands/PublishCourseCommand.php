<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Wsl\CourseManager\Config;

class PublishCourseCommand extends Command
{

    protected static $defaultDescription = 'Publish (to pdf, html, etc.) a course to the public directory of the current project';

    public function execute(InputInterface $input, OutputInterface $output)
    {
    }


    public function configure(): void
    {
    }
}
