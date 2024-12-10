<?php

declare(strict_types=1);

namespace JTL\OPC\Portlets\Video;

use JTL\OPC\InputType;
use JTL\OPC\Portlet;
use JTL\OPC\PortletInstance;
use JTL\Shop;

/**
 * Class Video
 * @package JTL\OPC\Portlets
 */
class Video extends Portlet
{
    /**
     * @param PortletInstance $instance
     * @return void
     */
    public function initInstance(PortletInstance $instance)
    {
        if ($instance->getProperty('video-vendor') === 'youtube') {
            $instance->video = \JTL\Media\Video::fromUrl(
                'https://www.youtube.com/?v=' . $instance->getProperty('video-yt-id')
            );
            /** @var int|string $start */
            $start = $instance->getProperty('video-yt-start');
            /** @var int|string $end */
            $end = $instance->getProperty('video-yt-end');
            /** @var string $playlist */
            $playlist = $instance->getProperty('video-yt-playlist');
            /** @var string $rel */
            $rel = $instance->getProperty('video-yt-rel');
            if ((int)$start > 0) {
                $instance->video->setStartSec((int)$start);
            }
            if ((int)$end > 0) {
                $instance->video->setEndSec((int)$end);
            }
            if (!empty($playlist)) {
                $instance->video->setExtraGetArg('playlist', $playlist);
            }
            $instance->video->setRelated($rel === '1');
            $instance->video->setExtraGetArg('color', $instance->getProperty('video-yt-color'));
            $instance->video->setExtraGetArg('controls', $instance->getProperty('video-yt-controls'));
        } elseif ($instance->getProperty('video-vendor') === 'vimeo') {
            /** @var string $id */
            $id = $instance->getProperty('video-vim-id');
            /** @var int|numeric-string $loop */
            $loop            = $instance->getProperty('video-vim-loop');
            $instance->video = \JTL\Media\Video::fromUrl(
                'https://vimeo.com/' . $id
            );
            $instance->video->setLoop((bool)$loop === true);
        } else {
            /** @var string $url */
            $url = $instance->getProperty('video-local-url');
            /** @var int|numeric-string $loop */
            $loop            = $instance->getProperty('video-local-loop');
            $instance->video = \JTL\Media\Video::fromUrl($url);
            $instance->video->setLoop((bool)$loop);
        }
        /** @var int|string $width */
        $width = $instance->getProperty('video-width');
        /** @var int|string $heigth */
        $heigth = $instance->getProperty('video-height');
        $instance->video->setWidth((int)$width);
        $instance->video->setHeight((int)$heigth);
    }

    /**
     * @param PortletInstance $instance
     * @return string|null
     */
    public function getPreviewImageUrl(PortletInstance $instance): ?string
    {
        return $instance->video->getPreviewImageUrl();
    }

    /**
     * @return string
     */
    public function getPreviewOverlayUrl(): string
    {
        return Shop::getURL() . '/' . \PFAD_INCLUDES . 'src/OPC/Portlets/Video/preview.svg';
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        return $this->getFontAwesomeButtonHtml('fas fa-film');
    }

