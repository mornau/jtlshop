<?php

declare(strict_types=1);

namespace JTL\Console\Command\Generator;

use JTL\Console\Command\Command;
use JTL\Installation\DemoDataInstaller;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateDemoDataCommand
 * @package JTL\Console\Command\Generator
 */
class GenerateDemoDataCommand extends Command
{
    /**
     * @var int
     */
    private int $manufacturers = 0;

    /**
     * @var int
     */
    private int $categories = 0;

    /**
     * @var int
     */
    private int $products = 0;

    /**
     * @var int
     */
    private int $customers = 0;

    /**
     * @var int
     */
    private int $links = 0;

    /**
     * @var int
     */
    private int $characteristics = 0;

    /**
     * @var int
     */
    private int $characteristicValues = 0;

    /**
     * @var ProgressBar|null
     */
    private ?ProgressBar $bar = null;

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('generate:demodata')
            ->setDescription('Generate demo data')
            ->addOption('manufacturers', 'm', InputOption::VALUE_OPTIONAL, 'Amount of manufacturers', 0)
            ->addOption('links', 'l', InputOption::VALUE_OPTIONAL, 'Amount of links', 0)
            ->addOption('categories', 'c', InputOption::VALUE_OPTIONAL, 'Amount of categories', 0)
            ->addOption('customers', 'u', InputOption::VALUE_OPTIONAL, 'Amount of customers', 0)
            ->addOption('characteristics', 'a', InputOption::VALUE_OPTIONAL, 'Amount of characteristics', 0)
            ->addOption('characteristicvalues', 'w', InputOption::VALUE_OPTIONAL, 'Amount of characteristic values', 0)
            ->addOption('products', 'p', InputOption::VALUE_OPTIONAL, 'Amount of products', 0);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->manufacturers        = (int)$this->getOption('manufacturers');
        $this->categories           = (int)$this->getOption('categories');
        $this->products             = (int)$this->getOption('products');
        $this->customers            = (int)$this->getOption('customers');
        $this->links                = (int)$this->getOption('links');
        $this->characteristics      = (int)$this->getOption('characteristics');
        $this->characteristicValues = (int)$this->getOption('characteristicvalues');

        $this->generate();

        return Command::SUCCESS;
    }

    /**
     * Generate the demo data.
     */
    private function generate(): void
    {
        $generator = new DemoDataInstaller(
            $this->getDB(),
            [
                'manufacturers'        => $this->manufacturers,
                'categories'           => $this->categories,
                'products'             => $this->products,
                'customers'            => $this->customers,
                'links'                => $this->links,
                'characteristics'      => $this->characteristics,
                'characteristicValues' => $this->characteristicValues,
            ]
        );
        ProgressBar::setFormatDefinition(
            'generator',
            '%message:s% %current%/%max% %bar% %percent:3s%% %elapsed:6s%/%estimated:-6s%'
        );

        if ($this->manufacturers > 0) {
            $this->barStart($this->manufacturers, 'manufacturer');
            $generator->createManufacturers($this->callBack(...));
            $this->barEnd();
        }
        if ($this->categories > 0) {
            $this->barStart($this->categories, 'categories');
            $generator->createCategories($this->callBack(...));
            $this->barEnd();
        }
        if ($this->products > 0) {
            $this->barStart($this->products, 'products');
            $generator->createProducts($this->callBack(...));
            $this->barEnd();
            $generator->updateRatingsAvg();
        }
        if ($this->customers > 0) {
            $this->barStart($this->customers, 'customers');
            $generator->createCustomers($this->callBack(...));
            $this->barEnd();
        }
        if ($this->links > 0) {
            $this->barStart($this->links, 'links');
            $generator->createLinks($this->callBack(...));
            $this->barEnd();
        }
        if ($this->characteristics > 0) {
            $this->barStart($this->characteristics, 'characteristics');
            $generator->createCharacteristics($this->callBack(...));
            $this->barEnd();
        }
        if ($this->characteristicValues > 0) {
            $this->barStart($this->characteristicValues, 'characteristicvalues');
            $generator->createCharacteristicValues($this->callBack(...));
            $this->barEnd();
        }

        $this->getIO()->writeln('Generated manufacturers: ' . $this->manufacturers)
            ->writeln('Generated categories: ' . $this->categories)
            ->writeln('Generated products: ' . $this->products)
            ->writeln('Generated characteristics: ' . $this->characteristics)
            ->writeln('Generated characteristic values: ' . $this->characteristicValues)
            ->writeln('Generated customers: ' . $this->customers)
            ->writeln('Generated links: ' . $this->links);
    }

    /**
     * @param int    $max
     * @param string $subject
     */
    private function barStart(int $max, string $subject): void
    {
        $this->bar = new ProgressBar($this->getIO(), $max);
        $this->bar->start();
        $this->bar->setFormat('generator');
        $this->bar->setMessage('Generate ' . $subject . ':');
    }

    private function barEnd(): void
    {
        $this->bar->finish();
        $this->getIO()->newLine();
        $this->getIO()->newLine();
    }

    public function callBack(): void
    {
        $this->bar->advance();
    }
}
