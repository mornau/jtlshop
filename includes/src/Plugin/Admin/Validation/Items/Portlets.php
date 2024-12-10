<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin\Validation\Items;

use JTL\Helpers\GeneralObject;
use JTL\Plugin\InstallCode;
use JTL\Shop;

/**
 * Class Portlets
 * @package JTL\Plugin\Admin\Validation\Items
 */
final class Portlets extends AbstractItem
{
    /**
     * @inheritdoc
     */
    public function validate(): int
    {
        $node = $this->getInstallNode();
        $dir  = $this->getDir();
        if (!GeneralObject::isCountable('Portlets', $node)) {
            return InstallCode::OK;
        }
        $node = $node['Portlets'][0]['Portlet'] ?? null;
        if (!GeneralObject::hasCount($node)) {
            return InstallCode::MISSING_PORTLETS;
        }
        $db                 = Shop::Container()->getDB();
        $existingClassNames = $db->getCollection(
            'SELECT DISTINCT cClass
                FROM topcportlet
                LEFT JOIN tplugin ON tplugin.cPluginID = :pid
                WHERE topcportlet.kPlugin != COALESCE(tplugin.kPlugin, 0) OR topcportlet.kPlugin = 0;',
            ['pid' => $this->pluginID]
        );
        foreach ($node as $i => $portlet) {
            if (!\is_array($portlet)) {
                continue;
            }
            $i       = (string)$i;
            $portlet = $this->sanitizePortlet($portlet);
            \preg_match('/\d+\sattr/', $i, $hits1);
            \preg_match('/\d+/', $i, $hits2);
            if (\mb_strlen($hits2[0]) === \mb_strlen($i)) {
                \preg_match('/[\w\/\-() ]+/u', $portlet['Title'], $hits1);
                $len = \mb_strlen($portlet['Title']);
                if ($len === 0 || \mb_strlen($hits1[0]) !== $len) {
                    return InstallCode::INVALID_PORTLET_TITLE;
                }
                \preg_match('/[a-zA-Z\d\/_\-.]+/', $portlet['Class'], $hits1);
                $len = \mb_strlen($portlet['Class']);
                if ($len === 0 || \mb_strlen($hits1[0]) === $len) {
                    if (
                        !\file_exists(
                            $dir . \PFAD_PLUGIN_PORTLETS . $portlet['Class'] . '/' . $portlet['Class'] . '.php'
                        )
                    ) {
                        return InstallCode::INVALID_PORTLET_CLASS_FILE;
                    }
                    if ($existingClassNames->firstWhere('cClass', $portlet['Class'])) {
                        return InstallCode::PORTLET_CLASS_NAME_EXISTS;
                    }
                } else {
                    return InstallCode::INVALID_PORTLET_CLASS;
                }
                \preg_match('/[\w\/\-() ]+/u', $portlet['Group'], $hits1);
                $len = \mb_strlen($portlet['Group']);
                if ($len === 0 || \mb_strlen($hits1[0]) !== $len) {
                    return InstallCode::INVALID_PORTLET_GROUP;
                }
                \preg_match('/[0-1]/', $portlet['Active'], $hits1);
                if (\mb_strlen($hits1[0]) !== \mb_strlen($portlet['Active'])) {
                    return InstallCode::INVALID_PORTLET_ACTIVE;
                }
            }
        }

        return InstallCode::OK;
    }

    /**
     * @param array{Title?: string, Class?: string, Group?: string, Active?: string} $portlet
     * @return array{Title: string, Class: string, Group: string, Active: string}
     */
    private function sanitizePortlet(array $portlet): array
    {
        $portlet['Title']  = $portlet['Title'] ?? '';
        $portlet['Class']  = $portlet['Class'] ?? '';
        $portlet['Group']  = $portlet['Group'] ?? '';
        $portlet['Active'] = $portlet['Active'] ?? '1';

        return $portlet;
    }
}
