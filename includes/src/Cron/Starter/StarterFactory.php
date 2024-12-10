<?php

declare(strict_types=1);

namespace JTL\Cron\Starter;

use JTL\Shop;

/**
 * Class StarterFactory
 * @package JTL\Cron\Starter
 */
class StarterFactory
{
    /**
     * @var StarterInterface|null
     */
    private ?StarterInterface $starter = null;

    /**
     * StarterFactory constructor.
     * @param array<string, string> $config
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @return StarterInterface
     */
    public function getStarter(): StarterInterface
    {
        switch ($this->config['cron_type']) {
            case 's2s':
                $this->starter = new Curl();
                $this->starter->setFrequency((int)$this->config['cron_freq']);
                $this->starter->setURL(Shop::getURL() . '/' . \PFAD_INCLUDES . 'cron_inc.php');
                break;
            case 'N':
            default:
                $this->starter = new DummyStarter();
                break;
        }

        return $this->starter;
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        return $this->starter?->start() ?? false;
    }
}
