<?php

declare(strict_types=1);

namespace JTL\Services\JTL\Validation\Rules;

use Eloquent\Pathogen\AbsolutePathInterface;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePath;
use JTL\Services\JTL\Validation\RuleInterface;
use JTL\Services\JTL\Validation\RuleResult;

/**
 * Class InPath
 * @package JTL\Services\JTL\Validation\Rules
 *
 * Validates that there is no path traversal in the specified path
 *
 * No transform
 */
class InPath implements RuleInterface
{
    /**
     * @var AbsolutePathInterface
     */
    protected AbsolutePathInterface $parentPath;

    /**
     * InPath constructor.
     * @param string|PathInterface $path
     * @throws InvalidPathStateException
     */
    public function __construct(string|PathInterface $path)
    {
        $parentPath       = $path instanceof PathInterface ? $path : Path::fromString($path);
        $parentPath       = $parentPath->normalize();
        $this->parentPath = $parentPath->toAbsolute();
    }

    /**
     * @inheritdoc
     * @param PathInterface|string $value
     */
    public function validate($value): RuleResult
    {
        $path = $value instanceof PathInterface ? $value : Path::fromString($value);
        $path = $path->normalize();
        if ($path instanceof RelativePath) {
            $path = $this->parentPath->join($path);
        }
        try {
            $path = $path->toAbsolute();
        } catch (InvalidPathStateException) {
            return new RuleResult(false, 'invalid path state', $value);
        }

        return $this->parentPath->isAncestorOf($path)
            ? new RuleResult(true, '', $value)
            : new RuleResult(false, 'path traversal detected', $value);
    }
}
