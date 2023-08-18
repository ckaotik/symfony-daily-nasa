<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'daily-nasa')]
class DailyNasaCommand extends Command
{
    protected const COMMAND_OPTION_DATE = 'date';
    protected const COMMAND_OPTION_DATE_FORMAT = 'Y-m-d';

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('destination', InputArgument::REQUIRED, 'The download destination directory.');

        $this->addOption(static::COMMAND_OPTION_DATE, null, InputOption::VALUE_REQUIRED, 'Specify the date for which to fetch images, e.g. 2022-12-24.', date(static::COMMAND_OPTION_DATE_FORMAT));
    }

    /**
     * {@inheritDoc}
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * 
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello world!');

        $date = new \DateTime($input->getOption(static::COMMAND_OPTION_DATE));
        $output->writeln(sprintf('Fetching images for %s...', 
            $date->format(static::COMMAND_OPTION_DATE_FORMAT)
        ));

        return Command::SUCCESS;
    }
}