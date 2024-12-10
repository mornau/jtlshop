<?php

declare(strict_types=1);

namespace JTL\Mail;

use JTL\Settings\Option\Email;
use JTL\Settings\Settings;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Class SmtpTest
 * @package JTL\Mail
 */
class SmtpTest
{
    public function run(Settings $settings): bool
    {
        $smtp           = new SMTP();
        $smtp->do_debug = SMTP::DEBUG_CONNECTION;
        try {
            if (!$smtp->connect($settings->string(Email::SMTP_HOST), $settings->int(Email::SMTP_PORT))) {
                throw new Exception('Connect failed');
            }
            if (!$smtp->hello(\gethostname())) {
                throw new Exception('EHLO failed: ' . $smtp->getError()['error']);
            }
            $e = $smtp->getServerExtList();
            if (\is_array($e) && \array_key_exists('STARTTLS', $e)) {
                $tlsok = $smtp->startTLS();
                if (!$tlsok) {
                    throw new Exception('Failed to start encryption: ' . $smtp->getError()['error']);
                }
                if (!$smtp->hello(\gethostname())) {
                    throw new Exception('EHLO (2) failed: ' . $smtp->getError()['error']);
                }
                $e = $smtp->getServerExtList();
            } elseif ($settings->string(Email::SMTP_ENCRYPTION) === 'tls') {
                throw new Exception('TLS not supported');
            }
            if (!\is_array($e) || !\array_key_exists('AUTH', $e)) {
                throw new Exception('No authentication supported');
            }
            if (
                $smtp->authenticate($settings->string(Email::SMTP_USER), $settings->string(Email::SMTP_PASS))
            ) {
                echo 'Connected ok!';
            } else {
                throw new Exception('Authentication failed: ' . $smtp->getError()['error']);
            }
        } catch (Exception $e) {
            echo 'SMTP error: ' . $e->getMessage(), "\n";
        }

        return $smtp->quit();
    }
}
