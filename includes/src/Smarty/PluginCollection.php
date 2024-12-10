<?php

declare(strict_types=1);

namespace JTL\Smarty;

use JTL\Backend\Wizard\QuestionType;
use JTL\Boxes\Type as BoxType;
use JTL\Cart\CartItem;
use JTL\Catalog\Product\Preise;
use JTL\Cron\Type;
use JTL\Customer\CustomerField;
use JTL\Customer\CustomerGroup;
use JTL\DB\Migration\Check;
use JTL\Filter\Visibility;
use JTL\Helpers\Text;
use JTL\Language\LanguageHelper;
use JTL\License\Struct\Release;
use JTL\Mail\Template\Model;
use JTL\Media\Image;
use JTL\Media\Video;
use JTL\Plugin\Admin\InputType;
use JTL\Plugin\Data\Config;
use JTL\Plugin\InstallCode;
use JTL\Plugin\State;
use JTL\Redirect;
use JTL\Router\Route;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Shopsetting;
use Smarty;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PluginCollection
 * @package JTL\Smarty
 */
class PluginCollection
{
    /**
     * @var array<string, string[]>
     */
    private array $config;

    public function __construct(private readonly JTLSmarty $smarty, private readonly LanguageHelper $lang)
    {
        $this->config = $this->smarty->config;
    }

