<?php

declare(strict_types=1);

namespace JTL\Mail\Validator;

use JTL\DB\DbInterface;
use JTL\Mail\Mail\MailInterface;
use JTL\Mail\Template\Model;
use stdClass;

/**
 * Class MailValidator
 * @package JTL\Mail\Validator
 */
final class MailValidator implements ValidatorInterface
{
    /**
     * MailValidator constructor.
     * @param DbInterface $db
     * @param array       $config
     */
    public function __construct(private readonly DbInterface $db, private readonly array $config)
    {
    }

    /**
     * @param MailInterface $mail
     * @return bool
     */
    public function validate(MailInterface $mail): bool
    {
        $model     = $mail->getTemplate()?->getModel();
        $activated = $model === null || $this->isTemplateActivated($model);

        return $activated && $this->checkBody($mail) === true && !$this->isBlacklisted($mail->getToMail());
    }

    /**
     * @param MailInterface $mail
     * @return bool
     */
    public function checkBody(MailInterface $mail): bool
    {
        return \mb_strlen($mail->getBodyHTML()) > 0 || \mb_strlen($mail->getBodyText()) > 0;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isBlacklisted(string $email): bool
    {
        if ($this->config['emailblacklist']['blacklist_benutzen'] !== 'Y') {
            return false;
        }
        $blackList = $this->db->select('temailblacklist', 'cEmail', $email);
        if ($blackList === null || empty($blackList->cEmail)) {
            return false;
        }
        $block                = new stdClass();
        $block->cEmail        = $blackList->cEmail;
        $block->dLetzterBlock = 'NOW()';

        $this->db->insert('temailblacklistblock', $block);

        return true;
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function isTemplateActivated(Model $model): bool
    {
        return $model->getActive() === true;
    }
}
