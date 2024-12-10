<?php

declare(strict_types=1);

namespace JTL\TwoFA;

use JTL\DB\DbInterface;
use JTL\Shop;
use qrcodegenerator\QRCode\Output\QRString;
use qrcodegenerator\QRCode\QRCode;
use stdClass;

/**
 * Class TwoFA
 * @package JTL\TwoFA
 */
abstract class TwoFA
{
    private string $shopName = '';

    public function __construct(protected DbInterface $db, protected UserData $userData)
    {
    }

    public function is2FAauth(): bool
    {
        return $this->userData->use2FA();
    }

    public function is2FAauthSecretExist(): bool
    {
        return $this->userData->getSecret() !== '';
    }

    public function createNewSecret(): self
    {
        $this->userData->setSecret((new GoogleAuthenticator())->createSecret());

        return $this;
    }

    public function getSecret(): string
    {
        return $this->userData->getSecret();
    }

    public function isCodeValid(string $code): bool
    {
        // codes with a length over 6 chars are emergency codes
        if (\mb_strlen($code) > 6) {
            return (new TwoFAEmergency($this->db))->isValidEmergencyCode($this->userData, $code);
        }
        return (new GoogleAuthenticator())->verifyCode($this->userData->getSecret(), $code);
    }

    public function getQRcode(): string
    {
        if ($this->userData->getSecret() === '') {
            return '';
        }
        $totpUrl = \rawurlencode('JTL-Shop ' . $this->userData->getName() . '@' . $this->getShopName());
        // by the QR code there are 63 bytes allowed for this URL appendix
        // so we shorten that string (and we take care about the hex character replacements!)
        $overflow = \mb_strlen($totpUrl) - 63;
        if (0 < $overflow) {
            for ($i = 0; $i < $overflow; $i++) {
                if ($totpUrl[\mb_strlen($totpUrl) - 3] === '%') {
                    $totpUrl  = \mb_substr($totpUrl, 0, -3); // shorten by 3 byte..
                    $overflow -= 2;                         // ..and correct the counter (here nOverhang)
                } else {
                    $totpUrl = \mb_substr($totpUrl, 0, -1);  // shorten by 1 byte
                }
            }
        }
        $qrCode = new QRCode(
            'otpauth://totp/' . $totpUrl
            . '?secret=' . $this->userData->getSecret()
            . '&issuer=JTL-Software',
            new QRString()
        );

        return $qrCode->output();
    }

    public function getUserData(): UserData
    {
        return $this->userData;
    }

    public function getShopName(): string
    {
        if ($this->shopName === '') {
            $this->shopName = Shop::getSettingValue(\CONF_GLOBAL, 'global_shopname');
        }

        return $this->shopName;
    }

    public function __toString(): string
    {
        return \print_r($this->userData, true);
    }

    abstract public static function getNewTwoFA(string $userName): string;

    abstract public static function genTwoFAEmergencyCodes(string $userName): stdClass;
}