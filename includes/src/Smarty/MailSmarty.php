<?php

declare(strict_types=1);

namespace JTL\Smarty;

use JTL\DB\DbInterface;
use JTL\Language\LanguageModel;
use Smarty;
use Smarty_Internal_Template;

/**
 * Class MailSmarty
 * @package JTL\Smarty
 */
class MailSmarty extends JTLSmarty
{
    public function __construct(protected DbInterface $db, string $context = ContextType::MAIL)
    {
        parent::__construct(true, $context);
        $this->setCaching(JTLSmarty::CACHING_OFF)
            ->setDebugging(false)
            ->registerResource('db', new SmartyResourceNiceDB($db, $context));
        $this->setCompileDir(\PFAD_ROOT . \PFAD_COMPILEDIR)
            ->setTemplateDir(\PFAD_ROOT . \PFAD_EMAILTEMPLATES);
        $this->registerPlugins();
        if ($context === ContextType::MAIL && \MAILTEMPLATE_USE_SECURITY) {
            $this->activateBackendSecurityMode();
        } elseif ($context === ContextType::NEWSLETTER && \NEWSLETTER_USE_SECURITY) {
            $this->activateBackendSecurityMode();
        }
    }

    protected function registerPlugins(): void
    {
        parent::registerPlugins();
        $this->registerPlugin(Smarty::PLUGIN_FUNCTION, 'includeMailTemplate', $this->includeMailTemplate(...))
            ->registerPlugin(Smarty::PLUGIN_MODIFIER, 'maskPrivate', $this->maskPrivate(...));
    }

    protected function initTemplate(): ?string
    {
        return null;
    }

    /**
     * @param string[]                 $params
     * @param Smarty_Internal_Template $smarty
     * @return string
     */
    public function includeMailTemplate(array $params, Smarty_Internal_Template $smarty): string
    {
        if (!isset($params['template'], $params['type']) || $smarty->getTemplateVars('int_lang') === null) {
            return '';
        }
        $tpl = $this->db->select(
            'temailvorlage',
            'cDateiname',
            $params['template']
        );
        if ($tpl !== null && isset($tpl->kEmailvorlage) && $tpl->kEmailvorlage > 0) {
            $tpl->kEmailvorlage = (int)$tpl->kEmailvorlage;
            /** @var LanguageModel $lang */
            $lang = $smarty->getTemplateVars('int_lang');
            $row  = $params['type'] === 'html' ? 'cContentHtml' : 'cContentText';
            $res  = $this->db->getSingleObject(
                'SELECT ' . $row . ' AS content
                    FROM temailvorlagesprache
                    WHERE kSprache = :lid
                 AND kEmailvorlage = :tid',
                ['lid' => $lang->getId(), 'tid' => $tpl->kEmailvorlage]
            );
            if (isset($res->content)) {
                return $smarty->fetch('db:' . $params['type'] . '_' . $tpl->kEmailvorlage . '_' . $lang->kSprache);
            }
        }

        return '';
    }

    public function maskPrivate(string $str, int $pre = 0, int $post = 4, string $mask = '****'): string
    {
        if (\mb_strlen($str) <= $pre + $post) {
            return $str;
        }

        return \mb_substr($str, 0, $pre) . $mask . \mb_substr($str, -$post);
    }
}
