<?php

declare(strict_types=1);

namespace JTL\Model;

use DateInterval;
use DateTime;
use Exception;

/**
 * Class ModelHelper
 * @package App\Models
 */
final class ModelHelper
{
    /**
     * @param DateTime|string|null $value
     * @param string               $format
     * @return string|null
     */
    private static function formatDateTime(DateTime|string|null $value, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (\is_a($value, DateTime::class)) {
            return $value->format($format);
        }
        if (\is_string($value)) {
            return self::fromStrToDateTime($value)?->format($format);
        }

        return null;
    }

    /**
     * @param DateTime|string|null $value
     * @return string|null
     */
    public static function fromDateTimeToStr(DateTime|string|null $value): ?string
    {
        return self::formatDateTime($value);
    }

    /**
     * @param DateTime|string|null $value
     * @param DateTime|string|null $default
     * @return DateTime|null
     */
    public static function fromStrToDateTime(DateTime|string|null $value, DateTime|string $default = null): ?DateTime
    {
        if (($value === null && $default === null) || \is_a($value, DateTime::class)) {
            return $value;
        }
        if (\is_string($value)) {
            try {
                return new DateTime(\str_replace('now()', 'now', $value));
            } catch (Exception) {
                return self::fromStrToDateTime($default);
            }
        }

        return self::fromStrToDateTime($default);
    }

    /**
     * @param DateInterval|string|null $value
     * @return string|null
     */
    public static function fromTimeToStr(DateInterval|string|null $value): ?string
    {
        if (\is_a($value, DateInterval::class)) {
            return $value->format('%H:%I:%S');
        }
        if (\is_string($value)) {
            return self::fromStrToTime($value)?->format('%H:%I:%S');
        }

        return null;
    }

    /**
     * @param DateInterval|string|null $value
     * @param DateInterval|string|null $default
     * @return DateInterval|null
     */
    public static function fromStrToTime(
        DateInterval|string|null $value,
        DateInterval|string $default = null
    ): ?DateInterval {
        if (!isset($value) && !isset($default)) {
            return null;
        }
        if (\is_a($value, DateInterval::class)) {
            return $value;
        }
        if (!\is_string($value)) {
            return self::fromStrToTime($default);
        }
        try {
            $splits = \explode(':', $value, 3);

            return match (\count($splits)) {
                0       => DateInterval::createFromDateString($value),
                1       => new DateInterval('PT' . (int)$splits[0] . 'H'),
                2       => new DateInterval('PT' . (int)$splits[0] . 'H' . (int)$splits[1] . 'M'),
                3       => new DateInterval(
                    'PT' . (int)$splits[0] . 'H' . (int)$splits[1] . 'M' . (int)$splits[2] . 'S'
                ),
                default => self::fromStrToTime($default),
            };
        } catch (Exception) {
            return self::fromStrToTime($default);
        }
    }

    /**
     * @param DateTime|string|null $value
     * @return string|null
     */
    public static function fromDateToStr(DateTime|string|null $value): ?string
    {
        return self::formatDateTime($value, 'Y-m-d');
    }

    /**
     * @param DateTime|string|null $value
     * @param DateTime|string|null $default
     * @return DateTime|null
     */
    public static function fromStrToDate(DateTime|string|null $value, DateTime|string $default = null): ?DateTime
    {
        $dateTime = self::fromStrToDateTime($value, $default);
        $dateTime?->setTime(0, 0);

        return $dateTime;
    }

    /**
     * @param DateTime|string|null $value
     * @return string|null
     */
    public static function fromTimestampToStr(DateTime|string|null $value): ?string
    {
        return self::formatDateTime($value, 'Y-m-d H:i:s.u');
    }

    /**
     * @param DateTime|string|null $value
     * @param DateTime|string|null $default
     * @return DateTime|null
     */
    public static function fromStrToTimestamp(DateTime|string|null $value, DateTime|string $default = null): ?DateTime
    {
        return self::fromStrToDateTime($value, $default);
    }

    /**
     * @param string|null $value
     * @param bool|null   $default
     * @return bool|null
     */
    public static function fromCharToBool(?string $value, bool $default = null): ?bool
    {
        if (\is_string($value)) {
            return \in_array(\strtoupper($value), ['Y', 'J', 'TRUE']);
        }

        return $default;
    }

    /**
     * @param bool $value
     * @return string
     */
    public static function fromBoolToChar(bool $value): string
    {
        return $value ? 'Y' : 'N';
    }

    /**
     * @param int|string $value
     * @param bool       $default
     * @return bool
     */
    public static function fromIntToBool(int|string $value, bool $default = false): bool
    {
        if (\is_numeric($value)) {
            return $value > 0;
        }

        return $default;
    }

    /**
     * @param bool $value
     * @return int
     */
    public static function fromBoolToInt(bool $value): int
    {
        return $value ? 1 : 0;
    }
}
