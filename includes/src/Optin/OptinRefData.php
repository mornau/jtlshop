<?php

declare(strict_types=1);

namespace JTL\Optin;

use JTL\GeneralDataProtection\IpAnonymizer;

/**
 * Class OptinRefData
 * @package JTL\Optin
 */
class OptinRefData
{
    /**
     * @var class-string<OptinInterface>
     */
    private string $optinClass;

    /**
     * @var int
     */
    private int $languageID;

    /**
     * @var int|null
     */
    private ?int $customerID = null;

    /**
     * @var int|null
     */
    private ?int $customerGroupID = null;

    /**
     * @var string
     */
    private string $salutation = '';

    /**
     * @var string
     */
    private string $firstName = '';

    /**
     * @var string
     */
    private string $lastName = '';

    /**
     * @var string
     */
    private string $email = '';

    /**
     * @var string
     */
    private string $realIP = '';

    /**
     * @var int|null
     */
    private ?int $productID = null;

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'optinClass'      => $this->optinClass,
            'languageID'      => $this->languageID,
            'customerID'      => $this->customerID,
            'salutation'      => $this->salutation,
            'firstName'       => $this->firstName,
            'lastName'        => $this->lastName,
            'email'           => $this->email,
            'realIP'          => $this->realIP,
            'productID'       => $this->productID,
            'customerGroupID' => $this->customerGroupID
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        // items pre 5.3.0 will not have a serialized customer group id
        if ($this->customerGroupID === null) {
            $this->customerGroupID = 0;
        }
    }

    /**
     * @param class-string<OptinInterface> $optinClass
     * @return OptinRefData
     */
    public function setOptinClass(string $optinClass): self
    {
        $this->optinClass = $optinClass;

        return $this;
    }

    /**
     * @param int $languageID
     * @return OptinRefData
     */
    public function setLanguageID(int $languageID): self
    {
        $this->languageID = $languageID;

        return $this;
    }

    /**
     * @param int $customerID
     * @return OptinRefData
     */
    public function setCustomerID(int $customerID): self
    {
        $this->customerID = $customerID;

        return $this;
    }

    /**
     * @param string $salutation
     * @return OptinRefData
     */
    public function setSalutation(string $salutation): self
    {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * @param string $firstName
     * @return OptinRefData
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @param string $lastName
     * @return OptinRefData
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @param string $email
     * @return OptinRefData
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $realIP
     * @return OptinRefData
     */
    public function setRealIP(string $realIP): self
    {
        $this->realIP = $realIP;

        return $this;
    }

    /**
     * @param int $productId
     * @return OptinRefData
     */
    public function setProductId(int $productId): self
    {
        $this->productID = $productId;

        return $this;
    }

    /**
     * @return class-string<OptinInterface>
     */
    public function getOptinClass(): string
    {
        return $this->optinClass;
    }

    /**
     * @return int
     */
    public function getLanguageID(): int
    {
        return $this->languageID;
    }

    /**
     * @return int
     */
    public function getCustomerID(): int
    {
        return $this->customerID ?? 0;
    }

    /**
     * @return string
     */
    public function getSalutation(): string
    {
        return $this->salutation;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getRealIP(): string
    {
        return $this->realIP;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productID ?? 0;
    }

    /**
     * @return int|null
     */
    public function getCustomerGroupID(): ?int
    {
        return $this->customerGroupID;
    }

    /**
     * @param int|null $customerGroupID
     * @return $this
     */
    public function setCustomerGroupID(?int $customerGroupID): self
    {
        $this->customerGroupID = $customerGroupID;

        return $this;
    }

    /**
     * @return $this
     */
    public function anonymized(): self
    {
        $this->setEmail('anonym');
        $this->setRealIP((new IpAnonymizer($this->getRealIP()))->anonymize());
        $this->setFirstName('anonym');
        $this->setLastName('anonym');

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \serialize([
            $this->optinClass,
            $this->languageID,
            $this->customerID,
            $this->salutation,
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->realIP,
            $this->productID,
            $this->customerGroupID
        ]);
    }
}
