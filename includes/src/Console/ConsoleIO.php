<?php

declare(strict_types=1);

namespace JTL\Console;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Terminal;

/**
 * Class ConsoleIO
 * @package JTL\Console
 */
class ConsoleIO extends OutputStyle
{
    public const MAX_LINE_LENGTH = 120;

    /**
     * @var int
     */
    protected int $lastMessagesLength = 0;

    /**
     * @var bool
     */
    protected bool $overwrite = true;

    /**
     * @var SymfonyQuestionHelper|null
     */
    private ?SymfonyQuestionHelper $questionHelper = null;

    /**
     * @var ProgressBar|null
     */
    private ?ProgressBar $progressBar = null;

    /**
     * @var int
     */
    private int $lineLength;

    /**
     * @var BufferedOutput
     */
    private BufferedOutput $bufferedOutput;

    /**
     * ConsoleIO constructor.
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param HelperSet|null  $helperSet
     */
    public function __construct(
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
        private readonly ?HelperSet $helperSet = null
    ) {
        $formatter = null;
        if ($output->getFormatter() !== null) {
            $formatter = clone $output->getFormatter();
        }
        $this->bufferedOutput = new BufferedOutput($output->getVerbosity(), false, $formatter);
        $this->lineLength     = $this->getTerminalWidth() - (int)(\DIRECTORY_SEPARATOR === '\\');

        parent::__construct($output);
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return HelperSet|null
     */
    public function getHelperSet(): ?HelperSet
    {
        return $this->helperSet;
    }

    /**
     * @inheritdoc
     */
    public function isQuiet(): bool
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_QUIET;
    }

