<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

use DevPhanuel\Models\Enums\Boolean;
use ErrorException;
use ValueError;

class AccountEntity
{
    private string $userUuid;
    private ?string $email = null;
    private ?string $password = null;
    private Boolean $isFunded = Boolean::TRUE;
    private ?float $totalFunding = 0;
    private ?float $totalEarning = 0;
    private ?float $earningBalance = 0;
    private ?float $remittedPayment = 0;
    private ?string $guarantorName = null;
    private ?string $guarantorPhone = null;
    private ?string $bankName = null;
    private ?string $acctNumber = null;
    private ?string $acctName = null;
    private Boolean $isDeactivated = Boolean::FALSE;
    private string $createdAt;
    private string $updatedAt;

    /**
     * Get the value of userUuid
     */
    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    /**
     * Set the value of userUuid
     *
     * @return  self
     */
    public function setUserUuid(string $userUuid): self
    {
        $this->userUuid = $userUuid;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of isFunded
     */
    public function getIsFunded(): Boolean
    {
        return $this->isFunded;
    }

    /**
     * Set the value of isFunded
     *
     * @return  self
     */
    public function setIsFunded(string $isFunded): self
    {
        try {
            $this->isFunded = Boolean::from($isFunded);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Boolean Type');
        }
    }

    /**
     * Get the value of totalFunding
     */
    public function getTotalFunding(): ?float
    {
        return $this->totalFunding;
    }

    /**
     * Set the value of totalFunding
     *
     * @return  self
     */
    public function setTotalFunding(string|float $totalFunding): self
    {
        $this->totalFunding = (float) $totalFunding;

        return $this;
    }

    /**
     * Get the value of totalEarning
     */
    public function getTotalEarning(): ?float
    {
        return $this->totalEarning;
    }

    /**
     * Set the value of totalEarning
     *
     * @return  self
     */
    public function setTotalEarning(string|float $totalEarning): self
    {
        $this->totalEarning = (float) $totalEarning;

        return $this;
    }

    /**
     * Get the value of earningBalance
     */
    public function getEarningBalance(): ?float
    {
        return $this->earningBalance;
    }

    /**
     * Set the value of earningBalance
     *
     * @return  self
     */
    public function setEarningBalance(string|float $earningBalance): self
    {
        $this->earningBalance = (float) $earningBalance;

        return $this;
    }

    /**
     * Get the value of remittedPayment
     */
    public function getRemittedPayment(): ?float
    {
        return $this->remittedPayment;
    }

    /**
     * Set the value of remittedPayment
     *
     * @return  self
     */
    public function setRemittedPayment(string|float $remittedPayment): self
    {
        $this->remittedPayment = (float) $remittedPayment;

        return $this;
    }

    /**
     * Get the value of guarantorName
     */
    public function getGuarantorName(): ?string
    {
        return $this->guarantorName;
    }

    /**
     * Set the value of guarantorName
     *
     * @return  self
     */
    public function setGuarantorName(string $guarantorName): self
    {
        $this->guarantorName = $guarantorName;

        return $this;
    }

    /**
     * Get the value of guarantorPhone
     */
    public function getGuarantorPhone(): ?string
    {
        return $this->guarantorPhone;
    }

    /**
     * Set the value of guarantorPhone
     *
     * @return  self
     */
    public function setGuarantorPhone(string $guarantorPhone): self
    {
        $this->guarantorPhone = $guarantorPhone;

        return $this;
    }

    /**
     * Get the value of bankName
     */
    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    /**
     * Set the value of bankName
     *
     * @return  self
     */
    public function setBankName(string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get the value of acctNumber
     */
    public function getAcctNumber(): ?string
    {
        return $this->acctNumber;
    }

    /**
     * Set the value of acctNumber
     *
     * @return  self
     */
    public function setAcctNumber(string $acctNumber): self
    {
        $this->acctNumber = $acctNumber;

        return $this;
    }

    /**
     * Get the value of acctName
     */
    public function getAcctName(): ?string
    {
        return $this->acctName;
    }

    /**
     * Set the value of acctName
     *
     * @return  self
     */
    public function setAcctName(string $acctName): self
    {
        $this->acctName = $acctName;

        return $this;
    }

    /**
     * Get the value of isDeactivated
     */
    public function getIsDeactivated(): Boolean
    {
        return $this->isDeactivated;
    }

    /**
     * Set the value of isDeactivated
     *
     * @return  self
     */
    public function setIsDeactivated(string $isDeactivated): self
    {
        try {
            $this->isDeactivated = Boolean::from($isDeactivated);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Boolean Type');
        }
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}