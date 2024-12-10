<?php declare(strict_types=1);

namespace JTLShop\SemVer;

/**
 * Class VersionCollection
 *
 * Sorting sets of SemVer Versions
 *
 * @package JTLShop\SemVer
 */
class VersionCollection extends \ArrayIterator
{
    /**
     * VersionCollection constructor.
     *
     * @param array $versionArr
     * @param int   $flags
     */
    public function __construct(array $versionArr = [], int $flags = 0)
    {
        $versions = [];

        foreach ($versionArr as $version) {
            try {
                $versions[] = Version::parse($version);
            } catch (\Exception) {
                $versions[] = Version::parse($version->reference);
            }
        }

        parent::__construct($versions, $flags);
    }

    /**
     * @param Version|string|int $value
     */
    public function append($value): void
    {
        $value = Version::parse($value);

        parent::append($value);
    }

    /**
     * @param int|string|Version $version
     * @return mixed|null
     */
    public function getLatestBuild(int|string|Version $version)
    {
        $version            = Version::parse($version);
        $latestBuildVersion = null;
        $relatedVersions    = $this->getBuilds($version);

        foreach ($relatedVersions as $relatedVersion) {
            if (empty($latestBuildVersion)) {
                $latestBuildVersion = $relatedVersion;
            } elseif ($relatedVersion->greaterThan($latestBuildVersion)) {
                $latestBuildVersion = $relatedVersion;
            }
        }

        return $latestBuildVersion;
    }

    /**
     * @param Version|string $version
     * @return VersionCollection
     */
    public function getMinors($version): self
    {
        $version         = Version::parse($version);
        $relatedVersions = [];
        foreach ($this as $vcVersion) {
            if ($vcVersion->getMajor() === $version->getMajor()) {
                $relatedVersions[] = $vcVersion;
            }
        }

        return new self($relatedVersions);
    }

    /**
     * @param Version|string $version
     * @return VersionCollection
     */
    public function getBuilds($version): self
    {
        $version         = Version::parse($version);
        $relatedVersions = [];
        foreach ($this as $vcVersion) {
            if ($vcVersion->getMajor() === $version->getMajor() && $vcVersion->getMinor() === $version->getMinor()) {
                $relatedVersions[] = $vcVersion;
            }
        }

        return new self($relatedVersions);
    }

    /**
     * @param string|int|Version $start
     * @param string|int|Version $end
     * @return VersionCollection
     */
    public function getVersionRange($start, $end): self
    {
        $startKey = 0;
        $endKey   = 0;
        foreach ($this as $key => $version) {
            if ($version->equals(Version::parse($start))) {
                $startKey = $key;
            }
            if ($version->equals(Version::parse($end))) {
                $endKey = $key;
            }
        }

        if ($startKey === $endKey) {
            return new self();
        }
        $arr = [];
        foreach ($this as $key => $version) {
            if ($key >= $startKey && $key <= $endKey) {
                $arr[] = $version;
            }
        }

        return new self($arr);
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function versionExists($version): bool
    {
        $exists = false;
        $copy   = $this->getArrayCopy();
        \array_walk($copy, static function (Version $item) use (&$exists, $version) {
            if ($item->equals($version)) {
                $exists = true;
            }
        });

        return $exists;
    }

    /**
     * @param $currentVersion
     * @return Version|string|null
     */
    public function getNextVersion($currentVersion)
    {
        foreach ($this as $key => $version) {
            if ($version->equals($currentVersion)) {
                return $this[($key + 1)] ?? null;
            }
        }

        return null;
    }
}