    /**
     * @return array<string, mixed>
     */
    public function getPropertyDesc(): array
    {
        return [
            // general
            'video-title'      => [
                'label' => \__('title'),
                'width' => 100,
            ],
            'video-width'      => [
                'type'    => InputType::NUMBER,
                'label'   => \__('widthPx'),
                'default' => 600,
                'width'   => 33,
            ],
            'video-height'     => [
                'type'    => InputType::NUMBER,
                'label'   => \__('heightPx'),
                'default' => 338,
                'width'   => 33,
            ],
            'video-responsive' => [
                'type'    => InputType::RADIO,
                'label'   => \__('embedResponsive'),
                'default' => true,
                'options' => [
                    true  => \__('yes'),
                    false => \__('no'),
                ],
                'width'   => 33,
            ],
            'video-vendor'     => [
                'label'       => \__('source'),
                'type'        => InputType::SELECT,
                'default'     => 'youtube',
                'options'     => [
                    'youtube' => 'YouTube',
                    'vimeo'   => 'Vimeo',
                    'local'   => \__('localVideo'),
                ],
                'childrenFor' => [
                    'youtube' => [
                        'video-yt-hint'     => [
                            'label' => \__('note'),
                            'type'  => InputType::HINT,
                            'class' => 'danger',
                            'text'  => \__('youtubeNote'),
                        ],
                        'video-yt-id'       => [
                            'label'   => \__('videoID'),
                            'default' => 'xITQHgJ3RRo',
                            'help'    => \__('videoIDHelpYoutube'),
                        ],
                        'video-yt-start'    => [
                            'label' => \__('startSec'),
                            'type'  => InputType::NUMBER,
                            'width' => 50,
                        ],
                        'video-yt-end'      => [
                            'label' => \__('endSec'),
                            'type'  => InputType::NUMBER,
                            'width' => 50,
                        ],
                        'video-yt-controls' => [
                            'label'   => \__('showControls'),
                            'type'    => InputType::RADIO,
                            'inline'  => true,
                            'options' => [
                                '1' => \__('yes'),
                                '0' => \__('no'),
                            ],
                            'default' => '1',
                            'width'   => 33,
                        ],
                        'video-yt-rel'      => [
                            'label'   => \__('showSimilarVideos'),
                            'type'    => InputType::RADIO,
                            'inline'  => true,
                            'options' => [
                                '1' => \__('yes'),
                                '0' => \__('no'),
                            ],
                            'default' => '0',
                            'width'   => 33,
                        ],
                        'video-yt-color'    => [
                            'label'        => \__('color'),
                            'type'         => InputType::RADIO,
                            'inline'       => true,
                            'options'      => [
                                'white' => \__('white'),
                                'red'   => \__('red'),
                            ],
                            'default'      => 'white',
                            'width'        => 33,
                            'color-format' => '#',
                            'desc'         => \__('colorYtDesc'),
                        ],
                        'video-yt-playlist' => [
                            'label' => \__('playlist'),
                            'help'  => \__('playlistHelp'),
                        ],
                    ],
                    'vimeo'   => [
                        'video-vim-id'     => [
                            'label'    => \__('videoID'),
                            'default'  => '141374353',
                            'nonempty' => true,
                            'help'     => \__('videoIDHelpVimeo'),
                        ],
                        'video-vim-loop'   => [
                            'label'   => \__('repeatVideo'),
                            'type'    => InputType::RADIO,
                            'inline'  => true,
                            'options' => [
                                '1' => \__('yes'),
                                '0' => \__('no'),
                            ],
                            'default' => '0',
                            'width'   => 50,
                        ],
                        'video-vim-img'    => [
                            'label'   => \__('showImage'),
                            'type'    => InputType::RADIO,
                            'inline'  => true,
                            'options' => [
                                '1' => \__('yes'),
                                '0' => \__('no'),
                            ],
                            'default' => '0',
                            'width'   => 50,
                        ],
                        'video-vim-title'  => [
                            'label'   => \__('showTitle'),
                            'type'    => InputType::RADIO,
                            'inline'  => true,
                            'options' => [
                                '1' => \__('yes'),
                                '0' => \__('no'),
                            ],
                            'default' => '1',
                            'width'   => 50,
                        ],
                        'video-vim-byline' => [
                            'label'   => \__('showAuthorInformation'),
                            'type'    => InputType::RADIO,
                            'inline'  => true,
                            'options' => [
                                '1' => \__('yes'),
                                '0' => \__('no'),
                            ],
                            'default' => '0',
                            'width'   => 50,
                        ],
                        'video-vim-color'  => [
                            'label'   => \__('color'),
                            'type'    => InputType::COLOR,
                            'default' => '#ffffff',
                            'width'   => 50,
                        ],
                    ],
                    'local'   => [
                        'video-local-url'      => [
                            'label' => \__('videoURL'),
                            'type'  => InputType::VIDEO,
                            'width' => 50,
                        ],
                        'video-local-loop'     => [
                            'label' => \__('repeatVideo'),
                            'type'  => InputType::CHECKBOX,
                            'width' => 50,
                        ],
                        'video-local-autoplay' => [
                            'label' => \__('autoplayVideo'),
                            'type'  => InputType::CHECKBOX,
                            'width' => 33,
                        ],
                        'video-local-mute'     => [
                            'label' => \__('muteVideo'),
                            'type'  => InputType::CHECKBOX,
                            'width' => 33,
                        ],
                        'video-local-controls' => [
                            'label'   => \__('showControls'),
                            'type'    => InputType::CHECKBOX,
                            'width'   => 33,
                            'default' => '1',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getPropertyTabs(): array
    {
        return [
            \__('Styles')    => 'styles',
            \__('Animation') => 'animations',
        ];
    }
}