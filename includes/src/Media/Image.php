<?php

declare(strict_types=1);

namespace JTL\Media;

use Exception;
use Imagick;
use Intervention\Image\Constraint;
use Intervention\Image\Image as InImage;
use Intervention\Image\ImageManager;
use JTL\Media\Image\AbstractImage;
use JTL\Settings\Settings;
use JTL\Settings\Option\Image as ImageOption;
use JTL\Shop;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Image
 * @package JTL\Media
 */
class Image
{
    /**
     * Image types
     */
    public const TYPE_PRODUCT              = 'product';
    public const TYPE_CATEGORY             = 'category';
    public const TYPE_OPC                  = 'opc';
    public const TYPE_CONFIGGROUP          = 'configgroup';
    public const TYPE_VARIATION            = 'variation';
    public const TYPE_MANUFACTURER         = 'manufacturer';
    public const TYPE_NEWS                 = 'news';
    public const TYPE_NEWSCATEGORY         = 'newscategory';
    public const TYPE_CHARACTERISTIC       = 'characteristic';
    public const TYPE_CHARACTERISTIC_VALUE = 'characteristicvalue';

    /**
     * Image sizes
     */
    public const SIZE_XS = 'xs';
    public const SIZE_SM = 'sm';
    public const SIZE_MD = 'md';
    public const SIZE_LG = 'lg';
    public const SIZE_XL = 'xl';

    /**
     * Image size map
     *
     * @var string[]
     */
    private static array $sizes = [
        self::SIZE_XS,
        self::SIZE_SM,
        self::SIZE_MD,
        self::SIZE_LG,
        self::SIZE_XL
    ];

    /**
     * Image settings
     *
     * @var array|null
     */
    private static ?array $settings = null;

    /**
     * @var bool|null
     */
    private static ?bool $webPSupport = null;

    /**
     * @return string[]
     */
    public static function getAllSizes(): array
    {
        return self::$sizes;
    }