    public function registerPlugins(): void
    {
        $this->smarty->registerPlugin(Smarty::PLUGIN_FUNCTION, 'lang', $this->translate(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, 'replace_delim', $this->replaceDelimiters(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, 'count_characters', $this->countCharacters(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, 'string_format', $this->stringFormat(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, 'string_date_format', $this->dateFormat(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, '__', $this->gettextTranslate(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIERCOMPILER, 'default', $this->compilerModifierDefault(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, 'truncate', $this->truncate(...))
            ->registerPlugin(Smarty::PLUGIN_BLOCK, 'inline_script', $this->inlineScript(...));
    }

    /**
     * @param array<int, string> $params
     * @return string
     */
    private function compilerModifierDefault(array $params): string
    {
        $output = $params[0];
        if (!isset($params[1])) {
            $params[1] = "''";
        }
        \array_shift($params);
        foreach ($params as $param) {
            $output = '(($tmp = ' . $output . ' ?? null)===null||$tmp===\'\' ? ' . $param . ' : $tmp)';
        }

        return $output;
    }

    private function replaceDelimiters(string $string): string
    {
        $replace = $this->config['global']['global_dezimaltrennzeichen_sonstigeangaben'];
        if ($replace !== ',' && $replace !== '.') {
            $replace = ',';
        }

        return \str_replace('.', $replace, $string);
    }

    private function truncate(
        string $string,
        int $length = 80,
        string $etc = '...',
        bool $break = false,
        bool $middle = false
    ): string {
        if ($length === 0) {
            return '';
        }
        if (\mb_strlen($string) <= $length) {
            return $string;
        }
        $length -= \min($length, \mb_strlen($etc));
        if (!$break && !$middle) {
            $string = \preg_replace(
                '/\s+?(\S+)?$/',
                '',
                \mb_substr($string, 0, $length + 1)
            ) ?? '';
        }

        return !$middle
            ? \mb_substr($string, 0, $length) . $etc
            : \mb_substr($string, 0, $length / 2) . $etc . \mb_substr($string, -$length / 2);
    }

    /**
     * translation
     *
     * @param array                     $params
     * @param \Smarty_Internal_Template $template
     * @return void|string
     */
    private function translate(array $params, \Smarty_Internal_Template $template)
    {
        $res     = '';
        $section = $params['section'] ?? 'global';
        $key     = $params['key'] ?? '';
        if ($key !== '') {
            $res = $this->lang->get($key, $section);
            // Für vsprintf ein String der :: exploded wird
            if (isset($params['printf'])) {
                $res = \vsprintf($res, \explode(':::', (string)$params['printf']));
            }
        }
        if (\SMARTY_SHOW_LANGKEY) {
            $res = '#' . $section . '.' . $key . '#';
        }
        if (isset($params['assign'])) {
            $template->assign($params['assign'], $res);
        } else {
            return !empty($params['addslashes']) ? \addslashes($res) : $res;
        }
    }

    private function countCharacters(?string $text): int
    {
        return \mb_strlen($text ?? '');
    }

    private function stringFormat(string $string, string $format): string
    {
        return \sprintf($format, $string);
    }

    private function dateFormat(string $string, string $format = '%b %e, %Y', string $default_date = ''): string
    {
        if ($string !== '') {
            $timestamp = \smarty_make_timestamp($string);
        } elseif ($default_date !== '') {
            $timestamp = \smarty_make_timestamp($default_date);
        } else {
            return $string;
        }
        if (\DIRECTORY_SEPARATOR === '\\') {
            $_win_from = ['%D', '%h', '%n', '%r', '%R', '%t', '%T'];
            $_win_to   = ['%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S'];
            if (\str_contains($format, '%e')) {
                $_win_from[] = '%e';
                $_win_to[]   = \sprintf('%\' 2d', \date('j', $timestamp));
            }
            if (\str_contains($format, '%l')) {
                $_win_from[] = '%l';
                $_win_to[]   = \sprintf('%\' 2d', \date('h', $timestamp));
            }
            $format = \str_replace($_win_from, $_win_to, $format);
        }

        return \PHP81_BC\strftime($format, $timestamp);
    }

    /**
     * @param string[] $params
     * @param ?string  $content
     * @return string
     */
    private function inlineScript(array $params, ?string $content): string
    {
        if ($content === null || empty(\trim($content))) {
            return '';
        }
        $content = \preg_replace('/^<script(.*?)>/', '', \trim($content)) ?? '';
        $content = \preg_replace('/<\/script>$/', '', $content) ?? '';

        return '<script defer src="data:text/javascript;base64,' . \base64_encode($content) . '"></script>';
    }

    private function gettextTranslate(string $original, ...$args): string
    {
        return \__($original, $args);
    }

    public function registerPhpFunctions(): void
    {
        // modifiers defined by smarty that are also callables
        $specialCases = [
            'join',
            'date_format',
            'round',
            'strip',
            'strlen',
            'substr',
            'wordwrap',
            'split',
            'number_format'
        ];
        foreach ($this->smarty->getSecurePhpFunctions() as $function) {
            if (!\is_callable($function) || \in_array($function, $specialCases, true)) {
                continue;
            }
            $this->smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, $function, '\\' . $function);
        }
        $moreFunctions = [
            'class_exists',
            'function_exists',
            'method_exists',
            'unserialize',
            'serialize',
            'dump',
            'urldecode',
            'dd',
            'http_build_query',
            'file_get_contents',
            'file_exists',
            'Functional\true',
            'Functional\false',
            'Functional\map',
            'Functional\group',
            'Functional\select',
            'Functional\some',
            'Functional\first',
            'Functional\last',
            'Functional\pluck',
            'n__'
        ];
        foreach ($moreFunctions as $function) {
            if (!\is_callable($function)) {
                continue;
            }
            $this->smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, $function, '\\' . $function);
        }
    }

    public function registerShopClasses(): void
    {
        $classNames = [
            Config::class,
            InputType::class,
            Image::class,
            Shop::class,
            Shopsetting::class,
            LanguageHelper::class,
            Frontend::class,
            Visibility::class,
            Route::class,
            Preise::class,
            CustomerField::class,
            Type::class,
            CartItem::class,
            Text::class,
            CustomerGroup::class,
            InstallCode::class,
            State::class,
            Release::class,
            Model::class,
            Redirect::class,
            AbstractTest::class,
            Check::class,
            BoxType::class,
            QuestionType::class,
            Video::class,
        ];
        foreach ($classNames as $className) {
            $this->smarty->registerClass($className, $className);
            $this->smarty->registerClass('\\' . $className, $className);
        }
    }
}
