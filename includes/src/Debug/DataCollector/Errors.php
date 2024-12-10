<?php

declare(strict_types=1);

namespace JTL\Debug\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use stdClass;

/**
 * Class Errors
 * @package JTL\Debug\DataCollector
 */
class Errors extends DataCollector implements Renderable
{
    /**
     * @var stdClass[]
     */
    private array $errors = [];

    /**
     * Errors constructor.
     */
    public function __construct()
    {
        \set_error_handler($this->handleError(...));
    }

    /**
     * @param int    $errno
     * @param string $errstr
     * @param string $file
     * @param int    $line
     * @param array  $context
     */
    public function handleError(int $errno, string $errstr, string $file = '', int $line = 0, array $context = []): void
    {
        $error          = new stdClass();
        $error->level   = $errno;
        $error->message = $errstr;
        $error->file    = $file;
        $error->line    = $line;
        $error->context = $context;
        $this->errors[] = $error;
    }

    /**
     * @inheritdoc
     */
    public function collect(): array
    {
        $data     = [];
        $fomatter = $this->getDataFormatter();
        foreach ($this->errors as $var) {
            $data[\basename($var->file) . ':' . $var->line] = $fomatter->formatVar($var);
        }

        return ['errors' => $data, 'count' => \count($data)];
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'errors';
    }

    /**
     * @inheritdoc
     */
    public function getWidgets(): array
    {
        $name = $this->getName();
        return [
            $name            => [
                'icon'    => 'tags',
                'widget'  => 'PhpDebugBar.Widgets.VariableListWidget',
                'map'     => $name . '.errors',
                'default' => '{}'
            ],
            $name . ':badge' => [
                'map'     => $name . '.count',
                'default' => 'null'
            ]
        ];
    }
}
