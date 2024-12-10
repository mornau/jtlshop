<?php

declare(strict_types=1);

namespace JTL\Smarty;

use JTL\DB\DbInterface;

/**
 * Class ExportSmarty
 * @package JTL\Smarty
 */
final class ExportSmarty extends JTLSmarty
{
    public function __construct(DbInterface $db)
    {
        parent::__construct(true, ContextType::EXPORT);
        $this->setCaching(JTLSmarty::CACHING_OFF)
            ->setTemplateDir(\PFAD_TEMPLATES)
            ->setCompileDir(\PFAD_ROOT . \PFAD_ADMIN . \PFAD_COMPILEDIR)
            ->registerResource('db', new SmartyResourceNiceDB($db, ContextType::EXPORT));
        $this->registerPlugins();
        if (\EXPORTFORMAT_USE_SECURITY) {
            $this->activateBackendSecurityMode();
        }
    }

    protected function initTemplate(): ?string
    {
        return null;
    }
}