    /**
     * @return bool
     */
    public function isNormal(): bool
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
    }

    /**
     * @inheritdoc
     */
    public function isVerbose(): bool
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * @inheritdoc
     */
    public function isVeryVerbose(): bool
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
    }

    /**
     * @inheritdoc
     */
    public function isDebug(): bool
    {
        return $this->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function overwrite(string $message): self
    {
        $lines = \explode("\n", $message);
        if ($this->lastMessagesLength !== 0) {
            foreach ($lines as $i => $line) {
                $len = Helper::width(Helper::removeDecoration($this->bufferedOutput->getFormatter(), $line));
                if ($this->lastMessagesLength > $len) {
                    $lines[$i] = $line . \str_repeat("\x20", $this->lastMessagesLength - $len);
                }
            }
        }
        if ($this->overwrite) {
            $this->write("\x0D");
        }

        $this->lastMessagesLength = 0;
        foreach ($lines as $line) {
            $len = Helper::width(Helper::removeDecoration($this->bufferedOutput->getFormatter(), $line));
            if ($len > $this->lineLength) {
                $line = \substr($line, 0, $this->lineLength);
            }

            $this->write($line);

            if ($len > $this->lastMessagesLength) {
                $this->lastMessagesLength = $len;
            }
        }

        return $this;
    }

    /**
     * @param callable    $process
     * @param string|null $format
     * @param bool        $clearMessage
     * @return $this
     */
    public function progress($process, $format = null, bool $clearMessage = true): self
    {
        $progress = parent::createProgressBar();

        if ($format === null) {
            $format = '%percent:3s%% [%bar%] %current% of %max%';
        }

        $progress->setFormat($format);
        $progress->setMessage('');
        $progress->setEmptyBarCharacter(' ');
        $progress->setBarCharacter('<comment>=</comment>');
        // $progress->setRedrawFrequency(1);

        $lastPercent = 0;
        $lastMessage = null;
        $lastRedraw  = \microtime(true);

        $callback = static function (
            $total,
            $current,
            $message = ''
        ) use (
            &$progress,
            &$lastRedraw,
            &$lastPercent,
            &$lastMessage
        ): void {
            if ($progress->getMaxSteps() === 0) {
                $progress->start($total);
            }

            // update frequence 250ms or on percent value changed
            $off = (\microtime(true) - $lastRedraw) * 1000;
            if ($off > 250 || $lastPercent !== $current || $lastMessage !== $message) {
                $progress->setMessage($message);
                if ($current > $lastPercent) {
                    $progress->setProgress($current);
                }
                $progress->display();
                $lastRedraw  = \microtime(true);
                $lastPercent = $current;
                $lastMessage = $message;
            }
        };

        if (\is_callable($process)) {
            $process($callback);
        }

        if ($clearMessage) {
            $progress->setMessage('');
        }
        $progress->finish();
        $this->writeln('');

        return $this;
    }

    /**
     * @param int    $current
     * @param int    $limit
     * @param string $step
     * @return $this
     */
    public function setStep($current, $limit, $step): self
    {
        $this->setLabel('Step ' . $current . ' of ' . $limit, $step);

        return $this;
    }

    /**
     * @param string      $title
     * @param string|null $sub
     * @return $this
     */
    public function setLabel($title, $sub = null): self
    {
        $this->writeln('');
        $this->writeln('<comment>' . $title . '</comment> ' . ($sub !== null ? '<info>' . $sub . '</info>' : ''));
        $this->writeln('');

        return $this;
    }

    /**
     * @return bool
     */
    public function isInteractive(): bool
    {
        return $this->getInput()->hasOption('no-interaction') === false;
    }

    /**
     * Formats a message as a block of text.
     *
     * @param string|array $messages The message to write in the block
     * @param string|null  $type The block type (added in [] on first line)
     * @param string|null  $style The style to apply to the whole block
     * @param string       $prefix The prefix for the block
     * @param bool         $padding Whether to add vertical padding
     *
     * @return $this
     */
    public function block(
        string|array $messages,
        ?string $type = null,
        ?string $style = null,
        string $prefix = ' ',
        bool $padding = false
    ): self {
        $this->autoPrependBlock();

        $messages = \is_array($messages) ? \array_values($messages) : [$messages];
        $lines    = [];
        // add type
        if ($type !== null) {
            $messages[0] = \sprintf('[%s] %s', $type, $messages[0]);
        }

        // wrap and add newlines for each element
        foreach ($messages as $key => $message) {
            $message = OutputFormatter::escape($message);
            $lines   = \array_merge(
                $lines,
                \explode(
                    \PHP_EOL,
                    \wordwrap($message, $this->lineLength - Helper::width($prefix), \PHP_EOL, true)
                )
            );

            if (\count($messages) > 1 && $key < \count($messages) - 1) {
                $lines[] = '';
            }
        }

        if ($padding && $this->isDecorated()) {
            \array_unshift($lines, '');
            $lines[] = '';
        }

        $length = \max(
            \array_map(
                function ($line) {
                    return Helper::width(Helper::removeDecoration($this->getFormatter(), $line));
                },
                $lines
            )
        );

        $length += \strlen($prefix) * 2;

        foreach ($lines as &$line) {
            $line = \sprintf('%s%s', $prefix, $line);
            $line .= \str_repeat(' ', $length - Helper::width(Helper::removeDecoration($this->getFormatter(), $line)));

            if ($style) {
                $line = \sprintf('<%s>%s</>', $style, $line);
            }
        }
        unset($line);

        $this->writeln($lines);
        $this->newLine();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function title(string $message): self
    {
        $this->autoPrependBlock();
        $this->writeln(
            [
                \sprintf('<comment>%s</comment>', $message),
                \sprintf(
                    '<comment>%s</comment>',
                    \str_repeat('=', Helper::width(Helper::removeDecoration($this->getFormatter(), $message)))
                ),
            ]
        );
        $this->newLine();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function section(string $message): self
    {
        $this->autoPrependBlock();
        $this->writeln(
            [
                \sprintf('<comment>%s</comment>', $message),
                \sprintf(
                    '<comment>%s</comment>',
                    \str_repeat('-', Helper::width(Helper::removeDecoration($this->getFormatter(), $message)))
                ),
            ]
        );
        $this->newLine();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function listing(array $elements): self
    {
        $this->autoPrependText();
        $elements = \array_map(
            static function ($element): string {
                return \sprintf(' * %s', $element);
            },
            $elements
        );
        $this->writeln($elements);
        $this->newLine();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function text(string|array $message): self
    {
        $this->autoPrependText();

        $messages = \is_array($message) ? \array_values($message) : [$message];
        foreach ($messages as $msg) {
            $this->writeln(\sprintf(' %s', $msg));
        }

        return $this;
    }

    /**
     * @param string|array $message
     * @return $this
     */
    public function comment(string|array $message): self
    {
        $this->autoPrependText();

        $messages = \is_array($message) ? \array_values($message) : [$message];
        foreach ($messages as $msg) {
            $this->writeln(\sprintf('<fg=white;bg=magenta>%s</>', $msg));
        }

        return $this;
    }

    /**
     * @param array|string $message
     * @return $this
     */
    public function verbose(array|string $message): self
    {
        return $this->block($message, null, 'fg=black;bg=cyan', ' ', true);
    }

    /**
     * @inheritdoc
     */
    public function success(string|array $message): self
    {
        return $this->block($message, null, 'fg=black;bg=green', ' ', true);
    }

    /**
     * @inheritdoc
     */
    public function error(string|array $message): self
    {
        return $this->block($message, null, 'fg=white;bg=red', ' ', true);
    }

    /**
     * @inheritdoc
     */
    public function warning(string|array $message): self
    {
        return $this->block($message, null, 'fg=black;bg=yellow', ' ', true);
    }

    /**
     * @inheritdoc
     */
    public function note(string|array $message): self
    {
        return $this->block($message, null, 'fg=white;bg=blue', ' ', true);
    }

    /**
     * @inheritdoc
     */
    public function caution(string|array $message): self
    {
        return $this->block($message, null, 'fg=white;bg=red', ' ', true);
    }

    /**
     * @inheritdoc
     */
    public function table(array $headers, array $rows, array $options = []): self
    {
        $options = \array_merge([
            'style' => 'symfony-style-guide'
        ], $options);
        $headers = \array_map(
            static function ($value): string {
                return \sprintf('<info>%s</info>', $value);
            },
            $headers
        );

        $table = new Table($this);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($options['style']);
        if (isset($options['columnWidth']) && \count($options['columnWidth']) > 0) {
            $table->setColumnWidths($options['columnWidth']);
        }
        $table->render();
        $this->newLine();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function ask(string $question, string $default = null, callable $validator = null): mixed
    {
        $instance = new Question($question, $default);
        $instance->setValidator($validator);

        return $this->askQuestion($instance);
    }

    /**
     * @inheritdoc
     */
    public function askHidden(string $question, callable $validator = null): mixed
    {
        $instance = new Question($question);

        $instance->setHidden(true);
        $instance->setValidator($validator);

        return $this->askQuestion($instance);
    }

    /**
     * @inheritdoc
     */
    public function confirm(string $question, bool $default = true): bool
    {
        return $this->askQuestion(new ConfirmationQuestion($question, $default));
    }

    /**
     * @inheritdoc
     */
    public function choice(string $question, array $choices, $default = null): mixed
    {
        if ($default !== null) {
            $values  = \array_flip($choices);
            $default = $values[$default];
        }

        return $this->askQuestion(new ChoiceQuestion($question, $choices, $default));
    }

    /**
     * @inheritdoc
     */
    public function progressStart(int $max = 0): self
    {
        $this->progressBar = $this->createProgressBar($max);
        $this->progressBar->start();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function progressAdvance(int $step = 1): self
    {
        $this->getProgressBar()->advance($step);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function progressFinish(): self
    {
        $this->getProgressBar()->finish();
        $this->newLine(2);
        $this->progressBar = null;

        return $this;
    }

    /**
     * @param int $max
     * @return ProgressBar
     */
    public function createProgressBar(int $max = 0): ProgressBar
    {
        $progressBar = parent::createProgressBar($max);

        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $progressBar->setEmptyBarCharacter('░'); // light shade character \u2591
            $progressBar->setProgressCharacter('');
            $progressBar->setBarCharacter('▓'); // dark shade character \u2593
        }

        return $progressBar;
    }

    /**
     * @param Question $question
     * @return string
     */
    public function askQuestion(Question $question)
    {
        if ($this->input->isInteractive()) {
            $this->autoPrependBlock();
        }

        if (!$this->questionHelper) {
            $this->questionHelper = new SymfonyQuestionHelper();
        }

        $answer = $this->questionHelper->ask($this->input, $this, $question);

        if ($this->input->isInteractive()) {
            $this->newLine();
        }

        return $answer;
    }

    /**
     * @inheritdoc
     */
    public function writeln($messages, int $type = self::OUTPUT_NORMAL): self
    {
        parent::writeln($messages, $type);
        $this->bufferedOutput->writeln($this->reduceBuffer($messages), $type);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function write($messages, bool $newline = false, int $type = self::OUTPUT_NORMAL): self
    {
        parent::write($messages, $newline, $type);
        $this->bufferedOutput->write($this->reduceBuffer($messages), $newline, $type);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function newLine(int $count = 1): self
    {
        parent::newLine($count);
        $this->bufferedOutput->write(\str_repeat("\n", $count));

        return $this;
    }

    /**
     * @return ProgressBar
     */
    private function getProgressBar(): ProgressBar
    {
        if (!$this->progressBar) {
            throw new RuntimeException('The ProgressBar is not started.');
        }

        return $this->progressBar;
    }

    /**
     * @return int
     */
    private function getTerminalWidth(): int
    {
        $terminal   = new Terminal();
        $dimensions = [$terminal->getWidth(), $terminal->getHeight()];

        return $dimensions[0] ?: self::MAX_LINE_LENGTH;
    }

    /**
     * @return self
     */
    private function autoPrependBlock(): self
    {
        $chars = \substr(\str_replace(\PHP_EOL, "\n", $this->bufferedOutput->fetch()), -2);

        if (!isset($chars[0])) {
            return $this->newLine(); //empty history, so we should start with a new line.
        }
        // Prepend new line for each non LF chars (This means no blank line was output before)
        $this->newLine(2 - \substr_count($chars, "\n"));

        return $this;
    }

    /**
     * @return $this
     */
    private function autoPrependText(): self
    {
        $fetched = $this->bufferedOutput->fetch();
        // Prepend new line if last char isn't EOL:
        if (!\str_ends_with($fetched, "\n")) {
            $this->newLine();
        }

        return $this;
    }

    /**
     * @param array|mixed $messages
     * @return array
     */
    private function reduceBuffer($messages): array
    {
        // We need to know if the two last chars are PHP_EOL
        // Preserve the last 4 chars inserted (PHP_EOL on windows is two chars) in the history buffer
        return \array_map(
            static function ($value): string {
                return \substr($value, -4);
            },
            \array_merge([$this->bufferedOutput->fetch()], (array)$messages)
        );
    }
}
