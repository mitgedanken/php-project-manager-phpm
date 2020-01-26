<?php declare(strict_types=1);

namespace PHPM\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends Command
{
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('Add a package into your project with depended packages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }

}