    /**
     *  Global image settings
     *
     * @return array
     */
    public static function getSettings(): array
    {
        if (self::$settings !== null) {
            return self::$settings;
        }
        $settings = Shop::getSettings([\CONF_BILDER, \CONF_BRANDING]);
        $branding = $settings['branding'];
        $settings = Settings::fromArray($settings);

        self::$settings         = [
            'background'                    => $settings->string(ImageOption::BACKGROUND),
            'container'                     => $settings->bool(ImageOption::USE_CONTAINER),
            'format'                        => \mb_convert_case(
                $settings->string(ImageOption::IMAGE_FORMAT),
                \MB_CASE_LOWER
            ),
            'quality'                       => $settings->int(ImageOption::JPG_QUALITY),
            'branding'                      => $branding,
            self::TYPE_PRODUCT              => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::PRODUCT_XS_WIDTH),
                    'height' => $settings->int(ImageOption::PRODUCT_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::PRODUCT_SM_WIDTH),
                    'height' => $settings->int(ImageOption::PRODUCT_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::PRODUCT_MD_WIDTH),
                    'height' => $settings->int(ImageOption::PRODUCT_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::PRODUCT_LG_WIDTH),
                    'height' => $settings->int(ImageOption::PRODUCT_LG_HEIGHT),
                ]
            ],
            self::TYPE_MANUFACTURER         => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::MANUFACTURER_XS_WIDTH),
                    'height' => $settings->int(ImageOption::MANUFACTURER_XS_HEIGHT)
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::MANUFACTURER_SM_WIDTH),
                    'height' => $settings->int(ImageOption::MANUFACTURER_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::MANUFACTURER_MD_WIDTH),
                    'height' => $settings->int(ImageOption::MANUFACTURER_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::MANUFACTURER_LG_WIDTH),
                    'height' => $settings->int(ImageOption::MANUFACTURER_LG_HEIGHT),
                ]
            ],
            self::TYPE_CHARACTERISTIC       => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_XS_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_SM_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_MD_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_LG_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_LG_HEIGHT),
                ]
            ],
            self::TYPE_CHARACTERISTIC_VALUE => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_VALUE_XS_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_VALUE_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_VALUE_SM_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_VALUE_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_VALUE_MD_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_VALUE_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::CHARACTERISTIC_VALUE_LG_WIDTH),
                    'height' => $settings->int(ImageOption::CHARACTERISTIC_VALUE_LG_HEIGHT),
                ]
            ],
            self::TYPE_CONFIGGROUP          => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::CONFIGGROUP_XS_WIDTH),
                    'height' => $settings->int(ImageOption::CONFIGGROUP_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::CONFIGGROUP_SM_WIDTH),
                    'height' => $settings->int(ImageOption::CONFIGGROUP_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::CONFIGGROUP_MD_WIDTH),
                    'height' => $settings->int(ImageOption::CONFIGGROUP_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::CONFIGGROUP_LG_WIDTH),
                    'height' => $settings->int(ImageOption::CONFIGGROUP_LG_HEIGHT),
                ]
            ],
            self::TYPE_CATEGORY             => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::CATEGORY_XS_WIDTH),
                    'height' => $settings->int(ImageOption::CATEGORY_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::CATEGORY_SM_WIDTH),
                    'height' => $settings->int(ImageOption::CATEGORY_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::CATEGORY_MD_WIDTH),
                    'height' => $settings->int(ImageOption::CATEGORY_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::CATEGORY_LG_WIDTH),
                    'height' => $settings->int(ImageOption::CATEGORY_LG_HEIGHT),
                ]
            ],
            self::TYPE_VARIATION            => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::VARIATION_XS_WIDTH),
                    'height' => $settings->int(ImageOption::VARIATION_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::VARIATION_SM_WIDTH),
                    'height' => $settings->int(ImageOption::VARIATION_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::VARIATION_MD_WIDTH),
                    'height' => $settings->int(ImageOption::VARIATION_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::VARIATION_LG_WIDTH),
                    'height' => $settings->int(ImageOption::VARIATION_LG_HEIGHT),
                ]
            ],
            self::TYPE_OPC                  => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::OPC_XS_WIDTH),
                    'height' => $settings->int(ImageOption::OPC_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::OPC_SM_WIDTH),
                    'height' => $settings->int(ImageOption::OPC_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::OPC_MD_WIDTH),
                    'height' => $settings->int(ImageOption::OPC_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::OPC_LG_WIDTH),
                    'height' => $settings->int(ImageOption::OPC_LG_HEIGHT),
                ]
            ],
            self::TYPE_NEWS                 => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::NEWS_XS_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::NEWS_SM_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::NEWS_MD_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::NEWS_LG_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_LG_HEIGHT),
                ]
            ],
            self::TYPE_NEWSCATEGORY         => [
                self::SIZE_XS => [
                    'width'  => $settings->int(ImageOption::NEWS_CATEGORY_XS_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_CATEGORY_XS_HEIGHT),
                ],
                self::SIZE_SM => [
                    'width'  => $settings->int(ImageOption::NEWS_CATEGORY_SM_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_CATEGORY_SM_HEIGHT),
                ],
                self::SIZE_MD => [
                    'width'  => $settings->int(ImageOption::NEWS_CATEGORY_MD_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_CATEGORY_MD_HEIGHT),
                ],
                self::SIZE_LG => [
                    'width'  => $settings->int(ImageOption::NEWS_CATEGORY_LG_WIDTH),
                    'height' => $settings->int(ImageOption::NEWS_CATEGORY_LG_HEIGHT),
                ]
            ],
            'naming'                        => [
                self::TYPE_PRODUCT              => $settings->int(ImageOption::PRODUCT_NAMES),
                self::TYPE_CATEGORY             => $settings->int(ImageOption::CATEGORY_NAMES),
                self::TYPE_VARIATION            => $settings->int(ImageOption::VARIATION_NAMES),
                self::TYPE_MANUFACTURER         => $settings->int(ImageOption::MANUFACTURER_NAMES),
                self::TYPE_CHARACTERISTIC       => $settings->int(ImageOption::CHARACTERISTIC_NAMES),
                self::TYPE_CHARACTERISTIC_VALUE => $settings->int(ImageOption::CHARACTERISTIC_VALUE_NAMES),
            ]
        ];
        self::$settings['size'] = self::$settings[self::TYPE_PRODUCT];

        return self::$settings;
    }

    /**
     * @param string $filepath
     * @return string
     */
    public static function getMimeType(string $filepath): string
    {
        return \image_type_to_mime_type(self::getImageType($filepath) ?? \IMAGETYPE_JPEG);
    }

    /**
     * @param string $filepath
     * @return int|null
     */
    public static function getImageType(string $filepath): ?int
    {
        if (\function_exists('exif_imagetype')) {
            return \exif_imagetype($filepath) ?: null;
        }

        return \getimagesize($filepath)[2] ?? null;
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getCleanFilename(string $filename): string
    {
        $source   = ['.', ' ', '/', 'ä', 'ö', 'ü', 'ß'];
        $replace  = ['-', '-', '-', 'ae', 'oe', 'ue', 'ss'];
        $filename = \str_replace($source, $replace, \mb_convert_case($filename, \MB_CASE_LOWER));

        return \preg_replace('/[^' . AbstractImage::REGEX_ALLOWED_CHARS . ']/u', '', $filename) ?? $filename;
    }

    /**
     * @param array{type?: string, error?: int, tmp_name?: string} $file
     * @param string[]|null                                        $allowed
     * @return bool
     */
    public static function isImageUpload(array $file, ?array $allowed = null): bool
    {
        $allowed = $allowed
            ?? [
                'image/jpeg',
                'image/jpg',
                'image/pjpeg',
                'image/gif',
                'image/x-png',
                'image/png',
                'image/bmp',
                'image/webp'
            ];
        $finfo   = \finfo_open(\FILEINFO_MIME_TYPE);

        return $finfo !== false
            && isset($file['type'], $file['error'], $file['tmp_name'])
            && $file['error'] === \UPLOAD_ERR_OK
            && \in_array($file['type'], $allowed, true)
            && \in_array(\finfo_file($finfo, $file['tmp_name']) ?: '???', $allowed, true);
    }

    /**
     * @param MediaImageRequest $req
     * @param bool              $streamOutput
     * @param bool              $sendResponse
     * @return ResponseInterface|void
     * @throws Exception
     */
    public static function render(MediaImageRequest $req, bool $streamOutput = false, bool $sendResponse = false)
    {
        $rawPath = $req->getRaw();
        if ($rawPath === null || !\is_file($rawPath)) {
            throw new Exception(\sprintf('Image "%s" does not exist', $rawPath));
        }
        $settings  = self::getSettings();
        $thumbnail = $req->getThumb($req->getSize(), true);
        $manager   = new ImageManager(['driver' => self::getImageDriver()]);
        $img       = $manager->make($rawPath);
        $regExt    = $req->getExt();
        if (($regExt === 'jpg' || $regExt === 'jpeg') && \str_starts_with($settings['background'], 'rgba(')) {
            $settings['background'] = self::rgba2rgb($settings['background']);
        }
        if ($settings['container'] === true) {
            $canvas = $manager->canvas($img->width(), $img->height(), $settings['background']);
            $canvas->insert($img);
            $img = $canvas;
        }
        self::checkDirectory($thumbnail);
        self::resize($req, $img, $settings);
        self::addBranding($manager, $req, $img);
        self::optimizeImage($img, $regExt);
        \executeHook(\HOOK_IMAGE_RENDER, [
            'image'    => $img,
            'settings' => $settings,
            'path'     => $thumbnail
        ]);
        $img->save($thumbnail, $settings['quality'], $regExt);
        if ($sendResponse === true) {
            return $img->psrResponse($regExt);
        }
        if ($streamOutput) {
            $response = $img->response($regExt);
            if (\is_object($response) && \method_exists($response, 'send')) {
                $response->send();
            } else {
                echo $response;
            }
        }
    }

    /**
     * @param InImage $image
     * @param string  $extension
     */
    private static function optimizeImage(InImage $image, string $extension): void
    {
        // @todo: doesn't look very good with small images
//        $image->blur(1);
        // @todo: strange blue tones with PNG
//        if (self::getImageDriver() === 'imagick') {
//            $image->getCore()->setColorspace(\Imagick::COLORSPACE_RGB);
//            $image->getCore()->transformImageColorspace(\Imagick::COLORSPACE_RGB);
//            $image->getCore()->stripImage();
//        }
        if ($extension === 'jpg') {
            $image->interlace();
        }
    }

    /**
     * @param MediaImageRequest          $req
     * @param InImage                    $img
     * @param array<string, bool|string> $settings
     */
    private static function resize(MediaImageRequest $req, InImage $img, array $settings): void
    {
        $containerDim = $req->getSize();
        $maxWidth     = $containerDim->getWidth();
        $maxHeight    = $containerDim->getHeight();
        if ($maxWidth > 0 && $maxHeight > 0) {
            if ($img->getWidth() > $maxWidth || $img->getHeight() > $maxHeight) {
                $img->resize($maxWidth, $maxHeight, static function (Constraint $constraint): void {
                    $constraint->aspectRatio();
                });
            }
            if ($settings['container'] === true && $req->getType() !== self::TYPE_OPC) {
                $img->resizeCanvas($maxWidth, $maxHeight, 'center', false, $settings['background']);
            }
        }
    }

    /**
     * @param ImageManager      $manager
     * @param MediaImageRequest $req
     * @param InImage           $img
     */
    private static function addBranding(ImageManager $manager, MediaImageRequest $req, InImage $img): void
    {
        $config = self::getSettings()['branding'][$req->getType()] ?? null;
        if ($config === null || !\in_array($req->getSize()->getSize(), $config->imagesizes, true)) {
            return;
        }
        $watermark = $manager->make($config->path);
        if ($config->size > 0) {
            $brandWidth  = \round(($img->getWidth() * $config->size) / 100.0);
            $brandHeight = \round(($brandWidth / $watermark->getWidth()) * $watermark->getHeight());
            $newWidth    = (int)\min($watermark->getWidth(), $brandWidth);
            $newHeight   = (int)\min($watermark->getHeight(), $brandHeight);
            $watermark->resize($newWidth, $newHeight, static function (Constraint $constraint): void {
                $constraint->aspectRatio();
            });
            $watermark->opacity(100 - $config->transparency);
            $img->insert($watermark, $config->position, 10, 10);
        }
    }

    /**
     * @param string $thumbnail
     * @throws Exception
     */
    private static function checkDirectory(string $thumbnail): void
    {
        $directory = \pathinfo($thumbnail, \PATHINFO_DIRNAME);
        if (!\is_dir($directory) && !\mkdir($directory, 0777, true) && !\is_dir($directory)) {
            $error = \error_get_last();
            if (empty($error)) {
                $error = 'Unable to create directory ' . $directory;
            }
            throw new Exception(\is_array($error) ? $error['message'] : $error);
        }
    }

    /**
     * @return string
     */
    public static function getImageDriver(): string
    {
        return \extension_loaded('imagick') && !\FORCE_IMAGEDRIVER_GD ? 'imagick' : 'gd';
    }

    /**
     * @return bool
     */
    public static function hasWebPSupport(): bool
    {
        if (self::getSettings()['format'] !== 'auto') {
            return false;
        }
        if (self::$webPSupport === null) {
            self::$webPSupport = self::getImageDriver() === 'imagick'
                ? \count(Imagick::queryFormats('WEBP')) > 0
                : (bool)(\gd_info()['WebP Support'] ?? false);
        }

        return self::$webPSupport;
    }

    /**
     * @param string $color
     * @return string
     */
    public static function rgba2rgb(string $color): string
    {
        $background = [255, 255, 255];
        $rgbaColor  = \explode(',', \rtrim(\substr($color, \strlen('rgba(')), ')'));
        $red        = \sprintf('%d', $rgbaColor[0]);
        $green      = \sprintf('%d', $rgbaColor[1]);
        $blue       = \sprintf('%d', $rgbaColor[2]);
        $alpha      = \sprintf('%.2f', $rgbaColor[3]);

        $ored   = ((1 - $alpha) * $background[0]) + ($alpha * $red);
        $ogreen = ((1 - $alpha) * $background[1]) + ($alpha * $green);
        $oblue  = ((1 - $alpha) * $background[2]) + ($alpha * $blue);

        return 'rgb(' . \sprintf('%d', $ored) . ', ' . \sprintf('%d', $ogreen) . ', ' . \sprintf('%d', $oblue) . ')';
    }
}
