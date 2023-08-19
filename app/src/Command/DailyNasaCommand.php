<?php

namespace App\Command;

use App\Client\NasaApiClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'daily-nasa',
    description: 'Download daily earth images from NASA.',
)]
class DailyNasaCommand extends Command
{
    protected const COMMAND_PARAM_DESTINATION = 'destination';
    protected const COMMAND_OPTION_DATE = 'date';
    protected const COMMAND_OPTION_DATE_FORMAT = 'Y-m-d';

    /**
     * @var \App\Client\NasaApiClientInterface
     */
    protected NasaApiClientInterface $apiClient;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected FileSystem $fileSystem;

    public function __construct(NasaApiClientInterface $api_client, FileSystem $file_system)
    {
        $this->apiClient = $api_client;
        $this->fileSystem = $file_system;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(static::COMMAND_PARAM_DESTINATION, InputArgument::REQUIRED, 'The path to the  download destination directory.');

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
        $dateString = $date->format(static::COMMAND_OPTION_DATE_FORMAT);
        $output->writeln(sprintf('Fetching images for %s...', 
            $dateString
        ));

        $imageDir = $input->getArgument(static::COMMAND_PARAM_DESTINATION) . DIRECTORY_SEPARATOR . $dateString . DIRECTORY_SEPARATOR;
        $output->writeln('Ensure directory ' . $imageDir . ' exists.');
        // $this->fileSystem->mkdir($imageDir);
        $responseContent = $this->apiClient->downloadDailyEarthImages($dateString, $imageDir, $output);

        // $imageDir = $input->getArgument(static::COMMAND_PARAM_DESTINATION . DIRECTORY_SEPARATOR . $dateString);
        // $this->fileSystem->dumpFile($imageDir . DIRECTORY_SEPARATOR . 'image.png', $responseContent);

        return Command::SUCCESS;
    }
}