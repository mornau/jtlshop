<?php

declare(strict_types=1);

namespace JTL\Console\Command\DataObjects;

use Exception;
use JTL\Console\Command\Command;
use JTL\Helpers\Typifier;
use League\Flysystem\FilesystemException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCommand
 * @package JTL\Console\Command\Model
 */
class CreateDTO extends Command
{
    protected ?CreateDTOService $service = null;

    protected function getService(): CreateDTOService
    {
        if (empty($this->service)) {
            $this->service = new CreateDTOService(
                new CreateDTORepository()
            );
        }

        return $this->service;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('dto:create')
            ->setDescription('Create a new DTO for given table    ')
            ->addArgument('table', InputArgument::REQUIRED, 'Name of the table for that DTO')
            ->addArgument('target-dir', InputArgument::OPTIONAL, 'Shop installation dir', \PFAD_ROOT);
    }

    /**
     * @inheritDoc
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $tableName = \trim(Typifier::stringify($input->getArgument('table'), ''));
        $targetDir = \trim(Typifier::stringify($input->getArgument('target-dir'), ''));
        while ($tableName === null || \strlen($tableName) < 3) {
            $tableName = $this->getIO()->ask('Name of the table for that DTO');
        }
        $input->setArgument('table', $tableName);
        if (\strlen($targetDir) < 2) {
            $targetDir = $this->getIO()->ask('target-dir');
            $input->setArgument('target-dir', $targetDir);
        }
    }

    /**
     * @inheritDoc
     * @throws FilesystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getIO();
        try {
            $targetDir = $input->getArgument('target-dir') === \PFAD_ROOT
                ? \PFAD_ROOT . 'models/'
                : \PFAD_ROOT . $input->getArgument('target-dir');
            $tableName = Typifier::stringify($input->getArgument('table'));
            $modelName = $this->getService()->execute($input, $tableName, $targetDir);
        } catch (Exception $e) {
            $io->error('Error: ' . $e->getMessage());

            return Command::FAILURE;
        }
        $io->writeln(
            \sprintf(
                '<info>Created DataTableObject:</info> <comment>%s</comment> in directory %s%s',
                $modelName,
                $targetDir,
                $modelName
            )
        );

        return Command::SUCCESS;
    }
}
