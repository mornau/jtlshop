<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JsonException;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20191014113600
 */
class Migration20191018152300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Convert page IDs in topcpage to new json format';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $pages = $this->fetchAll('SELECT kPage, cPageId FROM topcpage');

        foreach ($pages as $page) {
            $idObj       = new stdClass();
            $fields      = \explode(';', $page->cPageId);
            $numfields   = count($fields);
            $first       = \explode(':', $fields[0]);
            $idObj->type = $first[0];
            $idObj->id   = $first[1];

            if ($idObj->type !== 'search' && $idObj->type !== 'other') {
                $idObj->id = (int)$idObj->id;
            } elseif ($idObj->type === 'search') {
                $idObj->id = \base64_decode($idObj->id);
            }

            for ($i = 1; $i < $numfields; $i++) {
                $field = \explode(':', $fields[$i]);
                $key   = $field[0];
                $value = $field[1];

                if ($key === 'lang' || $key === 'manufacturerFilter') {
                    $value = (int)$value;
                } elseif ($key === 'attribs') {
                    $value = \explode(',', $value);
                    $value = \array_map('\intval', $value);
                }

                $idObj->{$key} = $value;
            }

            $jsonId        = \json_encode($idObj);
            $page->cPageId = $jsonId;

            $this->execute("UPDATE topcpage SET cPageId = '" . $page->cPageId . "' WHERE kPage = " . $page->kPage);
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $pages = $this->fetchAll('SELECT cPageId, kPage FROM topcpage');

        foreach ($pages as $page) {
            try {
                /** @var array<mixed> $json */
                $json = \json_decode($page->cPageId, true, 512, \JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }
            $type = $json['type'];
            $id   = $json['id'];

            if ($type === 'search') {
                $oldPageId = $type . ':' . \base64_encode((string)$id);
            } else {
                $oldPageId = $type . ':' . $id;
            }

            foreach ($json as $key => $val) {
                if ($key === 'attribs') {
                    $oldPageId .= ';' . $key . ':' . \implode(',', $val);
                } elseif ($key !== 'type' && $key !== 'id') {
                    $oldPageId .= ';' . $key . ':' . $val;
                }
            }

            $page->cPageId = $oldPageId;
            $this->execute("UPDATE topcpage SET cPageId = '" . $page->cPageId . "' WHERE kPage = " . $page->kPage);
        }
    }
}
